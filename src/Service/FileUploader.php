<?php
namespace App\Service;

use App\Util\FileInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{

     /**
     * @var Strategy The Context maintains a reference to one of the Strategy
     * objects. The Context does not know the concrete class of a strategy. It
     * should work with all strategies via the Strategy interface.
     */
    private $fileInterface;

    /**
     * Usually, the Context accepts a strategy through the constructor, but also
     * provides a setter to change it at runtime.
     */
    public function __construct(FileInterface $fileInterface)
    {
        $this->fileInterface = $fileInterface;
    }

    /**
     * Usually, the Context allows replacing a Strategy object at runtime.
     */
    public function setStrategy(FileInterface $fileInterface)
    {
        $this->fileInterface = $fileInterface;
    }
    /*  private $slugger;

     public function __construct(SluggerInterface $slugger)
     {
         $this->slugger = $slugger;
     } */

    public function upload(UploadedFile $file)
    {
        dd($file->guessExtension());
        return $file;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}