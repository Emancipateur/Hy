<?php

namespace App\Entity;

use App\Repository\EtablissementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtablissementsRepository::class)]
class Etablissements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\Column(type: 'string', length: 255)]
    private $ville;

    #[ORM\Column(type: 'string', length: 255)]
    private $adresse;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\OneToOne(targetEntity: Gerants::class, cascade: ['persist', 'remove'])]
    private $gerant;

    #[ORM\OneToMany(mappedBy: 'etablissements', targetEntity: Suites::class)]
    private $suite;

    public function __construct()
    {
        $this->suite = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getGerant(): ?Gerants
    {
        return $this->gerant;
    }

    public function setGerant(?Gerants $gerant): self
    {
        $this->gerant = $gerant;

        return $this;
    }

    /**
     * @return Collection<int, Suites>
     */
    public function getSuite(): Collection
    {
        return $this->suite;
    }

    public function addSuite(Suites $suite): self
    {
        if (!$this->suite->contains($suite)) {
            $this->suite[] = $suite;
            $suite->setEtablissements($this);
        }

        return $this;
    }

    public function removeSuite(Suites $suite): self
    {
        if ($this->suite->removeElement($suite)) {
            // set the owning side to null (unless already changed)
            if ($suite->getEtablissements() === $this) {
                $suite->setEtablissements(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getNom();
    }
}
