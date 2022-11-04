<?php

namespace App\Entity;

use App\Repository\WorkRepository;
use App\Validator as CustomAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: WorkRepository::class)]
#[Vich\Uploadable]
#[ORM\Index(name: 'idx_search', fields: ['name', 'originalName'])]
#[ORM\Index(name: 'idx_advanced_search', fields: ['name', 'type', 'nativeCountry', 'originalName'])]
#[ORM\HasLifecycleCallbacks]
class Work
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('api_user_show')]
    private ?int $id = null;

    #[ORM\Column(length: 190)]
    #[Assert\Length(
        min: 1,
        max: 190,
    )]
    #[Assert\NotBlank]
    #[Groups('api_user_show')]
    private ?string $name = null;

    #[ORM\Column(length: 20)]
    #[Assert\Choice(callback: 'getTypes')]
    #[Assert\NotBlank]
    private ?string $type = null;
    
    #[ORM\Column(length: 190)]
    #[Assert\NotBlank]
    #[CustomAssert\Country]
    private ?string $nativeCountry = null;
    
    #[ORM\Column(length: 190, nullable: true)]
    #[Assert\Length(
        max: 190,
    )]
    private ?string $originalName = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Unique]
    #[Assert\All([
        new Assert\Length(
            max: 255,
        ),
        new Assert\NotBlank,
    ])]
    private ?array $alternativeName = [];

    #[Vich\UploadableField(mapping: 'work_pictures', fileNameProperty: 'picturePath')]
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png"],
        maxSizeMessage: "The maximum allowed file size is 5MB.",
        mimeTypesMessage: "Only png, jpg and jpeg images are allowed."
    )]
    #[CustomAssert\NotBlankVich(message: 'Please provide a picture to create a work.', target: 'picturePath')]
    private ?File $pictureFile = null;

    #[ORM\Column(length: 255)]
    #[Groups('api_user_show')]
    private ?string $picturePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: Progress::class, orphanRemoval: true)]
    private Collection $progress;

    #[ORM\ManyToOne(inversedBy: 'works')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: Movie::class, orphanRemoval: true)]
    private Collection $movies;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: LightNovel::class, orphanRemoval: true)]
    private Collection $lightNovels;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: WorkNews::class, orphanRemoval: true)]
    private Collection $workNews;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: Manga::class, orphanRemoval: true)]
    private Collection $mangas;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: Anime::class, orphanRemoval: true)]
    private Collection $animes;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'works')]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: Platform::class, inversedBy: 'works')]
    private Collection $platforms;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'followedWorks')]
    private Collection $followers;

    public function __construct()
    {
        $this->progress = new ArrayCollection();
        $this->movies = new ArrayCollection();
        $this->lightNovels = new ArrayCollection();
        $this->workNews = new ArrayCollection();
        $this->mangas = new ArrayCollection();
        $this->animes = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->platforms = new ArrayCollection();
        $this->followers = new ArrayCollection();
    }

    public static function getTypes()
    {
        return [
            'shōnen',
            'seinen',
            'shōjo',
            'josei',
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNativeCountry(): ?string
    {
        return $this->nativeCountry;
    }

    public function setNativeCountry(string $nativeCountry): self
    {
        $this->nativeCountry = $nativeCountry;

        return $this;
    }

    public function getOriginalName(): ?string
    {
        return $this->originalName;
    }

    public function setOriginalName(?string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getAlternativeName(): array
    {
        return $this->alternativeName;
    }

    public function setAlternativeName(?array $alternativeName): self
    {
        $this->alternativeName = $alternativeName;

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

    /**
     * @return Collection<int, Progress>
     */
    public function getProgress(): Collection
    {
        return $this->progress;
    }

    public function addProgress(Progress $progress): self
    {
        if (!$this->progress->contains($progress)) {
            $this->progress->add($progress);
            $progress->setWork($this);
        }

        return $this;
    }

    public function removeProgress(Progress $progress): self
    {
        if ($this->progress->removeElement($progress)) {
            // set the owning side to null (unless already changed)
            if ($progress->getWork() === $this) {
                $progress->setWork(null);
            }
        }

        return $this;
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
     * @return Collection<int, Movie>
     */
    public function getMovies(): Collection
    {
        return $this->movies;
    }

    public function addMovie(Movie $movie): self
    {
        if (!$this->movies->contains($movie)) {
            $this->movies->add($movie);
            $movie->setWork($this);
        }

        return $this;
    }

    public function removeMovie(Movie $movie): self
    {
        if ($this->movies->removeElement($movie)) {
            // set the owning side to null (unless already changed)
            if ($movie->getWork() === $this) {
                $movie->setWork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LightNovel>
     */
    public function getLightNovels(): Collection
    {
        return $this->lightNovels;
    }

    public function addLightNovel(LightNovel $lightNovel): self
    {
        if (!$this->lightNovels->contains($lightNovel)) {
            $this->lightNovels->add($lightNovel);
            $lightNovel->setWork($this);
        }

        return $this;
    }

    public function removeLightNovel(LightNovel $lightNovel): self
    {
        if ($this->lightNovels->removeElement($lightNovel)) {
            // set the owning side to null (unless already changed)
            if ($lightNovel->getWork() === $this) {
                $lightNovel->setWork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, WorkNews>
     */
    public function getWorkNews(): Collection
    {
        return $this->workNews;
    }

    public function addWorkNews(WorkNews $workNews): self
    {
        if (!$this->workNews->contains($workNews)) {
            $this->workNews->add($workNews);
            $workNews->setWork($this);
        }

        return $this;
    }

    public function removeWorkNews(WorkNews $workNews): self
    {
        if ($this->workNews->removeElement($workNews)) {
            // set the owning side to null (unless already changed)
            if ($workNews->getWork() === $this) {
                $workNews->setWork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Manga>
     */
    public function getMangas(): Collection
    {
        return $this->mangas;
    }

    public function addManga(Manga $manga): self
    {
        if (!$this->mangas->contains($manga)) {
            $this->mangas->add($manga);
            $manga->setWork($this);
        }

        return $this;
    }

    public function removeManga(Manga $manga): self
    {
        if ($this->mangas->removeElement($manga)) {
            // set the owning side to null (unless already changed)
            if ($manga->getWork() === $this) {
                $manga->setWork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Anime>
     */
    public function getAnimes(): Collection
    {
        return $this->animes;
    }

    public function addAnime(Anime $anime): self
    {
        if (!$this->animes->contains($anime)) {
            $this->animes->add($anime);
            $anime->setWork($this);
        }

        return $this;
    }

    public function removeAnime(Anime $anime): self
    {
        if ($this->animes->removeElement($anime)) {
            // set the owning side to null (unless already changed)
            if ($anime->getWork() === $this) {
                $anime->setWork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, Platform>
     */
    public function getPlatforms(): Collection
    {
        return $this->platforms;
    }

    public function addPlatform(Platform $platform): self
    {
        if (!$this->platforms->contains($platform)) {
            $this->platforms->add($platform);
        }

        return $this;
    }

    public function removePlatform(Platform $platform): self
    {
        $this->platforms->removeElement($platform);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(User $follower): self
    {
        if (!$this->followers->contains($follower)) {
            $this->followers->add($follower);
        }

        return $this;
    }

    public function removeFollower(User $follower): self
    {
        $this->followers->removeElement($follower);

        return $this;
    }
}
