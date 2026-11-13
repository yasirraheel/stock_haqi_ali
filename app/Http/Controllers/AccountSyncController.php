<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountSyncController extends Controller
{
    public function upsert(Request $request)
    {
        $rawBody = $request->getContent();
        $secret = (string) config('services.account_sync.secret');
        $signature = (string) $request->header('X-Account-Sync-Signature');
        $expected = $secret !== '' ? hash_hmac('sha256', $rawBody, $secret) : '';

        if ($secret === '' || $signature === '' || !hash_equals($expected, $signature)) {
            return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = json_decode($rawBody, true);
        $validator = Validator::make(is_array($data) ? $data : [], [
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'password_hash' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails() || !preg_match('/^\$(2y|2b|argon2i|argon2id)\$/', (string) ($data['password_hash'] ?? ''))) {
            return response()->json(['ok' => false, 'message' => 'Invalid account data'], 422);
        }

        $email = strtolower(trim($data['email']));
        $user = User::withTrashed()->whereRaw('LOWER(email) = ?', [$email])->first();
        $created = !$user;

        if (!$created) {
            return response()->json(['ok' => true, 'created' => false, 'user_id' => $user->id]);
        }

        User::withoutEvents(function () use (&$user, $email, $data): void {
            $user = new User();
            $user->usertype = 'User';
            $user->name = trim((string) ($data['name'] ?? explode('@', $email)[0]));
            $user->email = $email;
            $user->password = $data['password_hash'];
            $user->save();
        });

        return response()->json(['ok' => true, 'created' => true, 'user_id' => $user->id]);
    }
}
