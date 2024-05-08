<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Table(name: "voiture")]
#[ORM\Entity]
class Voiture
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idV", type: "integer", nullable: false)]
    private $idv;

    #[ORM\Column(name: "marque", type: "string", length: 50, nullable: true, options: ["default" => "NULL"])]
    #[Assert\NotBlank(message: "La marque ne peut pas être vide.")]
    private $marque;

    #[ORM\Column(name: "modele", type: "string", length: 50, nullable: true, options: ["default" => "NULL"])]
    #[Assert\NotBlank(message: "Le modèle ne peut pas être vide.")]
    private $modele;

    #[ORM\Column(name: "couleur", type: "string", length: 20, nullable: true, options: ["default" => "NULL"])]
    #[Assert\NotBlank(message: "Le couleur ne peut pas être vide.")]
    private $couleur;

    #[ORM\Column(name: "prix", type: "decimal", precision: 10, scale: 2, nullable: true)]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide.")]
    #[Assert\Positive(message: "le prix doit etre un nombre positif")]
    private $prix;
    

    #[ORM\Column(name: "img", type: "string", length: 255, nullable: false)]
    


    

    private $img;

    #[ORM\Column(name: "description", type: "text", length: 65535, nullable: true, options: ["default" => "NULL"])]
    #[Assert\NotBlank(message: "La description ne doit pas etre vide")]
    private $description;


    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "Voitures")]
    #[ORM\JoinColumn(name: "idu", referencedColumnName: "idU")]
    private ?User $user = null;
    


    public function getIdv(): ?int
    {
        return $this->idv;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(?string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(?string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(string $img): static
    {
        $this->img = $img;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

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
