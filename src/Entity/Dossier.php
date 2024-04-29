<?php

namespace App\Entity;

use App\Repository\DossierRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DossierRepository::class)]
class Dossier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'CIN est obligatoire')]
    #[Assert\Length(min: 8, max: 8, exactMessage: 'CIN doit contenir 8 chiffres')]
    private ?int $cin = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Nom est obligatoire')]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Prenom est obligatoire')]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Region est obligatoire')]
    private ?string $region = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'Date est obligatoire')]
    #[Assert\LessThanOrEqual('-18 years', message: 'Date de naissance doit être supérieure ou égale à 18 ans')]
    #[Assert\LessThanOrEqual('today', message: 'Date de naissance doit être inférieure ou égale à la date actuelle')]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Montant est obligatoire')]
    #[Assert\Positive(message: 'Montant doit être positif')]
    private ?int $montant = null;

    #[ORM\ManyToOne(inversedBy: 'dossier')]
    private ?DemandeDossier $demandeDossier = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMontant(): ?int
    {
        return $this->montant;
    }

    public function setMontant(int $montant): static
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDemandeDossier(): ?DemandeDossier
    {
        return $this->demandeDossier;
    }

    public function setDemandeDossier(?DemandeDossier $demandeDossier): static
    {
        $this->demandeDossier = $demandeDossier;

        return $this;
    }
}
