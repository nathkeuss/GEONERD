<?php

namespace App\Tests\Twig;

use App\Entity\User;
use App\Twig\ProfilePictureExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\Packages;

class ProfilePictureExtensionTest extends TestCase
{
    public function testReturnsDefaultImageIfUserIsNull(): void
    {
        // on simule le composant asset de symfony
        $packages = $this->createMock(Packages::class);

        // on attend que getUrl soit appelé avec l’image par défaut
        $packages->expects($this->once())
            ->method('getUrl')
            ->with('assets/img/base/default_picture.svg')
            ->willReturn('/assets/img/base/default_picture.svg');

        // on instancie l'extension twig avec le mock
        $extension = new ProfilePictureExtension($packages);

        // on passe null pour l’utilisateur, donc on doit avoir l’image par défaut
        $result = $extension->getProfilePicture(null);

        $this->assertEquals('/assets/img/base/default_picture.svg', $result);
    }

    public function testReturnsDefaultImageIfUserHasNoPicture(): void
    {
        // on crée un user qui n’a pas de photo
        $user = $this->createMock(User::class);
        $user->method('getProfilePicture')->willReturn(null);

        $packages = $this->createMock(Packages::class);

        // même principe : image par défaut
        $packages->expects($this->once())
            ->method('getUrl')
            ->with('assets/img/base/default_picture.svg')
            ->willReturn('/assets/img/base/default_picture.svg');

        $extension = new ProfilePictureExtension($packages);

        $result = $extension->getProfilePicture($user);

        $this->assertEquals('/assets/img/base/default_picture.svg', $result);
    }

    public function testReturnsUserProfilePicture(): void
    {
        // on crée un user avec une vraie image
        $user = $this->createMock(User::class);
        $user->method('getProfilePicture')->willReturn('avatar.png');

        $packages = $this->createMock(Packages::class);

        // ici on attend l’url construite avec le chemin de l’image utilisateur
        $packages->expects($this->once())
            ->method('getUrl')
            ->with('uploads/user/profile_pictures/avatar.png')
            ->willReturn('/uploads/user/profile_pictures/avatar.png');

        $extension = new ProfilePictureExtension($packages);

        $result = $extension->getProfilePicture($user);

        $this->assertEquals('/uploads/user/profile_pictures/avatar.png', $result);
    }
}
