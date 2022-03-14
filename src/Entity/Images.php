<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Suites::class, inversedBy: 'images')]
    private $suite;

    #[ORM\Column(type: 'string', length: 255)]
    private $titre;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?String
    {
        return $this->titre;
    }

    public function setTitre(?String $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSuite(): ?Suites
    {
        return $this->suite;
    }

    public function setSuite(?Suites $suite): self
    {
        $this->suite = $suite;

        return $this;
    }

    public function __toString()
    {
        return $this->titre;
    }
}
