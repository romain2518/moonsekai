<?php

namespace App\Entity;

use App\Repository\CalendarEventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CalendarEventRepository::class)]
#[ORM\Index(name: 'idx_search', fields: ['targetTable', 'targetId'])]
#[ORM\HasLifecycleCallbacks]
class CalendarEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 3,
        max: 100,
    )]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\GreaterThan('now')]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?\DateTimeInterface $start = null;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $end = null;

    #[ORM\Column(length: 190)]
    #[Assert\Choice(callback: 'getTargetTables')]
    private ?string $targetTable = null;

    #[ORM\Column(options: [
        'unsigned' => true
    ])]
    #[Assert\Positive(message: 'This value is invalid.')]
    #[Assert\NotBlank()]
    private ?int $targetId = null;

    private ?string $targetName = null;

    private ?int $workId = null;

    private ?string $picturePath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'calendarEvents')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $user = null;

    public static function getTargetTables(): array
    {
        return [
            'Chapter' => Chapter::class,
            'Episode' => Episode::class,
            'Light novel' => LightNovel::class,
            'Movie' => Movie::class,
            'News' => News::class,
            'Work News' => WorkNews::class,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

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

    public function setTargetId(?int $targetId): self
    {
        $this->targetId = $targetId;

        return $this;
    }

    public function getTargetName(): ?string
    {
        return $this->targetName;
    }

    public function setTargetName(?string $targetName): self
    {
        $this->targetName = $targetName;

        return $this;
    }

    public function getWorkId(): ?string
    {
        return $this->workId;
    }

    public function setWorkId(?string $workId): self
    {
        $this->workId = $workId;

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
    public function updateEnd(): void
    {
        $this->setEnd(
            (\DateTime::createFromInterface($this->getStart()))->add(new \DateInterval('PT1H'))
        );
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
}
