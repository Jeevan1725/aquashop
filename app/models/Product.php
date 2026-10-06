<?php

class Product
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(
        ?string $search = null,
        ?int $category = null
    ): array
    {
        /*
        |--------------------------------------------------------------------------
        | VULNERABLE search — for demo only
        |--------------------------------------------------------------------------
        */
        if (is_vulnerable() && $search !== null && $search !== '') {

            $sql = "
                SELECT
                    p.id,
                    p.name,
                    p.slug,
                    p.description,
                    p.price,
                    p.stock,
                    p.image,
                    c.name AS category

                FROM products p

                INNER JOIN categories c
                    ON p.category_id = c.id

                WHERE p.status = 'active'
                  AND p.name LIKE '%$search%'
            ";

            if ($category) {
                $sql .= " AND p.category_id = $category";
            }

            $sql .= " ORDER BY p.created_at DESC";

            try {
                $stmt = $this->db->query($sql);
                return $stmt->fetchAll();
            } catch (PDOException $e) {
                // VULNERABLE: leak error
                die("SQL Error: " . $e->getMessage());
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SECURE search (prepared statements)
        |--------------------------------------------------------------------------
        */
        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.description,
                p.price,
                p.stock,
                p.image,
                c.name AS category

            FROM products p

            INNER JOIN categories c
                ON p.category_id = c.id

            WHERE p.status = 'active'
        ";

        $params = [];

        if ($search) {

            $sql .= "
                AND p.name LIKE ?
            ";

            $params[] = "%" . $search . "%";
        }

        if ($category) {

            $sql .= "
                AND p.category_id = ?
            ";

            $params[] = $category;
        }

        $sql .= "
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute($params);

        return $stmt->fetchAll();
    }


    public function getBySlug(string $slug): ?array
    {
        $sql = "
            SELECT
                p.*,
                c.name AS category

            FROM products p

            INNER JOIN categories c
                ON p.category_id = c.id

            WHERE p.slug = ?

            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$slug]);

        $product = $stmt->fetch();

        return $product ?: null;
    }


    public function getFeaturedProducts(int $limit = 8): array
    {
        $limit = max(1, min($limit, 20));

        $sql = "
            SELECT
                p.id,
                p.name,
                p.slug,
                p.description,
                p.price,
                p.stock,
                p.image,
                c.name AS category

            FROM products p

            INNER JOIN categories c
                ON p.category_id = c.id

            WHERE p.status = 'active'

            ORDER BY p.created_at DESC

            LIMIT $limit
        ";

        $stmt = $this->db->query($sql);

        return $stmt->fetchAll();
    }
}
