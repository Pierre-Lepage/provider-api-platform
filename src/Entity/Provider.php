<?php
// Chemin : src/Entity/Provider.php

namespace App\Entity;

use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['provider:read', 'service:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['provider:read', 'provider:write', 'service:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups(['provider:read', 'provider:write'])]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Groups(['provider:read', 'provider:write'])]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Service::class, orphanRemoval: true)]
    #[Groups(['provider:read'])]
    private Collection $services;

    #[ORM\Column]
    #[Groups(['provider:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    #[Groups(['provider:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

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

    // Méthode "getter" pour "email"
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Méthode "setter" pour "email"
    public function setEmail(?string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Méthode "getter" pour "phone"
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    // Méthode "setter" pour "phone"
    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    // Méthodes pour "services"

    /**
     * @return Collection<int, Service>
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): static
    {
        if (!$this->services->contains($service)) {
            $this->services->add($service);
            $service->setProvider($this);
        }

        return $this;
    }

    public function removeService(Service $service): static
    {
        if ($this->services->removeElement($service)) {
            // set the owning side to null (unless already changed)
            if ($service->getProvider() === $this) {
                $service->setProvider(null);
            }
        }

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
