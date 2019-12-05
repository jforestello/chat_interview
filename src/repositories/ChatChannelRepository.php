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

    public function fetchManyWithLastMessage(int $id) : array {
        $query = <<<SQL
        SELECT
        chat_channels.id chat_id,
        u.first_name first_name,
        u.last_name last_name,
        substr(u.first_name, 1, 1) || substr(u.last_name, 1, 1) acronym
        FROM chat_channels
        JOIN users u ON u.id != {$id} AND (u.id = chat_channels.user_from OR u.id = chat_channels.user_to)
        WHERE chat_channels.user_from = {$id} OR chat_channels.user_to = {$id}
        GROUP BY chat_channels.id
        ORDER BY chat_Channels.id DESC
SQL;
        $connector = $this->getConnector();
        $response = $connector->query($query);
        $data = [];
        while ($row = $response->fetchArray(SQLITE3_ASSOC)) {
            $data[$row['chat_id']] = $row;
        }

        $chats = implode(',', array_keys($data));
        $query = <<<SQL
        SELECT message last_message,
        seen is_seen,
        CASE creator_id WHEN {$id} THEN 1 ELSE 0 END owner_user,
        channel_id chat_id
        FROM messages
        WHERE id in (SELECT max(id) FROM messages WHERE channel_id in ({$chats}) group by channel_id)
        ORDER BY channel_id DESC
SQL;
        $response = $connector->query($query);
        while ($row = $response->fetchArray(SQLITE3_ASSOC)) {
            $data[$row['chat_id']] = array_merge($data[$row['chat_id']], $row);
        }

        return $data;
    }

    public function fetchAvailableUsers(int $userId) : array
    {
        $query = <<<SQL
        SELECT u.id, u.first_name, u.last_name FROM users u WHERE u.id != {$userId} and u.id NOT IN (SELECT case cc.user_from when {$userId} then cc.user_to else cc.user_from end from chat_channels cc WHERE cc.user_from = {$userId} OR cc.user_to = {$userId})
SQL;
        $data = $this->getConnector()->query($query);
        $response = [];
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $row;
        }

        return $response;
    }

    public function fetchChat(int $chat, int $loggedId) : array
    {
        $query = <<<SQL
        SELECT cc.id chatId, u.first_name || ' ' || u.last_name name, substr(u.first_name, 1, 1) || substr(u.last_name, 1, 1) acronym FROM users u JOIN chat_channels cc ON cc.user_from = u.id OR cc.user_to = u.id WHERE u.id != {$loggedId} AND cc.id = {$chat}
SQL;

        $data = $this->getConnector()->query($query);
        $response = $data->fetchArray(SQLITE3_ASSOC);
        $response['messages'] = [];

        $query = <<<SQL
        SELECT message, CASE creator_id WHEN {$loggedId} THEN 1 ELSE 0 END owner_user FROM messages WHERE channel_id = {$chat} ORDER BY id ASC
SQL;
        $data = $this->getConnector()->query($query);
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $response['messages'][] = $row;
        }

        $query = <<<SQL
        UPDATE messages SET seen = 1 WHERE channel_id = {$chat} AND creator_id != {$loggedId}
SQL;
        $this->getConnector()->exec($query);

        return $response;
    }
}