<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Entity\Voiture;
use Symfony\Component\Validator\Constraints as Assert;
 
#[ORM\Entity]
class Arrivage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idA", type: "integer", nullable: false)]
    private ?int $ida = null;

    #[ORM\Column(name: "quantite", type: "integer", nullable: true)]
    #[Assert\NotBlank(message: "la quantite ne peut pas etre vide.")]

    #[Assert\Positive(message: "la quantite doit etre positif")]
    private ?int $quantite = null;

    #[ORM\Column(name: "dateEntree", type: "date", nullable: true)]
    #[Assert\NotBlank(message: "La date peut pas être vide.")]
    #[Assert\Type(\DateTimeInterface::class)]
    #[Assert\GreaterThanOrEqual("today", message: "La date doit être aujourd'hui ou ultérieure.")]
   
    private ?\DateTimeInterface $dateentree = null;

    #[ORM\ManyToOne(targetEntity: Voiture::class)]
    #[ORM\JoinColumn(name: "idv", referencedColumnName: "idV")]
    #[Assert\NotBlank(message: "tu dois selectionner une voiture")]
    private ?Voiture $voiture = null; 


    
     #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "arrivages")]
    #[ORM\JoinColumn(name: "idu", referencedColumnName: "idU")]
    private ?User $user = null;

    public function getIda(): ?int
    {
        return $this->ida;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getDateentree(): ?\DateTimeInterface
    {
        return $this->dateentree;
    }

    public function setDateentree(?\DateTimeInterface $dateentree): static
    {
        $this->dateentree = $dateentree;

        return $this;
    }
    public function getVoiture(): ?Voiture
    {
        return $this->voiture;
    }

    public function setVoiture(?Voiture $voiture): self
    {
        $this->voiture = $voiture;
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










}
