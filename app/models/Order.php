<?php

class Order
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function createOrder(
        int $userId,
        int $addressId,
        array $items
    ): int {

        if (empty($items)) {
            throw new Exception("Cart is empty.");
        }


        try {

            $this->db->beginTransaction();


            /*
             * Verify address ownership.
             */

            $stmt = $this->db->prepare("
                SELECT id
                FROM addresses
                WHERE id = ?
                  AND user_id = ?
                LIMIT 1
            ");

            $stmt->execute([
                $addressId,
                $userId
            ]);


            if (!$stmt->fetch()) {
                throw new Exception("Invalid delivery address.");
            }


            $subtotal = 0;


            /*
             * Lock each product row while
             * checking and updating stock.
             */

            foreach ($items as $item) {

                $productId = (int)$item['product_id'];
                $quantity = (int)$item['quantity'];


                if ($quantity <= 0) {
                    throw new Exception("Invalid quantity.");
                }


                $stmt = $this->db->prepare("
                    SELECT
                        id,
                        name,
                        price,
                        stock
                    FROM products
                    WHERE id = ?
                      AND status = 'active'
                    FOR UPDATE
                ");

                $stmt->execute([
                    $productId
                ]);


                $product = $stmt->fetch();


                if (!$product) {
                    throw new Exception(
                        "Product is no longer available."
                    );
                }


                /*
                 * Check available stock.
                 */

                if ((int)$product['stock'] < $quantity) {

                    throw new Exception(
                        "Insufficient stock for " .
                        $product['name']
                    );
                }


                /*
                 * Always use the current
                 * database price.
                 */

                $subtotal +=
                    (float)$product['price'] *
                    $quantity;


                /*
                 * Deduct stock.
                 */

                $stmt = $this->db->prepare("
                    UPDATE products
                    SET stock = stock - ?
                    WHERE id = ?
                      AND stock >= ?
                ");

                $stmt->execute([
                    $quantity,
                    $productId,
                    $quantity
                ]);


                if ($stmt->rowCount() !== 1) {

                    throw new Exception(
                        "Unable to update inventory."
                    );
                }
            }


            /*
             * Calculate order totals.
             */

            $deliveryFee = 50.00;
            $tax = 0.00;
            $discount = 0.00;


            $total =
                $subtotal +
                $deliveryFee +
                $tax -
                $discount;


            /*
             * Generate unique order number.
             */

            $orderNumber =
                'AQ-' .
                date('YmdHis') .
                '-' .
                random_int(1000, 9999);


            /*
             * Create order.
             *
             * New orders always start
             * with pending status.
             */

            $stmt = $this->db->prepare("
                INSERT INTO orders
                (
                    user_id,
                    address_id,
                    order_number,
                    subtotal,
                    delivery_fee,
                    tax,
                    discount,
                    total,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");


            $stmt->execute([
                $userId,
                $addressId,
                $orderNumber,
                $subtotal,
                $deliveryFee,
                $tax,
                $discount,
                $total
            ]);


            $orderId =
                (int)$this->db->lastInsertId();


            /*
             * Create initial order status history.
             *
             * NULL -> pending means the order
             * has just been placed.
             */

            $stmt = $this->db->prepare("
                INSERT INTO order_status_history
                (
                    order_id,
                    old_status,
                    new_status,
                    changed_by
                )
                VALUES (?, ?, ?, ?)
            ");


            $stmt->execute([
                $orderId,
                null,
                'pending',
                null
            ]);


            /*
             * Create order items.
             */

            $stmt = $this->db->prepare("
                INSERT INTO order_items
                (
                    order_id,
                    product_id,
                    product_name,
                    quantity,
                    unit_price,
                    subtotal
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");


            foreach ($items as $item) {

                /*
                 * Get current product information.
                 */

                $productStmt = $this->db->prepare("
                    SELECT
                        name,
                        price
                    FROM products
                    WHERE id = ?
                ");


                $productStmt->execute([
                    $item['product_id']
                ]);


                $product = $productStmt->fetch();


                if (!$product) {
                    throw new Exception(
                        "Product not found."
                    );
                }


                $itemSubtotal =
                    (float)$product['price'] *
                    (int)$item['quantity'];


                $stmt->execute([
                    $orderId,
                    $item['product_id'],
                    $product['name'],
                    $item['quantity'],
                    $product['price'],
                    $itemSubtotal
                ]);
            }


            /*
             * Create payment record.
             *
             * This is currently a sandbox
             * payment record.
             */

            $stmt = $this->db->prepare("
                INSERT INTO payments
                (
                    order_id,
                    gateway,
                    gateway_payment_id,
                    amount,
                    status
                )
                VALUES (?, ?, ?, ?, 'created')
            ");


            $stmt->execute([
                $orderId,
                'sandbox',
                null,
                $total
            ]);


            /*
             * Commit the complete order.
             */

            $this->db->commit();


            return $orderId;


        } catch (Throwable $e) {

            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }


            throw $e;
        }
    }


    public function getOrdersByUser(
        int $userId
    ): array
    {

        $sql = "
            SELECT
                id,
                order_number,
                subtotal,
                delivery_fee,
                tax,
                discount,
                total,
                status,
                created_at
            FROM orders
            WHERE user_id = ?
            ORDER BY created_at DESC
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([
            $userId
        ]);


        return $stmt->fetchAll();
    }


    public function getOrderById(
        int $orderId,
        int $userId
    ): ?array
    {

        $sql = "
            SELECT
                o.id,
                o.order_number,
                o.subtotal,
                o.delivery_fee,
                o.tax,
                o.discount,
                o.total,
                o.status,
                o.created_at,

                a.full_name,
                a.phone,
                a.address_line1,
                a.address_line2,
                a.city,
                a.state,
                a.postal_code

            FROM orders o

            INNER JOIN addresses a
                ON o.address_id = a.id

            WHERE o.id = ?
              AND o.user_id = ?

            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([
            $orderId,
            $userId
        ]);


        $order = $stmt->fetch();


        return $order ?: null;
    }


    public function getOrderItems(
        int $orderId
    ): array
    {

        $sql = "
            SELECT
                product_id,
                product_name,
                quantity,
                unit_price,
                subtotal
            FROM order_items
            WHERE order_id = ?
            ORDER BY id ASC
        ";


        $stmt = $this->db->prepare($sql);


        $stmt->execute([
            $orderId
        ]);


        return $stmt->fetchAll();
    }
}