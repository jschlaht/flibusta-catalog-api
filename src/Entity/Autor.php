<?php

namespace App\Entity;

use App\Repository\AutorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutorRepository::class)]
class Autor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $autorLastName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autorFirstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $autorMiddleName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAutorLastName(): ?string
    {
        return $this->autorLastName;
    }

    public function setAutorLastName(string $autorLastName): static
    {
        $this->autorLastName = $autorLastName;

        return $this;
    }

    public function getAutorFirstName(): ?string
    {
        return $this->autorFirstName;
    }

    public function setAutorFirstName(?string $autorFirstName): static
    {
        $this->autorFirstName = $autorFirstName;

        return $this;
    }

    public function getAutorMiddleName(): ?string
    {
        return $this->autorMiddleName;
    }

    public function setAutorMiddleName(?string $autorMiddleName): static
    {
        $this->autorMiddleName = $autorMiddleName;

        return $this;
    }
}
