<?php

class Category
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function getAll(): array
    {
        $query = "
            SELECT id, name
            FROM categories
            ORDER BY name ASC
        ";

        $stmt = $this->db->query($query);

        return $stmt->fetchAll();
    }
}
