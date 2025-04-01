<?php

namespace App\Twig;

use App\Entity\User;
use Symfony\Component\Asset\Packages;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProfilePictureExtension extends AbstractExtension
{
    private Packages $assets;

    public function __construct(Packages $assets)
    {
        $this->assets = $assets;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('profile_picture', [$this, 'getProfilePicture']),
        ];
    }

    public function getProfilePicture(?User $user): string {
        if (!$user || !$user->getProfilePicture()) {
            return $this->assets->getUrl('assets/img/base/default_picture.svg');
        }

        return $this->assets->getUrl('uploads/user/profile_pictures/' . $user->getProfilePicture());
    }

}