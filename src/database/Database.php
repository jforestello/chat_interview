<?php

namespace App\database;

class Database extends \SQLite3 {

    public const BEGIN = "BEGIN TRANSACTION";
    public const COMMIT = "END TRANSACTION";
    public const ROLLBACK = "ROLLBACK";

    public function __construct()
    {
        parent::__construct(DOCUMENT_ROOT.DIR_SEPARATOR."bootstrap\\database.db");
        $this->load();
    }

    public function load() : self {
        $queries = <<<SQL
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS chat_channels (
        id INTEGER PRIMARY KEY,
        user_from INTEGER,
        user_to INTEGER,
        UNIQUE (user_from, user_to),
        FOREIGN KEY (user_from)
            REFERENCES users (id)
                ON DELETE CASCADE
                ON UPDATE NO ACTION,
        FOREIGN KEY (user_to)
            REFERENCES users (id)
                ON DELETE CASCADE
                ON UPDATE NO ACTION
    );

    CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY,
        seen INTEGER KEY,
        message TEXT NULL,
        creator_id INTEGER KEY,
        channel_id INTEGER KEY,
        FOREIGN KEY (creator_id)
            REFERENCES users (id)
                ON DELETE CASCADE
                ON UPDATE NO ACTION,
        FOREIGN KEY (channel_id)
            REFERENCES chat_channels (id)
                ON DELETE CASCADE
                ON UPDATE NO ACTION
    );
SQL;

        $this->query($queries);
        return $this;
    }
}