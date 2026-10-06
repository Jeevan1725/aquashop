<?php

class Cart
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }



    public function getOrCreateCart(int $userId): int
    {

        $sql = "
            SELECT id
            FROM carts
            WHERE user_id = ?
            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([$userId]);


        $cart = $stmt->fetch();



        if($cart)
        {
            return $cart['id'];
        }



        $insert = "
            INSERT INTO carts(user_id)
            VALUES(?)
        ";


        $stmt = $this->db->prepare($insert);

        $stmt->execute([$userId]);



        return $this->db->lastInsertId();

    }





    public function addItem(
        int $cartId,
        int $productId,
        int $quantity = 1
    ): bool
    {


        $check = "
            SELECT id, quantity
            FROM cart_items
            WHERE cart_id = ?
            AND product_id = ?
        ";


        $stmt = $this->db->prepare($check);

        $stmt->execute([
            $cartId,
            $productId
        ]);



        $item = $stmt->fetch();



        if($item)
        {

            $update = "
                UPDATE cart_items
                SET quantity = quantity + ?
                WHERE id = ?
            ";


            $stmt = $this->db->prepare($update);


            return $stmt->execute([
                $quantity,
                $item['id']
            ]);

        }



        $insert = "
            INSERT INTO cart_items
            (
                cart_id,
                product_id,
                quantity
            )

            VALUES
            (
                ?,
                ?,
                ?
            )
        ";


        $stmt = $this->db->prepare($insert);


        return $stmt->execute([
            $cartId,
            $productId,
            $quantity
        ]);

    }





public function getItems(int $cartId): array
{
    $sql = "
        SELECT
            ci.id AS cart_item_id,
            ci.quantity,
            p.id AS product_id,
            p.name,
            p.price,
            p.stock
        FROM cart_items ci
        INNER JOIN products p
            ON ci.product_id = p.id
        WHERE ci.cart_id = ?
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$cartId]);

    return $stmt->fetchAll();
}

public function updateQuantity(
    int $itemId,
    int $quantity
): bool
{

    $quantity = max(1, $quantity);


    $sql = "
        UPDATE cart_items
        SET quantity = ?
        WHERE id = ?
    ";


    $stmt = $this->db->prepare($sql);


    return $stmt->execute([
        $quantity,
        $itemId
    ]);

}




public function removeItem(int $itemId): bool
{

    $sql = "
        DELETE FROM cart_items
        WHERE id = ?
    ";


    $stmt = $this->db->prepare($sql);


    return $stmt->execute([
        $itemId
    ]);

}

}
