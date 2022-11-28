<?php

namespace App\Entity;

use App\Repository\ContactRequestRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRequestRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ContactRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\Length(
        min: 3,
        max: 180,
    )]
    #[Assert\Email]
    #[Assert\NotBlank]
    private ?string $applicantEmail = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 5,
        max: 255,
    )]
    #[Assert\NotBlank]
    private ?string $message = null;

    #[ORM\Column]
    private ?bool $isImportant = false;

    #[ORM\Column]
    private ?bool $isProcessed = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicantEmail(): ?string
    {
        return $this->applicantEmail;
    }

    public function setApplicantEmail(string $applicantEmail): self
    {
        $this->applicantEmail = $applicantEmail;

        return $this;
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

    public function isIsImportant(): ?bool
    {
        return $this->isImportant;
    }

    public function setIsImportant(bool $isImportant): self
    {
        $this->isImportant = $isImportant;

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
}
