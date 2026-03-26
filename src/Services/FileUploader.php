<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    public function upload(UploadedFile $file, string $directory, string $name = ''): string
    {
        $newFileName = $name . '-' . uniqid() . '.' . $file->guessExtension();
        $file->move($directory, $newFileName);
        return $newFileName;
    }

    public function delete(string $filename, string $directory) {
        return unlink($directory . '/' . $filename);
    }
    public function update(string $oldFilename, string $directory, UploadedFile $file, string $newName = '') {
        $this->delete($oldFilename, $directory);
        $this->upload($file, $directory, $newName);
    }

}
