<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileInterface
{
    public function upload(UploadedFile $file);
}
