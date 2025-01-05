<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class VerificationController extends Controller
{
    public function verify($id, $hash): \Illuminate\Http\JsonResponse
    {
        $user = User::findOrFail($id);
        if (sha1($user->email) !== $hash) {
            return response()->json(['message' => 'Invalid verification link.'], 400);
        }
        $user->email_verified_at = now();
        $user->save();
        return response()->json(['message' => 'Email verified successfully.'], 200);
    }
}

