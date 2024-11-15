<?php

namespace App\Entity\Importer;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $bookData = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $bookLanguage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $bookYear = null;

    #[ORM\Column]
    private ?int $bookFlibustaId = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    private ?Autor $bookAutor = null;

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

    public function setBookLanguage(?string $bookLanguage): static
    {
        $this->bookLanguage = $bookLanguage;

        return $this;
    }

    public function getBookYear(): ?\DateTimeInterface
    {
        return $this->bookYear;
    }

    public function setBookYear(?\DateTimeInterface $bookYear): static
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

    public function getBookAutor(): ?Autor
    {
        return $this->bookAutor;
    }

    public function setBookAutor(?Autor $bookAutor): static
    {
        $this->bookAutor = $bookAutor;

        return $this;
    }
}
