<?php

namespace App\Entity;

use App\Repository\ProposerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProposerRepository::class)]
class Proposer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'proposers')]
    private ?Prestataire $prestataire = null;

    #[ORM\ManyToOne(inversedBy: 'proposers')]
    private ?CategorieDeServices $categorie_service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrestataire(): ?Prestataire
    {
        return $this->prestataire;
    }

    public function setPrestataire(?Prestataire $prestataire): self
    {
        $this->prestataire = $prestataire;

        return $this;
    }

    public function getCategorieService(): ?CategorieDeServices
    {
        return $this->categorie_service;
    }

    public function setCategorieService(?CategorieDeServices $categorie_service): self
    {
        $this->categorie_service = $categorie_service;

        return $this;
    }
}
