<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
#[ORM\Index(name: 'idx_search', fields: ['name'])]
#[ORM\Index(name: 'idx_advanced_search', fields: ['name', 'state', 'releaseRegularity', 'author', 'designer', 'editor', 'releaseYear'])]
#[ORM\HasLifecycleCallbacks]
class Manga
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
    #[Assert\Choice([
        'ongoing',
        'finished',
        'paused',
    ])]
    #[Assert\NotBlank]
    private ?string $state = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice([
        'daily',
        'weekly',
        'bi-weekly',
        'monthly',
    ])]
    #[Assert\NotBlank]
    private ?string $releaseRegularity = null;

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
    private ?string $designer = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 1,
        max: 100,
    )]
    #[Assert\NotBlank]
    private ?string $editor = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\Range(
        min: '1900',
        max: '+10 years',
    )]
    #[Assert\NotBlank]
    #[Assert\Date]
    private ?\DateTimeInterface $releaseYear = null;

    #[ORM\Column(length: 255)]
    private ?string $picturePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Work $work = null;

    #[ORM\OneToMany(mappedBy: 'manga', targetEntity: Volume::class, orphanRemoval: true)]
    private Collection $volumes;

    public function __construct()
    {
        $this->volumes = new ArrayCollection();
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

    public function getReleaseRegularity(): ?string
    {
        return $this->releaseRegularity;
    }

    public function setReleaseRegularity(string $releaseRegularity): self
    {
        $this->releaseRegularity = $releaseRegularity;

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

    public function getDesigner(): ?string
    {
        return $this->designer;
    }

    public function setDesigner(string $designer): self
    {
        $this->designer = $designer;

        return $this;
    }

    public function getEditor(): ?string
    {
        return $this->editor;
    }

    public function setEditor(string $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getReleaseYear(): ?int
    {
        return $this->releaseYear->format('Y');
    }

    public function setReleaseYear(\DateTimeInterface $releaseYear): self
    {
        $this->releaseYear = $releaseYear;

        return $this;
    }

    public function getPicturePath(): ?string
    {
        return $this->picturePath;
    }

    public function setPicturePath(string $picturePath): self
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
     * @return Collection<int, Volume>
     */
    public function getVolumes(): Collection
    {
        return $this->volumes;
    }

    public function addVolume(Volume $volume): self
    {
        if (!$this->volumes->contains($volume)) {
            $this->volumes->add($volume);
            $volume->setManga($this);
        }

        return $this;
    }

    public function removeVolume(Volume $volume): self
    {
        if ($this->volumes->removeElement($volume)) {
            // set the owning side to null (unless already changed)
            if ($volume->getManga() === $this) {
                $volume->setManga(null);
            }
        }

        return $this;
    }
}
