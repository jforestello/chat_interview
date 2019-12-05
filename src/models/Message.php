<?php

namespace App\models;


class Message implements iModel {
    public const TABLE_NAME = "messages";

    /* @var int */
    private $id;
    /* @var int */
    private $seen;
    /* @var string */
    private $message;
    /* @var int */
    private $creator_id;
    /* @var int */
    private $channel_id;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Message
     */
    public function setId(int $id): Message
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getSeen(): ?int
    {
        return $this->seen;
    }

    /**
     * @param int $seen
     * @return Message
     */
    public function setSeen(int $seen): Message
    {
        $this->seen = $seen;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string $message
     * @return Message
     */
    public function setMessage(string $message): Message
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatorId(): ?int
    {
        return $this->creator_id;
    }

    /**
     * @param int $creator_id
     * @return Message
     */
    public function setCreatorId(int $creator_id): Message
    {
        $this->creator_id = $creator_id;

        return $this;
    }

    /**
     * @return int
     */
    public function getChannelId(): ?int
    {
        return $this->channel_id;
    }

    /**
     * @param int $channel_id
     * @return Message
     */
    public function setChannelId(int $channel_id): Message
    {
        $this->channel_id = $channel_id;

        return $this;
    }
}