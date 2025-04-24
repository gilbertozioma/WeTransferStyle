<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\UploadSession;

class TokenService
{
    /**
     * Generate a unique token
     *
     * @return string
     */
    public function generateUniqueToken(): string
    {
        $token = $this->generateToken();

        // Check if token exists and regenerate if needed
        while (UploadSession::where('token', $token)->exists()) {
            $token = $this->generateToken();
        }

        return $token;
    }

    /**
     * Generate a random token
     *
     * @return string
     */
    protected function generateToken(): string
    {
        return Str::random(32);
    }
}
