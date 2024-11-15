<?php

namespace App\Entity\Importer;

use App\Repository\AutorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'bookAutor')]
    private Collection $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, Book>
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): static
    {
        if (!$this->books->contains($book)) {
            $this->books->add($book);
            $book->setBookAutor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): static
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getBookAutor() === $this) {
                $book->setBookAutor(null);
            }
        }

        return $this;
    }
}
