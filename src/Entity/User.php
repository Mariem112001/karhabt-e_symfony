<?php

namespace App\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: "user")]
#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "idU", type: "integer", nullable: false)]
    private ?int $idu = null;

    #[ORM\Column(name: "nom", type: "string", length: 255, nullable: true, options: ["default" => "NULL"])]
    private ?string $nom = null;

    #[ORM\Column(name: "prenom", type: "string", length: 255, nullable: true, options: ["default" => "NULL"])]
    private ?string $prenom = null;

    #[ORM\Column(name: "DateNaissance", type: "date", nullable: true, options: ["default" => "NULL"])]
    private ?\DateTimeInterface $dateNaissance = null;

    #[ORM\Column(name: "numTel", type: "integer", nullable: true, options: ["default" => "NULL"])]
    private ?int $numTel = null;

    #[ORM\Column(name: "eMAIL", type: "string", length: 255, nullable: true, options: ["default" => "NULL"])]
    private ?string $email = null;

    #[ORM\Column(name: "passwd", type: "string", length: 255, nullable: true, options: ["default" => "NULL"])]
    private ?string $passwd = null;

    #[ORM\Column(name: "role", type: "string", length: 0, nullable: true, options: ["default" => "NULL"])]
    private ?string $role = null;

    #[ORM\Column(name: "imageUser", type: "string", length: 255, nullable: false)]
    private ?string $imageUser = null;

    public function getIdu(): ?int
    {
        return $this->idu;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTimeInterface $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getNumTel(): ?int
    {
        return $this->numTel;
    }

    public function setNumTel(?int $numTel): static
    {
        $this->numTel = $numTel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswd(): ?string
    {
        return $this->passwd;
    }

    public function setPasswd(?string $passwd): static
    {
        $this->passwd = $passwd;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getImageUser(): ?string
    {
        return $this->imageUser;
    }

    public function setImageUser(string $imageUser): static
    {
        $this->imageUser = $imageUser;

        return $this;
    }
}
