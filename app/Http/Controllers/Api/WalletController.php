<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    private const MAX_TRANSACTION_AMOUNT = 50_000_000;

    // ── GET /wallet ───────────────────────────────────────────────
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

    // ── GET /user/lookup ──────────────────────────────────────────
    public function lookup(Request $request): JsonResponse
    {
        $identifier = $request->query('identifier');
        if (!$identifier) {
            return response()->json(['message' => 'Identifier wajib diisi.'], 422);
        }

        $user = User::where('email', $identifier)->orWhere('phone', $identifier)->first();
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }
        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Tidak dapat transfer ke diri sendiri.'], 422);
        }

        return response()->json(['data' => ['name' => $user->name, 'username' => $user->username]]);
    }

    // ── POST /topup ───────────────────────────────────────────────
    public function topup(Request $request): JsonResponse
    {
        if ($request->user()->role === 'admin') {
            return response()->json(['message' => 'Admin tidak dapat melakukan top up.'], 403);
        }

        $request->validate([
            'amount'         => ['required'],
            'payment_method' => ['required', 'in:qris,mbanking,va,ewallet'],
        ]);

        $amount = $this->validateAmount($request->input('amount'));
        $user   = $request->user();

        $transaction = Transaction::create([
            'user_id'        => $user->id,
            'sender_id'      => null,
            'receiver_id'    => $user->id,
            'type'           => 'topup',
            'status'         => 'pending',
            'amount'         => $amount,
            'balance_before' => $user->balance,
            'balance_after'  => $user->balance,
            'description'    => 'Top Up saldo - menunggu konfirmasi',
            'reference_id'   => (string) Str::uuid(),
        ]);

        return response()->json([
            'message' => 'Permintaan top up berhasil dikirim, menunggu konfirmasi admin.',
            'data'    => $transaction,
        ], 201);
    }

    // ── POST /transfer ────────────────────────────────────────────
    public function transfer(Request $request): JsonResponse
    {
        if ($request->user()->role === 'admin') {
            return response()->json(['message' => 'Admin tidak dapat melakukan transfer.'], 403);
        }

        $request->validate([
            'amount'     => ['required'],
            'identifier' => ['required', 'string'],
        ], ['identifier.required' => 'Email atau nomor HP penerima wajib diisi.']);

        $amount   = $this->validateAmount($request->input('amount'));
        $sender   = $request->user();
        $receiver = User::where('email', $request->identifier)->orWhere('phone', $request->identifier)->first();

        if (!$receiver) {
            return response()->json(['message' => 'Penerima tidak ditemukan.', 'errors' => ['identifier' => ['User dengan email/nomor HP tersebut tidak ditemukan.']]], 422);
        }
        if ($receiver->id === $sender->id) {
            return response()->json(['message' => 'Tidak dapat transfer ke diri sendiri.', 'errors' => ['identifier' => ['Tidak dapat melakukan transfer ke akun sendiri.']]], 422);
        }
        if ($sender->balance < $amount) {
            return response()->json(['message' => 'Saldo tidak cukup.', 'errors' => ['amount' => ['Saldo Anda tidak mencukupi untuk melakukan transfer ini.']]], 422);
        }

        $referenceId = (string) Str::uuid();

        DB::transaction(function () use ($sender, $receiver, $amount, $referenceId) {
            $senderLocked = User::lockForUpdate()->find($sender->id);
            if ($senderLocked->balance < $amount) {
                throw ValidationException::withMessages(['amount' => ['Saldo Anda tidak mencukupi.']]);
            }

            $senderBalanceBefore   = $senderLocked->balance;
            $senderLocked->decrement('balance', $amount);
            $receiverLocked        = User::lockForUpdate()->find($receiver->id);
            $receiverBalanceBefore = $receiverLocked->balance;
            $receiverLocked->increment('balance', $amount);

            Transaction::create(['user_id' => $senderLocked->id, 'sender_id' => $senderLocked->id, 'receiver_id' => $receiverLocked->id, 'type' => 'transfer_out', 'status' => 'approved', 'amount' => $amount, 'balance_before' => $senderBalanceBefore, 'balance_after' => $senderBalanceBefore - $amount, 'description' => "Transfer ke {$receiverLocked->username}", 'reference_id' => $referenceId . '-out']);
            Transaction::create(['user_id' => $receiverLocked->id, 'sender_id' => $senderLocked->id, 'receiver_id' => $receiverLocked->id, 'type' => 'transfer_in', 'status' => 'approved', 'amount' => $amount, 'balance_before' => $receiverBalanceBefore, 'balance_after' => $receiverBalanceBefore + $amount, 'description' => "Transfer dari {$senderLocked->username}", 'reference_id' => $referenceId . '-in']);
        });

        $sender->refresh();
        return response()->json(['message' => 'Transfer berhasil.', 'data' => ['balance' => $sender->balance, 'amount_sent' => $amount, 'receiver_email' => $receiver->email, 'receiver_name' => $receiver->name, 'reference_id' => $referenceId]]);
    }

    // ── GET /transactions ─────────────────────────────────────────
    public function transactions(Request $request): JsonResponse
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->with(['sender:id,name,username', 'receiver:id,name,username'])
            ->orderByDesc('created_at')
            ->paginate(15);

        $data = $transactions->getCollection()->map(fn ($trx) => [
            'id'             => $trx->id,
            'type'           => $trx->type,
            'status'         => $trx->status,
            'amount'         => $trx->amount,
            'balance_before' => $trx->balance_before,
            'balance_after'  => $trx->balance_after,
            'description'    => $trx->description,
            'reference_id'   => $trx->reference_id,
            'sender'         => $trx->sender   ? ['name' => $trx->sender->name,   'username' => $trx->sender->username]   : null,
            'receiver'       => $trx->receiver ? ['name' => $trx->receiver->name, 'username' => $trx->receiver->username] : null,
            'created_at'     => $trx->created_at->toISOString(),
        ]);

        return response()->json(['data' => $data, 'meta' => ['current_page' => $transactions->currentPage(), 'last_page' => $transactions->lastPage(), 'per_page' => $transactions->perPage(), 'total' => $transactions->total()]]);
    }

    // ═══════════════════════════════════════════════════════════════
    // ADMIN ENDPOINTS
    // ═══════════════════════════════════════════════════════════════

    public function adminTopups(Request $request): JsonResponse
    {
        $status = $request->query('status', 'pending');
        $txs    = Transaction::with('user:id,name,username,email')->where('type', 'topup')->where('status', $status)->orderByDesc('created_at')->get();
        return response()->json(['data' => $txs]);
    }

    public function adminApprove(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::where('type', 'topup')->where('status', 'pending')->findOrFail($id);
        DB::transaction(function () use ($transaction) {
            $user          = User::lockForUpdate()->find($transaction->user_id);
            $balanceBefore = $user->balance;
            $user->increment('balance', $transaction->amount);
            $transaction->update(['status' => 'approved', 'balance_before' => $balanceBefore, 'balance_after' => $balanceBefore + $transaction->amount, 'description' => 'Top Up saldo']);
        });
        return response()->json(['message' => 'Top up berhasil disetujui.']);
    }

    public function adminReject(Request $request, $id): JsonResponse
    {
        $transaction = Transaction::where('type', 'topup')->where('status', 'pending')->findOrFail($id);
        $transaction->update(['status' => 'rejected', 'description' => 'Top Up saldo - ditolak admin']);
        return response()->json(['message' => 'Top up berhasil ditolak.']);
    }

    // ── GET /admin/users ──────────────────────────────────────────
    public function adminUsers(): JsonResponse
    {
        $users = User::select('id', 'name', 'username', 'email', 'phone', 'role', 'created_at')
            ->orderByDesc('created_at')
            ->get();
        return response()->json(['data' => $users]);
    }

    // ── POST /admin/users ─────────────────────────────────────────
    public function adminCreateUser(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_num'],
            'email'    => ['required', 'email', 'unique:users,email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:user,admin'],
        ], [
            'username.unique'    => 'Username sudah digunakan.',
            'username.alpha_num' => 'Username hanya boleh huruf dan angka.',
            'email.unique'       => 'Email sudah terdaftar.',
            'phone.unique'       => 'Nomor HP sudah terdaftar.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'balance'  => 0,
        ]);

        return response()->json([
            'message' => 'User berhasil dibuat.',
            'data'    => $user->only('id', 'name', 'username', 'email', 'phone', 'role', 'created_at'),
        ], 201);
    }

    // ── PUT /admin/users/{id} ─────────────────────────────────────
    public function adminUpdateUser(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => ['sometimes', 'string', 'max:255'],
            'username' => ['sometimes', 'string', 'max:50', 'unique:users,username,' . $id, 'alpha_num'],
            'email'    => ['sometimes', 'email', 'unique:users,email,' . $id, 'max:255'],
            'phone'    => ['nullable', 'string', 'max:15', 'unique:users,phone,' . $id],
            'password' => ['nullable', 'string', 'min:8'],
            'role'     => ['sometimes', 'in:user,admin'],
        ], [
            'username.unique'    => 'Username sudah digunakan.',
            'username.alpha_num' => 'Username hanya boleh huruf dan angka.',
            'email.unique'       => 'Email sudah terdaftar.',
            'phone.unique'       => 'Nomor HP sudah terdaftar.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        foreach (['name', 'username', 'email', 'phone', 'role'] as $field) {
            if ($request->has($field)) {
                $user->$field = $request->$field;
            }
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data'    => $user->only('id', 'name', 'username', 'email', 'phone', 'role', 'created_at'),
        ]);
    }

    // ── DELETE /admin/users/{id} ──────────────────────────────────
    public function adminDeleteUser(Request $request, $id): JsonResponse
    {
        // Admin tidak bisa hapus dirinya sendiri
        if ((int) $id === $request->user()->id) {
            return response()->json(['message' => 'Tidak dapat menghapus akun sendiri.'], 422);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.']);
    }


    private function validateAmount(mixed $raw): int
    {
        if ($raw === null || $raw === '') $this->throwAmountError('Nominal tidak boleh kosong.');

        $str = (string) $raw;
        if (preg_match('/[a-zA-Z]/', $str) || preg_match('/[^0-9\-]/', $str)) $this->throwAmountError('Nominal harus berupa angka.');
        if (str_contains($str, '.')) $this->throwAmountError('Nominal harus berupa angka bulat.');

        $amount = (int) $str;
        if ($amount < 0)   $this->throwAmountError('Nominal tidak boleh angka negatif.');
        if ($amount === 0) $this->throwAmountError('Nominal tidak boleh kosong.');
        if ($amount > self::MAX_TRANSACTION_AMOUNT) $this->throwAmountError('Nominal melebihi batas maksimum transaksi.');

        return $amount;
    }

    private function throwAmountError(string $message): never
    {
        throw ValidationException::withMessages(['amount' => [$message]]);
    }
}