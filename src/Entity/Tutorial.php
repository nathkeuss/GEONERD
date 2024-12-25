<?php

namespace App\Entity;

use App\Repository\TutorialRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorialRepository::class)]
class Tutorial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Country $country = null;

    /**
     * @var Collection<int, TutorialPart>
     */
    #[ORM\OneToMany(targetEntity: TutorialPart::class, mappedBy: 'tutorial', cascade: ['remove'])]
    private Collection $tutorialParts;

    #[ORM\Column(length: 255)]
    private ?string $backgroundImage = null;

    public function __construct()
    {
        $this->tutorialParts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, TutorialPart>
     */
    public function getTutorialParts(): Collection
    {
        return $this->tutorialParts;
    }

    public function addTutorialPart(TutorialPart $tutorialPart): static
    {
        if (!$this->tutorialParts->contains($tutorialPart)) {
            $this->tutorialParts->add($tutorialPart);
            $tutorialPart->setTutorial($this);
        }

        return $this;
    }

    public function removeTutorialPart(TutorialPart $tutorialPart): static
    {
        if ($this->tutorialParts->removeElement($tutorialPart)) {
            // set the owning side to null (unless already changed)
            if ($tutorialPart->getTutorial() === $this) {
                $tutorialPart->setTutorial(null);
            }
        }

        return $this;
    }

    public function getBackgroundImage(): ?string
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(string $backgroundImage): static
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }
}
