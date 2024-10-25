<?php
// Chemin : src/Entity/Service.php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['service:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['service:read', 'service:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['service:read', 'service:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Groups(['service:read', 'service:write'])]
    private ?float $price = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'services')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['service:read'])]
    private ?Provider $provider = null;

    #[ORM\Column]
    #[Groups(['service:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['service:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    // Getters et setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    // Méthode "getter" pour "name"
    public function getName(): ?string
    {
        return $this->name;
    }

    // Méthode "setter" pour "name"
    public function setName(?string $name): static
    {
        $this->name = $name;
        return $this;
    }

    // Méthode "getter" pour "description"
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Méthode "setter" pour "description"
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    // Méthode "getter" pour "price"
    public function getPrice(): ?float
    {
        return $this->price;
    }

    // Méthode "setter" pour "price"
    public function setPrice(?float $price): static
    {
        $this->price = $price;
        return $this;
    }

    // Méthode "getter" pour "provider"
    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    // Méthode "setter" pour "provider"
    public function setProvider(?Provider $provider): static
    {
        $this->provider = $provider;
        return $this;
    }

    // Méthodes pour "createdAt" et "updatedAt"

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
