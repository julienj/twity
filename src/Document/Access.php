<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\EmbeddedDocument
 */
class Access
{
    const ACCESS_OWNER = 'owner';
    const ACCESS_MASTER = 'master';
    const ACCESS_USER = 'user';

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"access_default", "access_full", "access_write"})
     * @Assert\Choice({Access::ACCESS_OWNER, Access::ACCESS_MASTER, Access::ACCESS_USER})
     * @Assert\NotBlank
     */
    private $access;

    /**
     * @MongoDB\ReferenceOne(targetDocument="User")
     * @Groups({"access_default", "access_full"})
     * @Assert\NotBlank
     */
    private $user;

    public function getAccess(): string
    {
        return $this->access;
    }

    public function setAccess(string $access): self
    {
        $this->access = $access;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
