<?php

class DbManager
{
    private PDO $db;

    /**
     * @param $config
     */
    public function connect($config)
    {
        // establish connection
        $dsn = $this->getDSN($config);
        $this->db = new PDO($dsn);

        // run "migrations"
        $this->init();
    }

    /**
     * @param string $table
     * @param array $params
     * @return string
     */
    public function insert(string $table, array $params): string
    {
        $fields = [];
        $placeholders = [];
        $values = [];
        // generate fields and values lists
        foreach ($params as $param) {
            $fields[] = $param['field'];
            $placeholders[] = ":{$param['field']}";
            $values[":${param['field']}"] = $param['value'];
        }

        $fields = implode(', ', $fields);
        $placeholders = implode(', ', $placeholders);
        $sql = "INSERT INTO $table($fields) VALUES($placeholders)";

        $query = $this->db->prepare($sql);
        $query->execute($values);

        return $this->db->lastInsertId();
    }

    /**
     * @param string $table
     * @param int $id
     * @param array $params
     * @param string $idColumn
     */
    public function update(string $table, int $id, array $params, $idColumn = 'id')
    {
        $fields = [];
        $values = [];
        // generate update list fields
        foreach ($params as $param) {
            $fields[] = "{$param['field']} = :{$param['field']}";
            $values[":${param['field']}"] = $param['value'];
        }

        $fields = implode(', ', $fields);
        $sql = "UPDATE $table SET $fields WHERE $idColumn = $id";
        $query = $this->db->prepare($sql);

        $query->execute($values);
    }

    /**
     * @param string $table
     * @param string|null $where
     * @param array $params
     * @return array
     */
    public function search(string $table, string $where = null, array $params = [])
    {
        // find table records with filters
        $where = $where ? "WHERE {$where}" : '';
        $query = $this->db->prepare("SELECT * FROM $table $where");
        $query->execute($params);

        return $query->fetchAll();
    }

    /**
     * @param array $dbConfig
     * @return string
     */
    private function getDSN(array $dbConfig)
    {
        return "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};user={$dbConfig['user']};password={$dbConfig['password']}";
    }

    private function init()
    {
        // create books table if does not exist
        $query =
            "CREATE TABLE IF NOT EXISTS books 
(id serial PRIMARY KEY, 
title text NOT NULL, 
author text NOT NULL, 
created timestamp NOT NULL, 
file text NOT NULL)";

        $this->db->exec($query);

        $query = "CREATE INDEX books_author_index ON books (author);";

        $this->db->exec($query);
    }
}
