<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Index(name: 'idx_search', fields: ['targetTable', 'targetId'])]
#[ORM\HasLifecycleCallbacks]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('api_comment_show')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
    )]
    #[Assert\NotBlank]
    #[Groups('api_comment_show')]
    private ?string $message = null;

    #[ORM\Column(length: 190)]
    #[Groups('api_comment_show')]
    private ?string $targetTable = null;

    #[ORM\Column(options: [
        'unsigned' => true
    ])]
    #[Groups('api_comment_show')]
    private ?int $targetId = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('api_comment_show')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('api_comment_show')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('api_comment_show')]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'answers')]
    #[Groups('api_comment_show')]
    #[MaxDepth(1)]
    private ?self $parent = null;

    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class, orphanRemoval: true, fetch: 'EAGER')]
    #[Groups('api_comment_show')]
    #[MaxDepth(1)]
    private Collection $answers;

    public static function getTargetTables(): array
    {
        return [
            'anime' => Anime::class,
            'light-novel' => LightNovel::class,
            'manga' => Manga::class,
            'movie' => Movie::class,
            'news' => News::class,
            'user' => User::class,
            'work-news' => WorkNews::class,
        ];
    }

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getTargetTable(): ?string
    {
        return $this->targetTable;
    }

    public function setTargetTable(string $targetTable): self
    {
        $this->targetTable = $targetTable;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): self
    {
        $this->targetId = $targetId;

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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(self $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
            $answer->setParent($this);
        }

        return $this;
    }

    public function removeAnswer(self $answer): self
    {
        if ($this->answers->removeElement($answer)) {
            // set the owning side to null (unless already changed)
            if ($answer->getParent() === $this) {
                $answer->setParent(null);
            }
        }

        return $this;
    }
}
