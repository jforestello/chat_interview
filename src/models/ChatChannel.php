<?php

namespace App\models;

class ChatChannel implements iModel {
    public const TABLE_NAME = "chat_channels";

    /* @var int */
    private $id;
    /* @var int */
    private $user_from;
    /* @var int */
    private $user_to;

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return ChatChannel
     */
    public function setId(int $id) : ChatChannel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserFrom() : ?int
    {
        return $this->user_from;
    }

    /**
     * @param int $user_from
     * @return ChatChannel
     */
    public function setUserFrom(int $user_from) : ChatChannel
    {
        $this->user_from = $user_from;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserTo() : ?int
    {
        return $this->user_to;
    }

    /**
     * @param int $user_to
     * @return ChatChannel
     */
    public function setUserTo(int $user_to) : ChatChannel
    {
        $this->user_to = $user_to;

        return $this;
    }
}