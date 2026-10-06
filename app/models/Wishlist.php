<?php

class Wishlist
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    /* =========================
       ADD PRODUCT
    ========================= */

    public function add(
        int $userId,
        int $productId
    ): bool
    {
        $sql = "
            INSERT IGNORE INTO wishlists
            (
                user_id,
                product_id
            )
            VALUES
            (
                ?,
                ?
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $userId,
            $productId
        ]);
    }


    /* =========================
       REMOVE PRODUCT
    ========================= */

    public function remove(
        int $userId,
        int $productId
    ): bool
    {
        $sql = "
            DELETE FROM wishlists
            WHERE user_id = ?
            AND product_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $userId,
            $productId
        ]);
    }


    /* =========================
       CHECK WISHLIST
    ========================= */

    public function exists(
        int $userId,
        int $productId
    ): bool
    {
        $sql = "
            SELECT id
            FROM wishlists
            WHERE user_id = ?
            AND product_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $userId,
            $productId
        ]);

        return (bool)$stmt->fetchColumn();
    }


    /* =========================
       GET USER WISHLIST
    ========================= */

    public function getByUser(
        int $userId
    ): array
    {
        $sql = "
            SELECT
                w.id AS wishlist_id,
                w.created_at,

                p.id,
                p.name,
                p.slug,
                p.description,
                p.price,
                p.stock,
                p.image,

                c.name AS category

            FROM wishlists w

            INNER JOIN products p
                ON w.product_id = p.id

            INNER JOIN categories c
                ON p.category_id = c.id

            WHERE w.user_id = ?

            ORDER BY w.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $userId
        ]);

        return $stmt->fetchAll();
    }
}
