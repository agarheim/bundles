<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingsRepository::class)
 */
class Settings
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $moduleId;

    /**
     * @ORM\Column(type="string")
     */
    private $clientId;

    /**
     * @ORM\Column(type="string")
     */
    private $variantId;

    /**
     * @ORM\Column(type="json")
     */
    private $settings = [];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ConnectionId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleId(): ?string
    {
        return $this->moduleId;
    }

    public function setModuleId(string $moduleId): self
    {
        $this->moduleId = $moduleId;

        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getVariantId(): ?string
    {
        return $this->variantId;
    }

    public function setVariantId(string $variantId): self
    {
        $this->variantId = $variantId;

        return $this;
    }

    public function getSettings(): ?array
    {
        return $this->settings;
    }

    public function setSettings($settings): self
    {
        $this->settings = $settings;

        return $this;
    }

    public function getConnectionId(): ?int
    {
        return $this->ConnectionId;
    }

    public function setConnectionId(?int $ConnectionId): self
    {
        $this->ConnectionId = $ConnectionId;

        return $this;
    }
}
