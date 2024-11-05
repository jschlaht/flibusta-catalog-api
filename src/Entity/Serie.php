<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $serieName = null;

    /**
     * @var Collection<int, Book>
     */
    #[ORM\OneToMany(targetEntity: Book::class, mappedBy: 'serie')]
    private Collection $serieBooks;

    public function __construct()
    {
        $this->serieBooks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerieName(): ?string
    {
        return $this->serieName;
    }

    public function setSerieName(string $serieName): static
    {
        $this->serieName = $serieName;

        return $this;
    }

    /**
     * @return Collection<int, Book>
     */
    public function getSerieBooks(): Collection
    {
        return $this->serieBooks;
    }

    public function addSerieBook(Book $serieBook): static
    {
        if (!$this->serieBooks->contains($serieBook)) {
            $this->serieBooks->add($serieBook);
            $serieBook->setSerie($this);
        }

        return $this;
    }

    public function removeSerieBook(Book $serieBook): static
    {
        if ($this->serieBooks->removeElement($serieBook)) {
            // set the owning side to null (unless already changed)
            if ($serieBook->getSerie() === $this) {
                $serieBook->setSerie(null);
            }
        }

        return $this;
    }
}
