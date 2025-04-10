<?php

namespace App\Tests\Service;

use App\Service\UniqueFilenameGenerator;
use PHPUnit\Framework\TestCase;

class UniqueFilenameGeneratorTest extends TestCase
{
    public function testGenerateUniqueFilenameReturnsAValidFormat(): void
    {
        $generator = new UniqueFilenameGenerator();

        $originalName = 'my-image.png';
        $extension = 'png';

        // on génère deux noms à partir du même fichier simulé
        $filename1 = $generator->generateUniqueFilename($originalName, $extension);
        $filename2 = $generator->generateUniqueFilename($originalName, $extension);

        // on affiche les résultats pour les voir en vrai dans la console
        echo "\nnom généré 1 : $filename1";
        echo "\nnom généré 2 : $filename2\n";

        // on vérifie que chaque nom finit bien par l’extension .png
        $this->assertStringEndsWith('.png', $filename1);

        // on vérifie que le hash du nom original est bien dans le nom généré
        $hashedPart = hash('sha256', $originalName);
        $this->assertStringContainsString($hashedPart, $filename1);

        // on s’assure que les deux noms sont bien différents (unique à chaque appel)
        $this->assertNotEquals($filename1, $filename2);
    }
}
