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
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'autor')]
    private Collection $autorBooks;

    public function __construct()
    {
        $this->autorBooks = new ArrayCollection();
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
    public function getAutorBooks(): Collection
    {
        return $this->autorBooks;
    }

    public function addAutorBook(Book $autorBook): static
    {
        if (!$this->autorBooks->contains($autorBook)) {
            $this->autorBooks->add($autorBook);
            $autorBook->setAutor($this);
        }

        return $this;
    }

    public function removeAutorBook(Book $autorBook): static
    {
        if ($this->autorBooks->removeElement($autorBook)) {
            // set the owning side to null (unless already changed)
            if ($autorBook->getAutor() === $this) {
                $autorBook->setAutor(null);
            }
        }

        return $this;
    }
}
