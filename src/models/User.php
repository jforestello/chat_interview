<?php

namespace App\models;

class User implements iModel {
    public const TABLE_NAME = "users";

    /* @var int */
    private $id;
    /* @var string */
    private $first_name;
    /* @var string */
    private $last_name;
    /* @var string */
    private $email;
    /* @var string */
    private $password;

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return User
     */
    public function setId(int $id) : User
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName() : ?string
    {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     * @return User
     */
    public function setFirstName(string $first_name) : User
    {
        $this->first_name = $first_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getLastName() : ?string
    {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     * @return User
     */
    public function setLastName(string $last_name) : User
    {
        $this->last_name = $last_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email) : User
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword() : ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password) : User
    {
        $this->password = $password;

        return $this;
    }
}