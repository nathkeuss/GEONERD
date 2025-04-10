<?php

namespace App\Tests\Service;

use App\Service\ImageUploader;
use App\Service\UniqueFilenameGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderTest extends TestCase
{
    // ici on prépare les mocks pour simuler les dépendances du service
    private $parameterBag;
    private $filenameGenerator;
    private $imageUploader;

    // cette méthode est appelée avant chaque test pour initialiser les objets
    protected function setUp(): void
    {
        // on simule le parameter bag de symfony
        $this->parameterBag = $this->createMock(ParameterBagInterface::class);

        // on simule le générateur de noms de fichiers
        $this->filenameGenerator = $this->createMock(UniqueFilenameGenerator::class);

        // on instancie le service avec les mocks
        $this->imageUploader = new ImageUploader($this->parameterBag, $this->filenameGenerator);
    }

    public function testUploadImage(): void
    {
        // on crée un faux fichier uploadé
        $uploadedFile = $this->createMock(UploadedFile::class);

        // quelques valeurs simulées
        $originalName = 'test-image.png';
        $extension = 'png';
        $subDirectory = 'avatars';
        $generatedFilename = 'unique123.png';
        $projectDir = '/var/www/project';

        // quand on demande le nom d’origine, on veut 'test-image.png'
        $uploadedFile->method('getClientOriginalName')
            ->willReturn($originalName);

        // pour l’extension aussi, on veut 'png'
        $uploadedFile->method('guessExtension')
            ->willReturn($extension);

        // le générateur va retourner notre nom simulé
        $this->filenameGenerator->method('generateUniqueFilename')
            ->with($originalName, $extension)
            ->willReturn($generatedFilename);

        // le parameterBag retourne le chemin racine du projet
        $this->parameterBag->method('get')
            ->with('kernel.project_dir')
            ->willReturn($projectDir);

        // on s’assure que la méthode move est bien appelée une seule fois avec les bons arguments
        $uploadedFile->expects($this->once())
            ->method('move')
            ->with($projectDir . '/public/uploads/' . $subDirectory, $generatedFilename);

        // on appelle la méthode à tester
        $result = $this->imageUploader->uploadImage($uploadedFile, $subDirectory);

        // on vérifie que le résultat est bien le nom généré
        $this->assertEquals($generatedFilename, $result);
    }

    public function testRemoveImageWithFilename(): void
    {
        $subDirectory = 'avatars';
        $filename = 'to-delete.png';

        // on utilise un dossier temporaire pour faire les tests
        $projectDir = sys_get_temp_dir();

        // on crée le dossier s’il n’existe pas
        $filePath = $projectDir . '/public/uploads/' . $subDirectory;
        if (!is_dir($filePath)) {
            mkdir($filePath, 0777, true);
        }

        // on crée un faux fichier à cet endroit
        $fullPath = $filePath . '/' . $filename;
        file_put_contents($fullPath, 'dummy content');

        // on simule le retour du chemin racine du projet
        $this->parameterBag->method('get')
            ->with('kernel.project_dir')
            ->willReturn($projectDir);

        // on appelle la méthode pour supprimer le fichier
        $this->imageUploader->removeImage($subDirectory, $filename);

        // on vérifie que le fichier a bien été supprimé
        $this->assertFileDoesNotExist($fullPath);
    }

    public function testRemoveImageWithNullFilename(): void
    {
        // ici on teste que ça ne plante pas si le nom du fichier est null
        $this->imageUploader->removeImage('avatars', null);

        // si aucune exception est levée, c’est bon
        $this->assertTrue(true);
    }
}
