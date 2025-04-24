<?php

namespace App\Http\Controllers\Api;

use App\Services\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use App\Http\Requests\UploadRequest;

class UploadController extends Controller
{
    protected UploadService $uploadService;

    public function __construct(UploadService $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * Handle file upload
     *
     * @param UploadRequest $request
     * @return JsonResponse
     */
    public function store(UploadRequest $request): JsonResponse
    {
        $files = Collection::wrap($request->file('files'));
        $expiresIn = $request->input('expires_in', 1);
        $emailToNotify = $request->input('email_to_notify');
        $password = $request->input('password');

        $session = $this->uploadService->createUploadSession(
            $files,
            $expiresIn,
            $emailToNotify,
            $password
        );

        return response()->json([
            'success' => true,
            'download_link' => URL::to('/api/download/' . $session->token),
        ]);
    }
}
