<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class StatsController extends Controller
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Get upload stats
     *
     * @param Request $request
     * @param string $token
     * @return JsonResponse
     */
    public function show(Request $request, string $token): JsonResponse
    {
        $session = $this->uploadService->findByToken($token);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'The upload session is invalid or has expired.',
            ], 404);
        }

        // If the session is password protected, validate the password
        if ($session->password) {
            $password = $request->input('password');

            if (!$this->uploadService->verifyPassword($session, $password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password.',
                    'requires_password' => true,
                ], 401);
            }
        }

        $data = [
            'success' => true,
            'expires_at' => $session->expires_at->toIso8601String(),
            'expires_in' => now()->diffInSeconds($session->expires_at),
            'has_password' => (bool) $session->password,
            'total_files' => $session->files->count(),
            'total_size' => $session->totalSize(),
            'total_downloads' => $session->files->sum('download_count'),
            'files' => $session->files->map(function ($file) {
                return [
                    'id' => $file->id,
                    'filename' => $file->original_filename,
                    'size' => $file->file_size,
                    'mime_type' => $file->mime_type,
                    'downloads' => $file->download_count,
                ];
            }),
        ];

        return response()->json($data);
    }
}
