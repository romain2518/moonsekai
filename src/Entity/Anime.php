<?php

namespace App\Entity;

use App\Repository\AnimeRepository;
use App\Validator as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: AnimeRepository::class)]
#[Vich\Uploadable]
#[ORM\Index(name: 'idx_search', fields: ['name'])]
#[ORM\Index(name: 'idx_advanced_search', fields: ['name', 'state', 'author', 'animationStudio', 'releaseYear'])]
#[ORM\HasLifecycleCallbacks]
class Anime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 1,
        max: 100,
    )]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 1000,
    )]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: 'getStates')]
    #[Assert\NotBlank]
    private ?string $state = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 1,
        max: 100,
    )]
    #[Assert\NotBlank]
    private ?string $author = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 1,
        max: 100,
    )]
    #[Assert\NotBlank]
    private ?string $animationStudio = null;

    //* Property used to set release year max range, see __construct()
    private ?int $maxReleaseYear = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Range(
        min: 1900,
        maxPropertyPath: 'maxReleaseYear',
    )]
    private ?int $releaseYear = null;

    #[Vich\UploadableField(mapping: 'anime_pictures', fileNameProperty: 'picturePath')]
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png"],
        maxSizeMessage: "The maximum allowed file size is 5MB.",
        mimeTypesMessage: "Only png, jpg and jpeg images are allowed."
    )]
    #[CustomAssert\NotBlankVich(message: 'Please provide a picture to create a anime.', target: 'picturePath')]
    private ?File $pictureFile = null;
    
    #[ORM\Column(length: 255)]
    private ?string $picturePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'animes')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'animes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Work $work = null;

    #[ORM\OneToMany(mappedBy: 'anime', targetEntity: Season::class, orphanRemoval: true)]
    private Collection $seasons;

    public function __construct()
    {
        $this->seasons = new ArrayCollection();
        $this->maxReleaseYear = (new \DateTime())->format('Y') + 10;
    }

    public static function getStates()
    {
        return [
            'ongoing',
            'finished',
            'paused',
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getAnimationStudio(): ?string
    {
        return $this->animationStudio;
    }

    public function setAnimationStudio(string $animationStudio): self
    {
        $this->animationStudio = $animationStudio;

        return $this;
    }

    public function getMaxReleaseYear(): ?int
    {
        return $this->maxReleaseYear;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear;
    }

    public function setReleaseYear(int $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getPictureFile(): ?File
    {
        return $this->pictureFile;
    }

    public function setPictureFile(?File $pictureFile = null): self
    {
        $this->pictureFile = $pictureFile;

        if (null !== $pictureFile) {
            // Needed to trigger event listener
            $this->updatedAt = new \DateTime();
        }

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(?string $picturePath): self
    {
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateDatetimes(): void
    {
        if ($this->getCreatedAt() === null) { // => PrePersist
            
            $this->setCreatedAt(new \DateTime('now'));
        } else { // => PreUpdate

            $this->setUpdatedAt(new \DateTime('now'));
        } 
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(?Work $work): self
    {
        $this->work = $work;

        return $this;
    }

    /**
     * @return Collection<int, Season>
     */
    public function getSeasons(): Collection
    {
        return $this->seasons;
    }

    public function addSeason(Season $season): self
    {
        if (!$this->seasons->contains($season)) {
            $this->seasons->add($season);
            $season->setAnime($this);
        }

        return $this;
    }

    public function removeSeason(Season $season): self
    {
        if ($this->seasons->removeElement($season)) {
            // set the owning side to null (unless already changed)
            if ($season->getAnime() === $this) {
                $season->setAnime(null);
            }
        }

        return $this;
    }
}
