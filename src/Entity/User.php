<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("user")
 * @ORM\Entity
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Un autre utilisateur s'est déjà inscrit avec cette email, merci de la modifier"
 * )
 * @UniqueEntity(
 *     fields={"username"},
 *     message="Un autre utilisateur s'est déjà inscrit avec cet username, merci de le modifier"
 * )
 */
class User implements UserInterface
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public const DEFAULT_ROLES = [self::ROLE_USER];
    public const ALL_ROLES = [self::ROLE_ADMIN, self::ROLE_USER];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=25, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir un nom d'utilisateur.")
     *  @Assert\Length(
     *      min = 2,
     *      max = 25,
     *      minMessage = "Votre prénom doit comporter au moins {{ limit }} caractères",
     *      maxMessage = "Votre prénom ne peut pas comporter plus de {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]/",
     *     message="Votre username devrait commencer par une lettre !"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9-]*$/",
     *     message="Votre username devrait contenir seulement des lettres, des chiffres et le caractère -"
     * )
     */
    private ?string $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=8,
     *     minMessage="Votre mot de passe doit faire au moins {{ limit }} caractères !",
     *     allowEmptyString = false
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[a-z])(?=.*[A-Z])/",
     *     message="Votre mot de passe devrait contenir au moin une lettre en majuscule et en minuscule !"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[0-9])/",
     *     message="Votre mot de passe devrait contenir des nombres !"
     * )
     * @Assert\Regex(
     *     pattern="/(?=.*[\W])/",
     *     message="Votre mot de passe devrait contenir au moin un caractère spécial!"
     * )
     */
    private ?string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Vous devez saisir une adresse email.")
     * @Assert\Email(message="Le format de l'adresse n'est pas correcte.")
     *  @Assert\Length(
     *      max = 255,
     *      maxMessage = "Votre adresse e-mail ne peut pas comporter plus de {{ limit }} caractères",
     *      allowEmptyString = false
     * )
     */
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     * @Assert\Choice({{User::ROLE_ADMIN},{User::ROLE_USER}}, message="Choisissez un rôle valide.")
     */
    private array $roles;

    /**
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user")
     */
    protected $tasks;

    public function __construct()
    {
        $this->roles = self::DEFAULT_ROLES;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function eraseCredentials(): void
    {
        // Do nothing because no sensitive information is stored .
    }
}
