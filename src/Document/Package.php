<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @MongoDB\EmbeddedDocument
 */
class Package
{
    /**
     * @MongoDB\Field(type="string")
     * @Groups({"package_default"})
     */
    protected $version;

    /**
     * @MongoDB\Field(type="hash")
     * @Groups({"package_default"})
     *
     * @var string[]
     */
    protected $data;

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }
}
