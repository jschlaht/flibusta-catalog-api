<?php

namespace App\Entity;

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
    private ?string $bookTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $bookSubtitle = null;

    #[ORM\Column(length: 2)]
    private ?string $bookLanguage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $bookYear = null;

    #[ORM\Column]
    private ?int $bookFlibustaId = null;

    #[ORM\ManyToOne(inversedBy: 'autorBooks')]
    private ?Autor $autor = null;

    #[ORM\ManyToOne(inversedBy: 'serieBooks')]
    private ?Serie $serie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookTitle(): ?string
    {
        return $this->bookTitle;
    }

    public function setBookTitle(string $bookTitle): static
    {
        $this->bookTitle = $bookTitle;

        return $this;
    }

    public function getBookSubtitle(): ?string
    {
        return $this->bookSubtitle;
    }

    public function setBookSubtitle(?string $bookSubtitle): static
    {
        $this->bookSubtitle = $bookSubtitle;

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

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): static
    {
        $this->serie = $serie;

        return $this;
    }
}
