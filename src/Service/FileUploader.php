<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Imagine\Image\Box;
use Imagine\Gd\Imagine;

class FileUploader
{
    public function __construct(
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        // $this->resize($file);

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        

        try {
            $file->move($this->getTargetDirectory(), $fileName);

        } catch (FileException $e) {
            throw new Exception("Error Processing Request", $e);
        }

        return $fileName;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }

    public function resize(string $filename): void
    {
        list($iwidth, $iheight) = getimagesize($filename);

        $imagine = new Imagine();
        
        $minWidth = 1080;
        $minHeight = 720;
        
        if ($minWidth > $iwidth && $minHeight >  $iheight) {
            $photo = $imagine->open($filename);
            $photo->resize(new Box($minWidth, $minHeight))->save($filename, ['format' => 'jpeg']);
        }

        // list($iwidth, $iheight) = getimagesize($filename);

        // $imagine = new Imagine();

        // $ratio = $iwidth / $iheight;

        // $width = 980;
        // $height = 720;

        // if ($width / $height > $ratio) {
        //     $width = $height * $ratio;
        // } else {
        //     $height = $width / $ratio;
        // }

        // $photo = $imagine->open($filename);
        // $photo->resize(new Box($width, $height))->save($filename, ['format' => 'jpeg']);
        
    }
}