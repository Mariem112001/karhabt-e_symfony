<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
 

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Assert\NotBlank]
    private $contenue;

    #[ORM\Column(type: 'date', nullable: true)]
    private $date;

    #[ORM\ManyToOne(targetEntity: Actualite::class, inversedBy: 'commentaires')]
    private $actualite;

    #[ORM\OneToMany(mappedBy: 'commentaire', targetEntity: Reponse::class)]
    private $reponses;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "commentaires")]
    #[ORM\JoinColumn(name: "idu", referencedColumnName: "idU")]
    private ?User $user = null;
    

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getcontenue(): ?string
    {
        return $this->contenue;
    }

    public function setcontenue(?string $contenue): self
    {
        $this->contenue = $contenue;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getActualite(): ?Actualite
    {
        return $this->actualite;
    }

    public function setActualite(?Actualite $actualite): self
    {
        $this->actualite = $actualite;

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    public function addReponse(Reponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setCommentaire($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): self
    {
        if ($this->reponses->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getCommentaire() === $this) {
                $reponse->setCommentaire(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->contenue ?? 'Unnamed Comment';
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





    
}
