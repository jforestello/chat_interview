<?php

namespace App\repositories;

use App\models\Message;

/**
 * @method fetchOne(array $params, array $order = []) : ?Message
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

    public function fetchNew(int $channel_id, int $loggedId) : array {
        $query = <<<SQL
        SELECT message FROM messages WHERE channel_id = '{$this->getConnector()->escapeString($channel_id)}' AND creator_id != {$loggedId} AND seen = 0
SQL;
        $data = $this->getConnector()->query($query);
        $response = [];
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $row;
        }

        $query = <<<SQL
        UPDATE messages SET seen = 1 WHERE channel_id = '{$this->getConnector()->escapeString($channel_id)}' AND creator_id != {$loggedId} AND seen = 0
SQL;
        $this->getConnector()->exec($query);

        return $response;
    }
}