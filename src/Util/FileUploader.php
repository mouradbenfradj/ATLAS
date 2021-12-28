<?php


namespace App\Util;

use App\Interfaces\FileInterfaces;

class FileUploader
{
    public function upload(string $fileInterfaces)
    {
        dd($fileInterfaces);
        return $fileInterfaces;
    }/* public function upload(FileInterfaces $fileInterfaces)
    {
        dd($fileInterfaces);
        return $fileInterfaces;
    } */
}
