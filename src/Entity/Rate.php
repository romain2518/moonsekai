<?php

namespace App\Entity;

use App\Repository\RateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ORM\Index(name: 'idx_search', fields: ['targetTable', 'targetId'])]
#[ORM\HasLifecycleCallbacks]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('api_rate_show')]
    private ?int $id = null;

    #[ORM\Column(length: 190)]
    #[Groups('api_rate_show')]
    private ?string $targetTable = null;

    #[ORM\Column(options: [
        'unsigned' => true
    ])]
    #[Groups('api_rate_show')]
    private ?int $targetId = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 0,
        max: 5,
    )]
    #[Assert\NotBlank]
    #[Groups('api_rate_show')]
    private ?int $rate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('api_rate_show')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups('api_rate_show')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('api_rate_show')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;

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
