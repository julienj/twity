<?php

namespace App\Document;

use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document(repositoryClass="App\Repositories\ProviderRepository")
 * @MongoDBUnique(fields="name")
 * @MongoDB\HasLifecycleCallbacks
 */
class Provider
{
    const TYPE_COMPOSER = 'composer';
    const TYPE_VCS = 'vcs';

    /**
     * @MongoDB\Id(strategy="NONE", type="string")
     * @Groups({"provider_default", "provider_full", "provider_write"})
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^.*\/.*$/", message="The name of the package. It consists of vendor name and project name, separated by /")
     */
    protected $name;

    /**
     * @MongoDB\Field(type="string")
     * @MongoDB\Index()
     *
     * @Groups({"provider_default", "provider_full", "provider_write"})
     * @Assert\Choice({Provider::TYPE_COMPOSER, Provider::TYPE_VCS})
     * @Assert\NotBlank
     */
    protected $type;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"provider_default", "provider_full", "provider_write"})
     * @Assert\Url
     */
    protected $vcsUri;

    /**
     * @MongoDB\Field(type="date")
     * @Groups({"provider_default", "provider_full"})
     */
    protected $lastUpdate;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"provider_default", "provider_full"})
     */
    protected $description;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $sha256;

    /**
     * @MongoDB\Field(type="boolean")
     * @Groups({"provider_default", "provider_full"})
     */
    protected $updateInProgress;

    /**
     * @MongoDB\Field(type="string")
     * @Groups({"provider_full"})
     */
    protected $logs;

    /**
     * @MongoDB\Field(type="boolean")
     * @Groups({"provider_full"})
     */
    protected $hasError;

    /**
     * @MongoDB\Field(type="int", strategy="increment")
     * @Groups({"provider_default", "provider_full"})
     */
    protected $downloads;

    /**
     * @MongoDB\EmbedMany(targetDocument="Package")
     * @Groups({"provider_full"})
     */
    private $packages;

    public function __construct()
    {
        $this->packages = new ArrayCollection();
        $this->updateInProgress = false;
        $this->downloads = 0;
        $this->hasError = false;
    }

    /**
     * @MongoDB\PreFlush
     */
    public function onFlush()
    {
        $this->setLastUpdate(new \DateTime());
        $this->setSha256(hash('sha256', json_encode($this->getData())));

        if ($lastpackage = $this->packages->last()) {
            if (isset($lastpackage->getData()['description'])) {
                $this->setDescription($lastpackage->getData()['description']);
            }
        }
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getVcsUri(): ?string
    {
        return $this->vcsUri;
    }

    public function setVcsUri(string $vcsUri): self
    {
        $this->vcsUri = $vcsUri;

        return $this;
    }

    public function getLastUpdate(): ?\DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTime $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getSha256(): string
    {
        return $this->sha256;
    }

    public function setSha256(string $sha256): self
    {
        $this->sha256 = $sha256;

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

    public function getUpdateInProgress(): bool
    {
        return $this->updateInProgress;
    }

    public function setUpdateInProgress(bool $updateInProgress): self
    {
        $this->updateInProgress = $updateInProgress;

        return $this;
    }

    public function getLogs(): ?string
    {
        return $this->logs;
    }

    public function setLogs(?string $logs): self
    {
        $this->logs = $logs;

        return $this;
    }

    public function getHasError(): bool
    {
        return $this->hasError;
    }

    public function setHasError(bool $hasError): self
    {
        $this->hasError = $hasError;

        return $this;
    }

    public function getDownloads(): ?int
    {
        return $this->downloads;
    }

    public function setDownloads(int $downloads): self
    {
        $this->downloads = $downloads;

        return $this;
    }

    public function getPackages(): Collection
    {
        return $this->packages;
    }

    public function addPackage(Package $packages): self
    {
        $this->packages->add($packages);

        return $this;
    }

    public function removePackage(Package $packages): self
    {
        $this->packages->remove($packages);

        return $this;
    }

    public function clearPackages(): self
    {
        $this->packages->clear();

        return $this;
    }

    public function getData(): array
    {
        $data = [];
        /** @var Package $package */
        foreach ($this->getPackages() as $package) {
            $data[$package->getVersion()] = $package->getData();
        }

        return ['packages' => [$this->getName() => $data]];
    }
}
