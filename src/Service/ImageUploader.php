<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploader
{
    private ParameterBagInterface $parameterBag;
    private UniqueFilenameGenerator $uniqueFilenameGenerator;

    public function __construct(ParameterBagInterface $parameterBag, UniqueFilenameGenerator $uniqueFilenameGenerator)
    {
        $this->parameterBag = $parameterBag;
        $this->uniqueFilenameGenerator = $uniqueFilenameGenerator;
    }

    public function uploadImage(UploadedFile $image, string $subDirectory): string
    {
        $imageOriginalName = $image->getClientOriginalName();
        $imageExtension = $image->guessExtension();

        $imageNewFilename = $this->uniqueFilenameGenerator->generateUniqueFilename($imageOriginalName, $imageExtension);

        $projectDir = $this->parameterBag->get('kernel.project_dir');

        $imgDir = $projectDir . '/public/uploads/' . $subDirectory;

        $image->move($imgDir, $imageNewFilename);

        return $imageNewFilename;
    }

    public function removeImage(string $subDirectory, ?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $projectDir = $this->parameterBag->get('kernel.project_dir');
        $filePath = $projectDir . '/public/uploads/' . $subDirectory . '/' . $filename;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }


}