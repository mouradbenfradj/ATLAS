<?php
namespace App\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use XBase\TableReader;

class DbfFile implements FileInterface
{
    public function upload(UploadedFile $file):TableReader
    {
        return new TableReader($file);
    }
}