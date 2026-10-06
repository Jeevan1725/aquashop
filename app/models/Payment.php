<?php

class Payment
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(
        int $orderId,
        string $gateway,
        float $amount
    ): int {
        $sql = "
            INSERT INTO payments
            (
                order_id,
                gateway,
                amount,
                status
            )
            VALUES (?, ?, ?, 'created')
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $orderId,
            $gateway,
            $amount
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getByOrderId(int $orderId): ?array
    {
        $sql = "
            SELECT *
            FROM payments
            WHERE order_id = ?
            ORDER BY id DESC
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$orderId]);

        $payment = $stmt->fetch();

        return $payment ?: null;
    }

    public function setGatewayOrder(
        int $paymentId,
        string $gatewayOrderId
    ): bool {
        $sql = "
            UPDATE payments
            SET
                gateway_order_id = ?,
                status = 'pending'
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $gatewayOrderId,
            $paymentId
        ]);
    }

    public function markPaid(
        int $paymentId,
        string $gatewayPaymentId,
        string $gatewaySignature
    ): bool {
        $sql = "
            UPDATE payments
            SET
                gateway_payment_id = ?,
                gateway_signature = ?,
                status = 'paid',
                paid_at = CURRENT_TIMESTAMP
            WHERE id = ?
            AND status != 'paid'
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $gatewayPaymentId,
            $gatewaySignature,
            $paymentId
        ]);
    }

    public function markFailed(int $paymentId): bool
    {
        $sql = "
            UPDATE payments
            SET status = 'failed'
            WHERE id = ?
            AND status != 'paid'
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $paymentId
        ]);
    }
}
