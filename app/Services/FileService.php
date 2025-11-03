<?php

namespace App\Services;

use App\Models\User;
use App\Models\File;
use App\Models\Configuration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class FileService
{
    /**
     * Process and store an uploaded file with all security checks.
     */
    public function uploadFile(UploadedFile $file, User $user): array
    {
        try {
            // Validate file exists and is accessible
            if (!$file || !$file->isValid()) {
                return [
                    'success' => false,
                    'message' => 'Error: El archivo no es válido o no se pudo procesar.',
                ];
            }

            // Get file size safely
            $fileSize = $file->getSize();
            if ($fileSize === false || $fileSize === null) {
                return [
                    'success' => false,
                    'message' => 'Error: No se pudo determinar el tamaño del archivo.',
                ];
            }

            // 1. Check if user can upload based on quota
            if (!$user->canUpload($fileSize)) {
                return [
                    'success' => false,
                    'message' => 'Error: Cuota de almacenamiento excedida. Límite: ' . $this->formatBytes($user->getStorageQuota()),
                ];
            }

            // 2. Check file type restrictions
            $extension = strtolower($file->getClientOriginalExtension());
            if ($this->isForbiddenFileType($extension)) {
                return [
                    'success' => false,
                    'message' => "Error: El tipo de archivo '.{$extension}' no está permitido.",
                ];
            }

            // 3. If it's a ZIP file, analyze its contents
            if ($extension === 'zip') {
                $zipAnalysis = $this->analyzeZipContents($file);
                if (!$zipAnalysis['safe']) {
                    return [
                        'success' => false,
                        'message' => "Error: El archivo '{$zipAnalysis['problematicFile']}' dentro del .zip no está permitido.",
                    ];
                }
            }

            // 4. Store the file
            $storedPath = $this->storeFile($file, $user);

            // 5. Save file record to database
            $fileRecord = File::create([
                'user_id' => $user->id,
                'original_name' => $file->getClientOriginalName(),
                'path' => $storedPath,
                'size' => $fileSize,
            ]);

            return [
                'success' => true,
                'message' => 'Archivo subido exitosamente.',
                'file' => $fileRecord,
            ];

        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor al subir el archivo.',
            ];
        }
    }

    /**
     * Check if a file extension is forbidden.
     */
    private function isForbiddenFileType(string $extension): bool
    {
        $forbiddenExtensions = Configuration::getValue('extensiones_prohibidas', 'exe,bat,js,php,sh');
        $forbiddenList = array_map('trim', explode(',', $forbiddenExtensions));

        return in_array($extension, $forbiddenList);
    }

    /**
     * Analyze ZIP file contents for forbidden file types.
     */
    private function analyzeZipContents(UploadedFile $file): array
    {
        $zipPath = $file->getPathname();
        $zip = new ZipArchive;

        if ($zip->open($zipPath) === TRUE) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                if ($this->isForbiddenFileType($fileExtension)) {
                    $zip->close();
                    return [
                        'safe' => false,
                        'problematicFile' => basename($filename),
                    ];
                }
            }
            $zip->close();
        }

        return ['safe' => true];
    }

    /**
     * Store the uploaded file in the filesystem directly in project.
     */
    private function storeFile(UploadedFile $file, User $user): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $userDirectory = base_path('storage/uploads/users/' . $user->id);

        // Create directory if it doesn't exist
        if (!file_exists($userDirectory)) {
            mkdir($userDirectory, 0755, true);
        }

        // Store file directly in filesystem
        $filePath = $userDirectory . '/' . $filename;
        $file->move($userDirectory, $filename);

        // Return relative path for database storage
        return 'storage/uploads/users/' . $user->id . '/' . $filename;
    }

    /**
     * Delete a file and its database record.
     */
    public function deleteFile(File $file, User $user): bool
    {
        // Ensure user can only delete their own files (unless admin)
        if ($file->user_id !== $user->id && !$user->isAdmin()) {
            return false;
        }

        // Delete from filesystem
        $fullPath = base_path($file->path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete from database
        return $file->delete();
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

    /**
     * Get user's storage usage information.
     */
    public function getStorageInfo(User $user): array
    {
        $used = $user->getStorageUsed();
        $quota = $user->getStorageQuota();
        $percentage = $quota > 0 ? round(($used / $quota) * 100, 2) : 0;

        return [
            'used' => $used,
            'quota' => $quota,
            'remaining' => $quota - $used,
            'percentage' => $percentage,
            'used_formatted' => $this->formatBytes($used),
            'quota_formatted' => $this->formatBytes($quota),
            'remaining_formatted' => $this->formatBytes($quota - $used),
        ];
    }
}
