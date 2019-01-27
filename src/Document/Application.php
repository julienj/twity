<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\ApplicationRepository")
 */
class Application
{
    /**
     * @MongoDB\Id
     * @Groups({"application_default", "application_full"})
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"application_default", "application_write", "application_full"})
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     *
     * @Groups({"application_default", "application_write", "application_full"})
     * @Assert\NotBlank
     */
    protected $description;

    /**
     * @MongoDB\ReferenceMany(targetDocument="Environment", mappedBy="application", orphanRemoval=true)
     *
     * @var Environment[]
     */
    protected $environments;

    /**
     * @MongoDB\EmbedMany(targetDocument="Access")
     *
     * @var Access[]
     */
    protected $accesses;

    public function __construct()
    {
        $this->accesses = new ArrayCollection();
        $this->environments = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Environment[]
     */
    public function getEnvironments()
    {
        return $this->environments;
    }

    /**
     * @return Access[]
     */
    public function getAccesses()
    {
        return $this->accesses;
    }

    public function addAccess(Access $access)
    {
        $this->removeAccess($access->getUser()->getId());
        $this->accesses->add($access);
    }

    public function getAccess(string $userId)
    {
        foreach ($this->accesses as $item) {
            if ($item->getUser()->getId() === $userId) {
                return $item;
            }
        }
    }

    public function removeAccess(string $userId)
    {
        if ($access = $this->getAccess($userId)) {
            $this->accesses->removeElement($access);
        }
    }
}
