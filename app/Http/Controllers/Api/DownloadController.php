<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadFile;
use App\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Download a file
     *
     * @param Request $request
     * @param string $token
     * @return Response|StreamedResponse
     */
    public function show(Request $request, string $token)
    {
        // Find the session by token
        $session = $this->uploadService->findByToken($token);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'The download link is invalid or has expired.',
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

        // If there are multiple files, we need to decide which file to download
        $file = null;

        if ($session->files->count() === 1) {
            $file = $session->files->first();
        } else {
            $fileId = $request->input('file_id');

            if (!$fileId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Multiple files found. Please specify which file to download.',
                    'files' => $session->files->map(function ($file) {
                        return [
                            'id' => $file->id,
                            'name' => $file->original_filename,
                            'size' => $file->file_size,
                        ];
                    }),
                ], 400);
            }

            $file = $session->files->firstWhere('id', $fileId);

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found.',
                ], 404);
            }
        }

        // Increment the download count
        $file->incrementDownloadCount();

        // Return the file as a download
        return Storage::disk('local')->download(
            $file->getStoragePath(),
            $file->original_filename
        );
    }
}
