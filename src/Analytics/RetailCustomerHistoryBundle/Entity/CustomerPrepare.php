<?php

namespace App\Analytics\RetailCustomerHistoryBundle\Entity;

class CustomerPrepare
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $moduleId;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $patronymic;

    /**
     * @var string
     */
    private $email;

    /**
     * @var array
     */
    private $phones = [];

    /**
     * @var string
     */
    private $city;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $ordersCount;

    /**
     * @var string
     */
    private $clientIdinRetailCrm;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getModuleId(): string
    {
        return $this->moduleId;
    }

    /**
     * @param string $moduleId
     */
    public function setModuleId(string $moduleId): void
    {
        $this->moduleId = $moduleId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getPatronymic(): string
    {
        return $this->patronymic;
    }

    /**
     * @param string $patronymic
     */
    public function setPatronymic(string $patronymic): void
    {
        $this->patronymic = $patronymic;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
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
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getOrdersCount(): string
    {
        return $this->ordersCount;
    }

    /**
     * @param string $ordersCount
     */
    public function setOrdersCount(string $ordersCount): void
    {
        $this->ordersCount = $ordersCount;
    }

    /**
     * @return string
     */
    public function getClientIdinRetailCrm(): string
    {
        return $this->clientIdinRetailCrm;
    }

    /**
     * @param string $clientIdinRetailCrm
     */
    public function setClientIdinRetailCrm(string $clientIdinRetailCrm): void
    {
        $this->clientIdinRetailCrm = $clientIdinRetailCrm;
    }


}