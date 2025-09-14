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
         * Saves a base64-encoded image to the uploads directory and returns the relative file path.
         * @param string $base64 The base64-encoded image (may be a data URL or plain base64)
         * @param string $subfolder Optional subfolder (e.g., 'gift-images')
         * @return string|null Relative path from uploads root, or null on failure
         */
        public function saveBase64Image(string $base64, string $subfolder = 'gift-images'): ?string
        {
            $matches = [];
            if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $base64, $matches)) {
                $ext = $matches[1];
                $base64 = $matches[2];
            } else {
                $ext = 'jpg';
            }
            $data = base64_decode($base64);
            if ($data === false) {
                return null;
            }
            $uploadDir = $this->targetDirectory . DIRECTORY_SEPARATOR . $subfolder;
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = uniqid('gift_', true) . '.' . $ext;
            $filePath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
            if (file_put_contents($filePath, $data) === false) {
                return null;
            }
            return $subfolder . '/' . $filename;
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
