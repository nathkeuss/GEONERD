<?php

namespace App\Entity;

use App\Repository\PostForumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostForumRepository::class)]
class PostForum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
 /*   #[Assert\NotBlank(message: "Le titre est obligatoire")]
    #[Assert\Length(
        min: 5,
        max: 20,
        minMessage: "Le titre doit au moins contenir {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]*/
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Le contenu est obligatoire.")]
    #[Assert\Length(
        min: 5,
        max: 1200,
        minMessage: "Le contenu doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le contenu ne doit pas contenir plus de {{ limit }} caractères"
    )]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: "8M",
        mimeTypes: ["image/png", "image/jpeg", "image/webp"],
        maxSizeMessage: "L'image ne peut pas dépasser 8 Mo.",
        mimeTypesMessage: "Seules les images png, jpeg, et webp sont autorisées",
    )]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePost = null;

    #[ORM\ManyToOne(inversedBy: 'replies')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'replies')]
    private ?self $parentPost = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'parentPost', cascade: ['remove'], orphanRemoval: true)]
    private Collection $replies;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
        $this->datePost = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getDatePost(): ?\DateTimeInterface
    {
        return $this->datePost;
    }

    public function setDatePost(\DateTimeInterface $datePost): static
    {
        $this->datePost = $datePost;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getParentPost(): ?self
    {
        return $this->parentPost;
    }

    public function setParentPost(?self $parentPost): static
    {
        $this->parentPost = $parentPost;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function getRepliesCount(): int
    {
        return $this->replies->count();
    }

    public function addReply(self $reply): static
    {
        if (!$this->replies->contains($reply)) {
            $this->replies->add($reply);
            $reply->setParentPost($this);
        }

        return $this;
    }

    public function removeReply(self $reply): static
    {
        if ($this->replies->removeElement($reply)) {
            // set the owning side to null (unless already changed)
            if ($reply->getParentPost() === $this) {
                $reply->setParentPost(null);
            }
        }

        return $this;
    }

    public function getLastActivityDate(): \DateTimeInterface
    {
        $lastReply = $this->getReplies()->last();
        $date = $lastReply ? $lastReply->getDatePost() : $this->getDatePost();

        // Retourne un objet \DateTimeInterface
        return (new \DateTime($date->format('Y-m-d H:i:s'), new \DateTimeZone('UTC')))
            ->setTimezone(new \DateTimeZone('Europe/Paris'));


    }

}
