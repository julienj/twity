<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @MongoDB\MappedSuperclass
 * @MongoDB\HasLifecycleCallbacks
 */
abstract class AbstractUser implements UserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_USER = 'ROLE_USER';

    /**
     * @MongoDB\Id
     * @Groups({"user_default", "user_full", "user_autocomplete", "environment_default", "environment_full"})
     */
    protected $id;

    /**
     * @MongoDB\Field(type="date")
     *
     * @Groups({"user_default", "user_full", "user_profile", "environment_default", "environment_full"})
     */
    protected $lastLogin;

    /**
     * @MongoDB\Field(type="date")
     *
     * @var \DateTime
     *
     * @Groups({"user_default", "user_full", "user_profile", "environment_default", "environment_full"})
     */
    protected $createdAt;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"user_profile", "environment_full"})
     */
    protected $token;

    abstract public function getRoles();

    abstract public function getUsername();

    abstract public function getPassword();

    /**
     * @MongoDB\PrePersist
     */
    public function onPersist()
    {
        $this
            ->setCreatedAt(new \DateTime())
            ->generateToken();
    }

    public function generateToken(): self
    {
        $this->setToken(sha1(random_bytes(50)));

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTime $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
