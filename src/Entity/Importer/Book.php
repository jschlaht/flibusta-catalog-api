<?php

namespace App\Entity\Importer;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ApiResource]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $bookData = null;

    #[ORM\Column(length: 2)]
    private ?string $bookLanguage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $bookYear = null;

    #[ORM\Column]
    private ?int $bookFlibustaId = null;

    #[ORM\ManyToOne(inversedBy: 'autorBooks')]
    private ?Autor $autor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookData(): ?string
    {
        return $this->bookData;
    }

    public function setBookData(string $bookData): static
    {
        $this->bookData = $bookData;

        return $this;
    }

    public function getBookLanguage(): ?string
    {
        return $this->bookLanguage;
    }

    public function setBookLanguage(string $bookLanguage): static
    {
        $this->bookLanguage = $bookLanguage;

        return $this;
    }

    public function getBookYear(): ?\DateTimeInterface
    {
        return $this->bookYear;
    }

    public function setBookYear(\DateTimeInterface $bookYear): static
    {
        $this->bookYear = $bookYear;

        return $this;
    }

    public function getBookFlibustaId(): ?int
    {
        return $this->bookFlibustaId;
    }

    public function setBookFlibustaId(int $bookFlibustaId): static
    {
        $this->bookFlibustaId = $bookFlibustaId;

        return $this;
    }

    public function getAutor(): ?Autor
    {
        return $this->autor;
    }

    public function setAutor(?Autor $autor): static
    {
        $this->autor = $autor;

        return $this;
    }
}
