<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Help to save the image file
 */
class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }


    /**
     * Create a custom name of the image file and move the file to a specified folder
     * @param UploadedFile $file - The image file
     * @return string - Name of the image file
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            $file->move($this->getTargetDirectory(), $fileName);

        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    /**
     * Delete an image file from a specified folder
     */
    public function deleteFile($path, $imageFile){

        if($imageFile != 'man.png' && $imageFile != 'women.png' && $imageFile != 'user.png'){

            $fs = new Filesystem();
            $fs->remove($path.$imageFile);
        }
    }

}