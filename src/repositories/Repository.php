<?php

namespace App\repositories;

use App\database\Database;
use App\models\iModel;

class Repository {
    private $connector;
    protected $table;
    protected $fields;
    protected $currentClass;

    public function __construct(Database $database)
    {
        $this->connector = $database;
    }

    /**
     * @param iModel $model
     * @param array $filters
     * @return Repository
     * @throws \Exception
     */
    public function save(iModel $model, array $filters) : Repository {
        $where = "";
        $setters = "";
        $data = $this->modelToArray($model);
        foreach ($data as $field => $value) {
            $value = is_string($value) ? $this->connector->escapeString($value) : $value;
            if (in_array($field, $filters)) {
                $where .= empty($where) ? "{$field} = '{$value}'" : " AND {$field} = '{$value}'";
                continue;
            }

            $setters .= empty($setters) ? "{$field} = '{$value}'" : ", {$field} = '{$value}'";
        }

        $this->transaction(Database::BEGIN);
        $query = <<<SQL
        UPDATE {$this->table} SET {$setters} WHERE {$where};
SQL;
        $succeeded = $this->connector->query($query);
        if (!$succeeded) {
            $this->transaction(Database::ROLLBACK);
            throw new \Exception($this->connector->lastErrorMsg(), $this->connector->lastErrorCode());
        }

        $this->transaction(Database::COMMIT);
        return $this;
    }

    /**
     * @param iModel $model
     * @return Repository
     * @throws \Exception
     */
    public function create(iModel $model) : Repository {
        $data = $this->modelToArray($model);
        unset($data["id"]);
        $fields = implode(",", array_keys($data));
        $values = implode("', '", array_map(function ($value) {
            return is_string($value) ? $this->connector->escapeString($value) : $value;
        }, $data));

        $query = <<<SQL
        INSERT INTO {$this->table} ({$fields}) VALUES ('{$values}');
SQL;

        $this->transaction(Database::BEGIN);
        $succeeded = $this->connector->exec($query);
        if (!$succeeded) {
            $this->transaction(Database::ROLLBACK);
            throw new \Exception($this->connector->lastErrorMsg(), $this->connector->lastErrorCode());
        }

        $this->transaction(Database::COMMIT);
        return $this;
    }

    /**
     * @param iModel $model
     * @return Repository
     * @throws \Exception
     */
    public function delete(iModel $model) : Repository {
        $query = <<<SQL
        DELETE FROM {$this->table} WHERE id = {$model->getId()}
SQL;
        $this->transaction(Database::BEGIN);
        $succeeded = $this->connector->exec($query);
        if (!$succeeded) {
            $this->transaction(Database::ROLLBACK);
            throw new \Exception($this->connector->lastErrorMsg(), $this->connector->lastErrorCode());
        }

        $this->transaction(Database::COMMIT);
        return $this;
    }

    private function fetch(array $filters, array $order = [], string $limit = "") : array {
        $where = "";
        foreach ($filters as $field => $value) {
            $optionalFields = explode("|", $field);
            if (empty($where)) {
                $where = "WHERE ";
            } else {
                $where .= " AND ";
            }
            if (count($optionalFields) > 1) {
                $where .= "(";
                $first = true;
                foreach ($optionalFields as $optionalField) {
                    if (! $first) {
                        $where .= " OR ";
                    }
                    if (is_array($value)) {
                        $where .= "{$optionalField} IN ('".implode("','", $value)."')";
                    }else {
                        $filter = is_string($value) ? $this->connector->escapeString($value) : $value;
                        $where .= "{$optionalField} = '{$filter}'";
                    }
                    $first = false;
                }
                $where .= ")";
            } else {
                if (is_array($value)) {
                    $where .= "{$field} IN ('".implode("','", $value)."')" . ($first ? "" : ")");
                }else {
                    $filter = is_string($value) ? $this->connector->escapeString($value) : $value;
                    $where .= "{$field} = '{$filter}'";
                }
            }
        }
        $sort = "";
        foreach ($order as $field => $type) {
            if (empty($sort)) {
                $sort = "ORDER BY ";
            } else {
                $sort .= ", ";
            }
            $sort .= $field . $type;
        }
        $fields = implode(",", $this->fields);
        $query = <<<SQL
        SELECT {$fields} FROM {$this->table} {$where} {$sort} {$limit}
SQL;

        $result = $this->connector->query($query);
        return $this->parseResult($result);
    }

    /**
     * @param array $params
     * @param array $order
     * @return iModel
     */
    public function fetchOne(array $params, array $order = []) : ?iModel {
        $response = $this->fetch($params, $order, "LIMIT 1");
        return ! empty($response) ? $response[0] : null;
    }

    /**
     * @param array $order
     * @return iModel[]
     */
    public function fetchAll(array $order = []) : array {
        return $this->fetch([], $order);
    }

    /**
     * @param array $params
     * @param array $order
     * @return iModel[]
     */
    public function fetchMany(array $params, array $order = []) : array {
        return $this->fetch($params, $order);
    }

    /**
     * @param \SQLite3Result $data
     * @return iModel[]
     */
    private function parseResult(\SQLite3Result $data): array
    {
        if (! $data) {
            return [];
        }
        $response = [];
        while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
            $response[] = $this->arrayToModel($row);
        }
        return $response;
    }

    private function modelToArray(iModel $model) : array {
        $response = [];
        foreach ($this->fields as $field) {
            $getter = "get".str_replace("_", "", ucwords($field, "_"));
            $response[$field] = $model->$getter();
        }
        return $response;
    }

    private function arrayToModel(array $row) : iModel {
        $model = new $this->currentClass();
        foreach ($this->fields as $field) {
            $setter = "set".str_replace("_", "", ucwords($field, "_"));
            $model->$setter($row[$field]);
        }
        return $model;
    }

    private function transaction(string $command) : void {
        $this->connector->exec($command);
    }
}