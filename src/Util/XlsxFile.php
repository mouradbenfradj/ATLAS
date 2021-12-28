<?php
namespace App\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class XlsxFile implements FileInterface
{
    public function upload(UploadedFile $file)
    {
        return "Xlsx";
    }
}
