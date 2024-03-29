<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('api_report_show')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
    )]
    #[Assert\NotBlank]
    #[Groups('api_report_show')]
    private ?string $message = null;

    #[ORM\Column(length: 60)]
    #[Assert\Choice(callback: 'getTypes')]
    #[Groups('api_report_show')]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 12,
        max: 255,
    )]
    #[Assert\Url]
    #[Groups('api_report_show')]
    private ?string $url = null;

    #[ORM\Column]
    #[Groups('api_report_show')]
    private ?bool $isProcessed = false;

    #[ORM\Column]
    #[Groups('api_report_show')]
    private ?bool $isImportant = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('api_report_show')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('api_report_show')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'reports', fetch: 'EAGER')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('api_report_show')]
    private ?User $user = null;

    public static function getTypes(): array
    {
        return [
            'user behaviour',
            'minor error',
            'bug',
        ];
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function isIsProcessed(): ?bool
    {
        return $this->isProcessed;
    }

    public function setIsProcessed(bool $isProcessed): self
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    public function isIsImportant(): ?bool
    {
        return $this->isImportant;
    }

    public function setIsImportant(bool $isImportant): self
    {
        $this->isImportant = $isImportant;

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
}
