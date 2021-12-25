<?php
namespace App\Service;

use App\Interfaces\FileUploaderInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private FileUploaderInterface $fileUploaderInterface ;

    public function __construct(FileUploaderInterface $fileUploaderInterface)
    {
     
        /**
         * @var FileUploaderInterface
         */
        $this->fileUploaderInterface = $fileUploaderInterface;
    }

    public function upload(UploadedFile $file)
    {
        var_dump($this->fileUploaderInterface->upload($file));
        die();
        uasort($elements, [$this->fileUploaderInterface, 'Impl']);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}
