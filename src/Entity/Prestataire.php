<?php

namespace App\Entity;

use App\Repository\PrestataireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrestataireRepository::class)]
class Prestataire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $site_internet = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $num_tel = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $num_tva = null;

    #[ORM\OneToOne(inversedBy: 'prestataire', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Promotion::class)]
    private Collection $promotions;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Stage::class)]
    private Collection $stages;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Proposer::class)]
    private Collection $proposers;

    #[ORM\ManyToMany(mappedBy: 'prestataire', targetEntity: CategorieDeServices::class)]
    private Collection $categories;

    #[ORM\OneToMany(mappedBy: 'prestataire', targetEntity: Images::class, cascade: ['remove', 'persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    private ?Collection $images = null;




    public function __construct()
    {
        $this->promotions = new ArrayCollection();
        $this->stages = new ArrayCollection();
        $this->proposers = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->categories = new ArrayCollection();
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

    public function getSiteInternet(): ?string
    {
        return $this->site_internet;
    }

    public function setSiteInternet(?string $site_internet): self
    {
        $this->site_internet = $site_internet;

        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->num_tel;
    }

    public function setNumTel(?string $num_tel): self
    {
        $this->num_tel = $num_tel;

        return $this;
    }

    public function getNumTva(): ?string
    {
        return $this->num_tva;
    }

    public function setNumTva(?string $num_tva): self
    {
        $this->num_tva = $num_tva;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * @return Collection<int, Promotion>
     */
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }

    public function addPromotion(Promotion $promotion): self
    {
        if (!$this->promotions->contains($promotion)) {
            $this->promotions->add($promotion);
            $promotion->setPrestataire($this);
        }

        return $this;
    }

    public function removePromotion(Promotion $promotion): self
    {
        if ($this->promotions->removeElement($promotion)) {
            // set the owning side to null (unless already changed)
            if ($promotion->getPrestataire() === $this) {
                $promotion->setPrestataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stage>
     */
    public function getStages(): Collection
    {
        return $this->stages;
    }

    public function addStage(Stage $stage): self
    {
        if (!$this->stages->contains($stage)) {
            $this->stages->add($stage);
            $stage->setPrestataire($this);
        }

        return $this;
    }

    public function removeStage(Stage $stage): self
    {
        if ($this->stages->removeElement($stage)) {
            // set the owning side to null (unless already changed)
            if ($stage->getPrestataire() === $this) {
                $stage->setPrestataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Proposer>
     */
    public function getProposers(): Collection
    {
        return $this->proposers;
    }

    public function addProposer(Proposer $proposer): self
    {
        if (!$this->proposers->contains($proposer)) {
            $this->proposers->add($proposer);
            $proposer->setPrestataire($this);
        }

        return $this;
    }

    public function removeProposer(Proposer $proposer): self
    {
        if ($this->proposers->removeElement($proposer)) {
            // set the owning side to null (unless already changed)
            if ($proposer->getPrestataire() === $this) {
                $proposer->setPrestataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategorieDeServices>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(CategorieDeServices $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setPrestataire($this);
        }

        return $this;
    }

    public function removeCategory(CategorieDeServices $category): self
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getPrestataire() === $this) {
                $category->setPrestataire(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setPrestataire($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getPrestataire() === $this) {
                $image->setPrestataire(null);
            }
        }

        return $this;
    }
}
