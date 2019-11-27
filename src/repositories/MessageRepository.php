<?php

namespace App\repositories;

use App\models\Message;

/**
 * @method fetchOne(array $params, array $order = []) : Message
 * @method fetchMany(array $params, array $order = []) : Message[]
 * @method fetchAll(array $order = []) : Message[]
 * @method save(Message $user, array $filters) : MessageRepository
 * @method create(Message $user) : MessageRepository
 * @method delete(Message $user) : MessageRepository
 */
class MessageRepository extends Repository {
    protected $table = Message::TABLE_NAME;
    protected $fields = ["id", "seen", "message", "creator_id", "channel_id"];
    protected $currentClass = Message::class;
}