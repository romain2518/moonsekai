<?php

namespace App\Entity;

use App\Repository\PlatformRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PlatformRepository::class)]
#[Vich\Uploadable]
#[ORM\Index(name: 'idx_search', fields: ['name'])]
#[ORM\HasLifecycleCallbacks]
class Platform
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

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 3,
        max: 100,
    )]
    #[Assert\NotBlank]
    #[Assert\Url]
    private ?string $url = null;

    #[Vich\UploadableField(mapping: 'platform_pictures', fileNameProperty: 'picturePath')]
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png"],
        maxSizeMessage: "The maximum allowed file size is 5MB.",
        mimeTypesMessage: "Only png, jpg and jpeg images are allowed."
    )]
    private ?File $pictureFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picturePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'platforms')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity: Work::class, mappedBy: 'platforms')]
    private Collection $works;

    public function __construct()
    {
        $this->works = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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

    /**
     * @return Collection<int, Work>
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works->add($work);
            $work->addPlatform($this);
        }

        return $this;
    }

    public function removeWork(Work $work): self
    {
        if ($this->works->removeElement($work)) {
            $work->removePlatform($this);
        }

        return $this;
    }
}
