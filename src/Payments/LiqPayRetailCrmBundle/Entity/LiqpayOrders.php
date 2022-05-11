<?php

namespace App\Payments\LiqPayRetailCrmBundle\Entity;

use App\Payments\LiqPayRetailCrmBundle\Repository\LiqpayOrdersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LiqpayOrdersRepository::class)
 */
class LiqpayOrders
{
    const STATUS_UPDATE = 'update';
    const STATUS_NOTUPDATE = 'not_update';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $linkInvoice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $orderId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $paymentId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $number;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statusForUpdateRetailCrm;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $amount;

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount): void
    {
        $this->amount = $amount;
    }


    public function getStatusForUpdateRetailCrm()
    {
        return $this->statusForUpdateRetailCrm;
    }


    public function setStatusForUpdateRetailCrm($statusForUpdateRetailCrm): void
    {
        if (!in_array($statusForUpdateRetailCrm, array(self::STATUS_UPDATE, self::STATUS_NOTUPDATE))) {
            throw new \InvalidArgumentException("Invalid status");
        }
        $this->statusForUpdateRetailCrm = $statusForUpdateRetailCrm;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSite(): ?string
    {
        return $this->site;
    }

    public function setSite(?string $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function setPaymentId(?string $paymentId): self
    {
        $this->paymentId = $paymentId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getLinkInvoice(): ?string
    {
        return $this->linkInvoice;
    }

    public function setLinkInvoice(?string $linkInvoice): self
    {
        $this->linkInvoice = $linkInvoice;

        return $this;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function setOrderId(?string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
