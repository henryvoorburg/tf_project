<?php

namespace App\Entity;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 * @UniqueEntity(fields={"loginname"}, message="Gebruikersnaam al in gebruik.")
 */
class Person implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="loginname", type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Vul een geldig gebruikersnaam in.")
     */
    private $loginname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vul een geldig wachtwoord in.")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vul een geldige voornaam in.")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $preprovision;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vul een geldige achternaam in.")
     */
    private $lastname;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="Vul een geldige geboortedatum in.")
     */
    private $dateofbirth;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Kies een geldig geslacht in.")
     */
    private $gender;

    /**
     * @ORM\Column(type="string", length=255,unique=true)
     * @Assert\NotBlank(message="Vul een geldig e-mailadres in.")
     */
    private $emailadres;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLoginname(): ?string
    {
        return $this->loginname;
    }

    public function setLoginname(string $loginname): self
    {
        $this->loginname = $loginname;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getPreprovision(): ?string
    {
        return $this->preprovision;
    }

    public function setPreprovision(string $preprovision): self
    {
        $this->preprovision = $preprovision;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDateofbirth(): ?\DateTimeInterface
    {
        return $this->dateofbirth;
    }

    public function setDateofbirth(\DateTimeInterface $dateofbirth): self
    {
        $this->dateofbirth = $dateofbirth;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getEmailadres(): ?string
    {
        return $this->emailadres;
    }

    public function setEmailadres(string $emailadres): self
    {
        $this->emailadres = $emailadres;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->loginname;
    }

    public function setUsername(string $username): self
    {
        $this->loginname = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_MEMBER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed for apps that do not check user passwords
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName()
    {
        $fullname = $this->getFirstname() . ' ' . $this->getLastname();
        return $fullname;
    }

    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function isEnabled()
    {
        return (bool)$this->enabled;
    }
}
