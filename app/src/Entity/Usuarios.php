<?php

namespace App\Entity;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuariosRepository::class)]
class Usuarios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $pass = null;

    #[ORM\Column]
    private ?float $salario = null;

    #[ORM\Column]
    private ?float $salario_disponible = null;

    #[ORM\Column]
    private ?float $ahorro = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(string $pass): static
    {
        $this->pass = $pass;

        return $this;
    }

    public function getSalario(): ?float
    {
        return $this->salario;
    }

    public function setSalario(float $salario): static
    {
        $this->salario = $salario;

        return $this;
    }

    public function getSalarioDisponible(): ?float
    {
        return $this->salario_disponible;
    }

    public function setSalarioDisponible(float $salario_disponible): static
    {
        $this->salario_disponible = $salario_disponible;

        return $this;
    }

    public function getAhorro(): ?float
    {
        return $this->ahorro;
    }

    public function setAhorro(float $ahorro): static
    {
        $this->ahorro = $ahorro;

        return $this;
    }
}
