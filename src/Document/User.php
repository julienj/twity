<?php

namespace App\Document;

use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\UserRepository")
 * @MongoDBUnique(fields="username")
 * @MongoDBUnique(fields="email")
 */
class User extends AbstractUser
{
    const TYPE_INTERNAL = 'internal';
    const TYPE_GITLAB = 'gitlab';

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"user_profile", "user_write", "user_default", "user_full", "user_autocomplete"})
     * @Assert\NotBlank
     */
    protected $username;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"user_profile", "user_write", "user_default", "user_full", "user_autocomplete"})
     * @Assert\NotBlank
     * @Assert\Email
     */
    protected $email;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"user_profile", "user_write", "user_default", "user_full", "user_autocomplete"})
     * @Assert\NotBlank
     */
    protected $fullName;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"user_profile", "user_default", "user_full", "user_autocomplete"})
     */
    protected $avatarUrl;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"user_default", "user_full"})
     * @Assert\Choice({User::TYPE_INTERNAL, User::TYPE_GITLAB})
     * @Assert\NotBlank
     */
    protected $type;

    /**
     * @Assert\Length(min=8, groups={"password"})
     * @Assert\Regex(
     *     "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]|.*[\?!:$@&_#\*\-\+]).*$/", groups={"password"},
     *     message="Your password must be have at least 8 characters, 1 uppercase & 1 lowercase character, 1 number and 1 special character."
     * )
     * @Assert\NotBlank(groups={"password"})
     */
    protected $plainPassword;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $password;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $resetPasswordToken;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"user_write", "user_default", "user_full", "user_profile"})
     * @Assert\Choice({User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_USER})
     * @Assert\NotBlank
     */
    protected $role;

    public function __construct()
    {
        $this->role = self::ROLE_USER;
        $this->type = self::TYPE_INTERNAL;
    }

    /**
     * @Groups({"user_profile"})
     */
    public function getRoles()
    {
        $roles = [$this->getRole()];

        if (in_array(self::ROLE_ADMIN, $roles)) {
            $roles[] = self::ROLE_MANAGER;
        }

        if (in_array(self::ROLE_MANAGER, $roles)) {
            $roles[] = self::ROLE_USER;
        }

        return $roles;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

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

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(string $resetPasswordToken): self
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }
}
