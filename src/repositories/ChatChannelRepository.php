<?php

namespace App\repositories;

use App\models\ChatChannel;

/**
 * @method fetchOne(array $params, array $order = []) : ?ChatChannel
 * @method fetchMany(array $params, array $order = []) : ChatChannel[]
 * @method fetchAll(array $order = []) : ChatChannel[]
 * @method save(ChatChannel $user, array $filters) : ChatChannelRepository
 * @method create(ChatChannel $user) : ChatChannelRepository
 * @method delete(ChatChannel $user) : ChatChannelRepository
 */
class ChatChannelRepository extends Repository {
    protected $table = ChatChannel::TABLE_NAME;
    protected $fields = ["id", "user_from", "user_to"];
    protected $currentClass = ChatChannel::class;
}