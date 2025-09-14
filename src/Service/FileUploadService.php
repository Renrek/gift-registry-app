<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{
    private string $targetDirectory;

    public function __construct(string $targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }


    /**
     * Handles file upload and returns the relative file path.
     * @param UploadedFile $file
     * @param string|null $subfolder Optional subfolder (e.g., 'gift-images')
     * @return string Relative path from uploads root
     */
    public function upload(UploadedFile $file, ?string $subfolder = null): string
    {
        $uploadDir = $this->targetDirectory;
        if ($subfolder) {
            $uploadDir .= DIRECTORY_SEPARATOR . $subfolder;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
        }
        $filename = uniqid().'.'.$file->guessExtension();
        $file->move($uploadDir, $filename);
        return ($subfolder ? ($subfolder . '/') : '') . $filename;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
