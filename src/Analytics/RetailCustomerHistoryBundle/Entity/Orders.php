<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Entity;

use App\Analytics\RetailCustomerHistoryBundle\Repository\OrdersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrdersRepository::class)
 */
class Orders
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
    private $orderId;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $orderStatus;

    /**
     * @ORM\Column(type="string")
     */
    private $customerId;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalsumm;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $statusUpdatedAt;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $address=[];

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address;
    }

    /**
     * @param array $address
     */
    public function setAddress(array $address): void
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getStatusUpdatedAt()
    {
        return $this->statusUpdatedAt;
    }

    /**
     * @param mixed $statusUpdatedAt
     */
    public function setStatusUpdatedAt($statusUpdatedAt): void
    {
        $this->statusUpdatedAt = $statusUpdatedAt;
    }


    /**
     * @return mixed
     */
    public function getTotalsumm()
    {
        return $this->totalsumm;
    }

    /**
     * @param mixed $totalsumm
     */
    public function setTotalsumm($totalsumm): void
    {
        $this->totalsumm = $totalsumm;
    }

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $offers = [];
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * @param mixed $moduleId
     */
    public function setModuleId($moduleId): void
    {
        $this->moduleId = $moduleId;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getOrderStatus()
    {
        return $this->orderStatus;
    }

    /**
     * @param mixed $orderStatus
     */
    public function setOrderStatus($orderStatus): void
    {
        $this->orderStatus = $orderStatus;
    }

    /**
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->customerId;
    }

    /**
     * @param mixed $customerId
     */
    public function setCustomerId($customerId): void
    {
        $this->customerId = $customerId;
    }

    /**
     * @return array
     */
    public function getOffers(): array
    {
        return $this->offers;
    }

    /**
     * @param array $offers
     */
    public function setOffers(array $offers): void
    {
        $this->offers = $offers;
    }



}
