<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PanierRepository::class)
 */
class Panier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity=Article::class, mappedBy="panier")
     */
    private $articles;

    /**
     * @ORM\OneToMany(targetEntity=PanierProduit::class, mappedBy="panier")
     */
    private $panierproduit;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $session;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="paniers")
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $archive;

    /**
     * @ORM\OneToMany(targetEntity=PanierValidate::class, mappedBy="Panier")
     */
    private $panierValidates;


    public function __construct()
    {
        $this->articles = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->panierProduit = new ArrayCollection();
        $this->panierproduit = new ArrayCollection();
        $this->panierValidates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
            $article->setPanier($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getPanier() === $this) {
                $article->setPanier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PanierProduit>
     */
    public function getPanierproduit(): Collection
    {
        return $this->panierproduit;
    }

    public function addPanierproduit(PanierProduit $panierproduit): self
    {
        if (!$this->panierproduit->contains($panierproduit)) {
            $this->panierproduit[] = $panierproduit;
            $panierproduit->setPanier($this);
        }

        return $this;
    }

    public function removePanierproduit(PanierProduit $panierproduit): self
    {
        if ($this->panierproduit->removeElement($panierproduit)) {
            // set the owning side to null (unless already changed)
            if ($panierproduit->getPanier() === $this) {
                $panierproduit->setPanier(null);
            }
        }

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(?string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getArchive(): ?string
    {
        return $this->archive;
    }

    public function setArchive(string $archive): self
    {
        $this->archive = $archive;

        return $this;
    }

    /**
     * @return Collection<int, PanierValidate>
     */
    public function getPanierValidates(): Collection
    {
        return $this->panierValidates;
    }

    public function addPanierValidate(PanierValidate $panierValidate): self
    {
        if (!$this->panierValidates->contains($panierValidate)) {
            $this->panierValidates[] = $panierValidate;
            $panierValidate->setPanier($this);
        }

        return $this;
    }

    public function removePanierValidate(PanierValidate $panierValidate): self
    {
        if ($this->panierValidates->removeElement($panierValidate)) {
            // set the owning side to null (unless already changed)
            if ($panierValidate->getPanier() === $this) {
                $panierValidate->setPanier(null);
            }
        }

        return $this;
    }


}
