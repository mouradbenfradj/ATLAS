<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Util\FileUploader;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserService
{
    private $uploader;

    public function __construct(FileUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function upload(User $user, UploadedFile $file): void
    {
        $transformedStatus = $this->uploader->upload($file->guessExtension().'Upload');

        // ... connect to Twitter and send the encoded status
    }
}
