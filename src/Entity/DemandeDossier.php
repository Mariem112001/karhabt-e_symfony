<?php

namespace App\Entity;

use App\Repository\DemandeDossierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DemandeDossierRepository::class)]
class DemandeDossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $urlcin = null;

    #[ORM\Column(length: 255)]
    private ?string $urlcerretenu = null;

    #[ORM\Column(length: 255)]
    private ?string $urlatttravail = null;

    #[ORM\Column(length: 255)]
    private ?string $urldecrevenu = null;

    #[ORM\Column(length: 255)]
    private ?string $urlextnaissance = null;

    #[ORM\OneToMany(targetEntity: Dossier::class, mappedBy: 'demandeDossier')]
    private Collection $dossier;



    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "demandeDossier")]
    #[ORM\JoinColumn(name: "idu", referencedColumnName: "idU")]
    private ?User $user = null;



    public function __construct()
    {
        $this->dossier = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlcin(): ?string
    {
        return $this->urlcin;
    }

    public function setUrlcin(string $urlcin): static
    {
        $this->urlcin = $urlcin;

        return $this;
    }

    public function getUrlcerretenu(): ?string
    {
        return $this->urlcerretenu;
    }

    public function setUrlcerretenu(string $urlcerretenu): static
    {
        $this->urlcerretenu = $urlcerretenu;

        return $this;
    }

    public function getUrlatttravail(): ?string
    {
        return $this->urlatttravail;
    }

    public function setUrlatttravail(string $urlatttravail): static
    {
        $this->urlatttravail = $urlatttravail;

        return $this;
    }

    public function getUrldecrevenu(): ?string
    {
        return $this->urldecrevenu;
    }

    public function setUrldecrevenu(string $urldecrevenu): static
    {
        $this->urldecrevenu = $urldecrevenu;

        return $this;
    }

    public function getUrlextnaissance(): ?string
    {
        return $this->urlextnaissance;
    }

    public function setUrlextnaissance(string $urlextnaissance): static
    {
        $this->urlextnaissance = $urlextnaissance;

        return $this;
    }

    /**
     * @return Collection<int, Dossier>
     */
    public function getDossier(): Collection
    {
        return $this->dossier;
    }

    public function addDossier(Dossier $dossier): static
    {
        if (!$this->dossier->contains($dossier)) {
            $this->dossier->add($dossier);
            $dossier->setDemandeDossier($this);
        }

        return $this;
    }

    public function removeDossier(Dossier $dossier): static
    {
        if ($this->dossier->removeElement($dossier)) {
            // set the owning side to null (unless already changed)
            if ($dossier->getDemandeDossier() === $this) {
                $dossier->setDemandeDossier(null);
            }
        }

        return $this;
    }
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
    public function __toString(): string
    {
      
        return $this->id;
    }

}
