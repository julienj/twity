<?php

namespace App\Document;

use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\BackendRepository")
 * @MongoDBUnique(fields="domain")
 */
class Backend
{
    const TYPE_GITLAB = 'gitlab';
    const TYPE_GITHUB = 'github';

    /**
     * @MongoDB\Id
     * @Groups({"backend_default", "backend_full"})
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\Choice({Backend::TYPE_GITHUB, Backend::TYPE_GITLAB})
     * @Assert\NotBlank
     * @Groups({"backend_default", "backend_full", "backend_write"})
     */
    protected $type;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank
     * @Groups({"backend_default", "backend_full", "backend_write"})
     */
    protected $domain;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank
     * @Groups({"backend_full", "backend_write"})
     */
    protected $token;

    public function getId(): string
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

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
