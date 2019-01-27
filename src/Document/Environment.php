<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 */
class Environment extends AbstractUser
{
    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"environment_default", "environment_write", "environment_full"})
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Application", inversedBy="environments")
     */
    protected $application;

    public function getRoles(): array
    {
        return [static::ROLE_USER];
    }

    public function getUsername(): string
    {
        return $this->id;
    }

    public function getPassword()
    {
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getApplication(): Application
    {
        return $this->application;
    }

    public function setApplication(Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}
