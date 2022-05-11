<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Entity;

use App\Analytics\RetailCustomerHistoryBundle\Repository\CustomersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CustomersRepository::class)
 */
class Customers
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $patronymic;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $phones = [];

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $address=[];

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $ordersCount;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $clientIdinRetailCrm;

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
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getPatronymic()
    {
        return $this->patronymic;
    }

    /**
     * @param mixed $patronymic
     */
    public function setPatronymic($patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return array
     */
    public function getPhones(): array
    {
        return $this->phones;
    }

    /**
     * @param array $phones
     */
    public function setPhones(array $phones): void
    {
        $this->phones = $phones;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

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
    public function getOrdersCount()
    {
        return $this->ordersCount;
    }

    /**
     * @param mixed $ordersCount
     */
    public function setOrdersCount($ordersCount): void
    {
        $this->ordersCount = $ordersCount;
    }

    /**
     * @return mixed
     */
    public function getClientIdinRetailCrm()
    {
        return $this->clientIdinRetailCrm;
    }

    /**
     * @param mixed $clientIdinRetailCrm
     */
    public function setClientIdinRetailCrm($clientIdinRetailCrm): void
    {
        $this->clientIdinRetailCrm = $clientIdinRetailCrm;
    }




}
