<?php

namespace App\Http\Controllers;

use App\Services\FileService;
use App\Models\User;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Show the user's dashboard with files.
     */
    public function index()
    {
        $user = Auth::user();
        $files = $user->files()->latest()->get();
        $storageInfo = $this->fileService->getStorageInfo($user);

        return view('dashboard', compact('files', 'storageInfo'));
    }

    /**
     * Upload file via AJAX.
     */
    public function upload(Request $request)
    {
        // Manual validation to handle AJAX requests properly
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:10240', // Max 10MB
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación: ' . $validator->errors()->first(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = Auth::user();
        $file = $request->file('file');

        $result = $this->fileService->uploadFile($file, $user);

        // Always return JSON for file upload requests
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->route('dashboard')->with('success', $result['message']);
        }

        return redirect()->route('dashboard')->with('error', $result['message']);
    }

    /**
     * Get user's files as JSON (for AJAX requests).
     */
    public function getFiles()
    {
        $user = Auth::user();
        $files = $user->files()->latest()->get()->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->original_name,
                'size' => $this->formatBytes($file->size),
                'upload_date' => $file->created_at->format('Y-m-d H:i:s'),
                'created_at' => $file->created_at->toISOString(),
            ];
        });

        return response()->json([
            'files' => $files,
            'storage_info' => $this->fileService->getStorageInfo($user),
        ]);
    }

    /**
     * Delete a file.
     */
    public function destroy(File $file, Request $request)
    {
        $user = Auth::user();

        if ($this->fileService->deleteFile($file, $user)) {
            if ($request->ajax() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Archivo eliminado exitosamente.'
                ]);
            }
            return redirect()->route('dashboard')->with('success', 'Archivo eliminado exitosamente.');
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para eliminar este archivo.'
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', 'No tienes permisos para eliminar este archivo.');
    }

    /**
     * Get storage information for the current user.
     */
    public function getStorageInfo()
    {
        $user = Auth::user();
        $storageInfo = $this->fileService->getStorageInfo($user);

        return response()->json($storageInfo);
    }

    /**
     * Format bytes to human readable format.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' bytes';
    }
}
