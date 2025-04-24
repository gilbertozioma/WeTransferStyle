<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\UploadFile;
use Illuminate\Support\Str;
use App\Models\UploadSession;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendUploadNotificationEmail;

class UploadService
{
    protected TokenService $tokenService;

    public function __construct(TokenService $tokenService)
    {
        $this->tokenService = $tokenService;
    }

    /**
     * Create a new upload session with files
     *
     * @param Collection<UploadedFile> $files
     * @param int $expiresIn
     * @param string|null $emailToNotify
     * @param string|null $password
     * @return UploadSession
     */
    public function createUploadSession(Collection $files, int $expiresIn = 1, ?string $emailToNotify = null, ?string $password = null): UploadSession
    {
        // Create upload session
        $session = UploadSession::create([
            'token' => $this->tokenService->generateUniqueToken(),
            'expires_at' => Carbon::now()->addDays($expiresIn),
            'email_to_notify' => $emailToNotify,
            'password' => $password ? bcrypt($password) : null,
        ]);

        // Add files to session
        foreach ($files as $file) {
            $this->addFileToSession($session, $file);
        }

        // Send notification email if requested
        if ($emailToNotify) {
            SendUploadNotificationEmail::dispatch($session);
        }

        return $session;
    }

    /**
     * Add a file to an existing upload session
     *
     * @param UploadSession $session
     * @param UploadedFile $file
     * @return UploadFile
     */
    protected function addFileToSession(UploadSession $session, UploadedFile $file): UploadFile
    {
        // Generate a unique filename
        $storedFilename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store the file
        Storage::disk('local')->put('uploads/' . $storedFilename, file_get_contents($file->getRealPath()));

        // Create file record
        return $session->files()->create([
            'original_filename' => $file->getClientOriginalName(),
            'stored_filename' => $storedFilename,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'download_count' => 0,
        ]);
    }

    /**
     * Find upload session by token
     *
     * @param string $token
     * @return UploadSession|null
     */
    public function findByToken(string $token): ?UploadSession
    {
        return UploadSession::where('token', $token)
            ->where('expires_at', '>', Carbon::now())
            ->with('files')
            ->first();
    }

    /**
     * Verify password for a session
     *
     * @param UploadSession $session
     * @param string|null $password
     * @return bool
     */
    public function verifyPassword(UploadSession $session, ?string $password): bool
    {
        // If no password is set, return true
        if (!$session->password) {
            return true;
        }

        // If password is required but not provided, return false
        if (!$password) {
            return false;
        }

        return password_verify($password, $session->password);
    }

    /**
     * Clean expired uploads
     *
     * @return int Number of deleted sessions
     */
    public function cleanExpiredUploads(): int
    {
        $expiredSessions = UploadSession::where('expires_at', '<', Carbon::now())->get();

        foreach ($expiredSessions as $session) {
            foreach ($session->files as $file) {
                Storage::disk('local')->delete('uploads/' . $file->stored_filename);
            }
        }

        return UploadSession::where('expires_at', '<', Carbon::now())->delete();
    }
}
