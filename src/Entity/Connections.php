<?php

namespace App\Entity;

use App\Repository\ConnectionsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConnectionsRepository::class)
 */
class Connections
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ApiKey;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $LoginUrl;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hash;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $UserId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->ApiKey;
    }

    public function setApiKey(string $ApiKey): self
    {
        $this->ApiKey = $ApiKey;

        return $this;
    }

    public function getLoginUrl(): ?string
    {
        return $this->LoginUrl;
    }

    public function setLoginUrl(?string $LoginUrl): self
    {
        $this->LoginUrl = $LoginUrl;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(?string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->UserId;
    }

    public function setUserId(string $UserId): self
    {
        $this->UserId = $UserId;

        return $this;
    }
}
