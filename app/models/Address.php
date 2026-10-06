<?php

class Address
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    /* =========================
       CREATE ADDRESS
    ========================= */

    public function create(
        int $userId,
        string $fullName,
        string $phone,
        string $address1,
        string $address2,
        string $city,
        string $state,
        string $postal
    ): bool
    {
        $sql = "
            INSERT INTO addresses
            (
                user_id,
                full_name,
                phone,
                address_line1,
                address_line2,
                city,
                state,
                postal_code
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?
            )
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $userId,
            $fullName,
            $phone,
            $address1,
            $address2,
            $city,
            $state,
            $postal
        ]);
    }


    /* =========================
       GET USER ADDRESSES
    ========================= */

    public function getByUser(int $userId): array
    {
        $sql = "
            SELECT *
            FROM addresses
            WHERE user_id = ?
            ORDER BY id DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $userId
        ]);

        return $stmt->fetchAll();
    }


    /* =========================
       GET SINGLE ADDRESS
       OWNERSHIP CHECK INCLUDED
    ========================= */

    public function getById(
        int $addressId,
        int $userId
    ): ?array
    {
        $sql = "
            SELECT *
            FROM addresses
            WHERE id = ?
            AND user_id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $addressId,
            $userId
        ]);

        $address = $stmt->fetch();

        return $address ?: null;
    }


    /* =========================
       UPDATE ADDRESS
       OWNERSHIP CHECK INCLUDED
    ========================= */

    public function update(
        int $addressId,
        int $userId,
        string $fullName,
        string $phone,
        string $address1,
        string $address2,
        string $city,
        string $state,
        string $postal
    ): bool
    {
        $sql = "
            UPDATE addresses
            SET
                full_name = ?,
                phone = ?,
                address_line1 = ?,
                address_line2 = ?,
                city = ?,
                state = ?,
                postal_code = ?
            WHERE id = ?
            AND user_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $fullName,
            $phone,
            $address1,
            $address2,
            $city,
            $state,
            $postal,
            $addressId,
            $userId
        ]);
    }


    /* =========================
       DELETE ADDRESS
       OWNERSHIP CHECK INCLUDED
    ========================= */

    public function delete(
        int $addressId,
        int $userId
    ): bool
    {
        $sql = "
            DELETE FROM addresses
            WHERE id = ?
            AND user_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $addressId,
            $userId
        ]);
    }
}
