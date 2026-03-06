<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    private const MAX_TRANSACTION_AMOUNT = 50_000_000;

    public function balance(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => [
                'balance'  => $user->balance,
                'username' => $user->username,
                'email'    => $user->email,
            ],
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $identifier = $request->query('identifier');

        if (!$identifier) {
            return response()->json([
                'message' => 'Identifier wajib diisi.',
            ], 422);
        }

        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Pengguna tidak ditemukan.',
            ], 404);
        }

        if ($user->id === $request->user()->id) {
            return response()->json([
                'message' => 'Tidak dapat transfer ke diri sendiri.',
            ], 422);
        }

        return response()->json([
            'data' => [
                'name'     => $user->name,
                'username' => $user->username,
            ],
        ]);
    }

    public function topup(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => ['required'],
        ]);

        $amount = $this->validateAmount($request->input('amount'));
        $user   = $request->user();

        DB::transaction(function () use ($user, $amount) {
            $balanceBefore = $user->balance;

            $user->increment('balance', $amount);

            Transaction::create([
                'user_id'        => $user->id,
                'sender_id'      => null,
                'receiver_id'    => $user->id,
                'type'           => 'topup',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceBefore + $amount,
                'description'    => 'Top up saldo',
                'reference_id'   => (string) Str::uuid(),
            ]);
        });

        $user->refresh();

        return response()->json([
            'message' => 'Top up berhasil.',
            'data'    => [
                'balance'      => $user->balance,
                'amount_added' => $amount,
            ],
        ]);
    }

    public function transfer(Request $request): JsonResponse
    {
        $request->validate([
            'amount'     => ['required'],
            'identifier' => ['required', 'string'],
        ], [
            'identifier.required' => 'Email atau nomor HP penerima wajib diisi.',
        ]);

        $amount = $this->validateAmount($request->input('amount'));
        $sender = $request->user();

        $receiver = User::where('email', $request->identifier)
            ->orWhere('phone', $request->identifier)
            ->first();

        if (!$receiver) {
            return response()->json([
                'message' => 'Penerima tidak ditemukan.',
                'errors'  => [
                    'identifier' => ['User dengan email/nomor HP tersebut tidak ditemukan.'],
                ],
            ], 422);
        }

        if ($receiver->id === $sender->id) {
            return response()->json([
                'message' => 'Tidak dapat transfer ke diri sendiri.',
                'errors'  => [
                    'identifier' => ['Tidak dapat melakukan transfer ke akun sendiri.'],
                ],
            ], 422);
        }

        if ($sender->balance < $amount) {
            return response()->json([
                'message' => 'Saldo tidak cukup.',
                'errors'  => [
                    'amount' => ['Saldo Anda tidak mencukupi untuk melakukan transfer ini.'],
                ],
            ], 422);
        }

        $referenceId = (string) Str::uuid();

        DB::transaction(function () use ($sender, $receiver, $amount, $referenceId) {
            $senderLocked = User::lockForUpdate()->find($sender->id);

            if ($senderLocked->balance < $amount) {
                throw ValidationException::withMessages([
                    'amount' => ['Saldo Anda tidak mencukupi.'],
                ]);
            }

            $senderBalanceBefore = $senderLocked->balance;
            $senderLocked->decrement('balance', $amount);

            $receiverLocked        = User::lockForUpdate()->find($receiver->id);
            $receiverBalanceBefore = $receiverLocked->balance;
            $receiverLocked->increment('balance', $amount);

            Transaction::create([
                'user_id'        => $senderLocked->id,
                'sender_id'      => $senderLocked->id,
                'receiver_id'    => $receiverLocked->id,
                'type'           => 'transfer_out',
                'amount'         => $amount,
                'balance_before' => $senderBalanceBefore,
                'balance_after'  => $senderBalanceBefore - $amount,
                'description'    => "Transfer ke {$receiverLocked->username}",
                'reference_id'   => $referenceId . '-out',
            ]);

            Transaction::create([
                'user_id'        => $receiverLocked->id,
                'sender_id'      => $senderLocked->id,
                'receiver_id'    => $receiverLocked->id,
                'type'           => 'transfer_in',
                'amount'         => $amount,
                'balance_before' => $receiverBalanceBefore,
                'balance_after'  => $receiverBalanceBefore + $amount,
                'description'    => "Transfer dari {$senderLocked->username}",
                'reference_id'   => $referenceId . '-in',
            ]);
        });

        $sender->refresh();

        return response()->json([
            'message' => 'Transfer berhasil.',
            'data'    => [
                'balance'        => $sender->balance,
                'amount_sent'    => $amount,
                'receiver_email' => $receiver->email,
                'receiver_name'  => $receiver->name,
                'reference_id'   => $referenceId,
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->with(['sender:id,name,username', 'receiver:id,name,username'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $data = $transactions->getCollection()->map(fn ($trx) => [
            'id'             => $trx->id,
            'type'           => $trx->type,
            'amount'         => $trx->amount,
            'balance_before' => $trx->balance_before,
            'balance_after'  => $trx->balance_after,
            'description'    => $trx->description,
            'reference_id'   => $trx->reference_id,
            'sender'         => $trx->sender   ? ['name' => $trx->sender->name,   'username' => $trx->sender->username]   : null,
            'receiver'       => $trx->receiver ? ['name' => $trx->receiver->name, 'username' => $trx->receiver->username] : null,
            'created_at'     => $trx->created_at->toISOString(),
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'last_page'    => $transactions->lastPage(),
                'per_page'     => $transactions->perPage(),
                'total'        => $transactions->total(),
            ],
        ]);
    }

    private function validateAmount(mixed $raw): int
    {
        if ($raw === null || $raw === '') {
            $this->throwAmountError('Nominal tidak boleh kosong.');
        }

        $str = (string) $raw;

        if (preg_match('/[a-zA-Z]/', $str) || preg_match('/[^0-9\-]/', $str)) {
            $this->throwAmountError('Nominal harus berupa angka.');
        }

        if (str_contains($str, '.')) {
            $this->throwAmountError('Nominal harus berupa angka bulat.');
        }

        $amount = (int) $str;

        if ($amount < 0)  $this->throwAmountError('Nominal tidak boleh angka negatif.');
        if ($amount === 0) $this->throwAmountError('Nominal tidak boleh kosong.');

        if ($amount > self::MAX_TRANSACTION_AMOUNT) {
            $this->throwAmountError('Nominal melebihi batas maksimum transaksi.');
        }

        return $amount;
    }

    private function throwAmountError(string $message): never
    {
        throw ValidationException::withMessages([
            'amount' => [$message],
        ]);
    }
}