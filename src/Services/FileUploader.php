<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function upload(UploadedFile $file, string $directory, string $name = ''): string
    {
        $name = preg_replace('/\s+/', '_', $name);
        if (str_contains($name, '?')) {
            preg_replace('?', '', $name);
        }
        $newFileName = $name . '-' . uniqid() . '.' . $file->guessExtension();
        $file->move($directory, $newFileName);
        return $newFileName;
    }

    public function delete(string $filename, string $directory) {
        return unlink($directory . '/' . $filename);
    }
    public function update(string $oldFilename, string $directory, UploadedFile $file, string $newName = '') {
        $this->delete($oldFilename, $directory);
        return $this->upload($file, $directory, $newName);

    }

}
