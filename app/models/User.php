<?php

class User
{
    private PDO $db;


    public function __construct(PDO $db)
    {
        $this->db = $db;
    }


    public function create(
        string $name,
        string $email,
        string $password,
        string $phone = null
    ): bool
    {

        $roleQuery = "
            SELECT id
            FROM roles
            WHERE name='customer'
            LIMIT 1
        ";

        $roleStmt = $this->db->query($roleQuery);

        $role = $roleStmt->fetch();


        $passwordHash = password_hash(
            $password,
            PASSWORD_DEFAULT
        );


        $sql = "
            INSERT INTO users
            (
                role_id,
                name,
                email,
                password_hash,
                phone
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?
            )
        ";


        $stmt = $this->db->prepare($sql);


        return $stmt->execute([
            $role['id'],
            $name,
            $email,
            $passwordHash,
            $phone
        ]);

    }



    public function findByEmail(string $email): ?array
    {

        $sql = "
            SELECT
                u.*,
                r.name AS role

            FROM users u

            INNER JOIN roles r
            ON u.role_id = r.id

            WHERE u.email = ?

            LIMIT 1
        ";


        $stmt = $this->db->prepare($sql);

        $stmt->execute([$email]);


        $user = $stmt->fetch();


        return $user ?: null;

    }


        /*
    |--------------------------------------------------------------------------
    | VULNERABLE findByEmail — for demo only
    |--------------------------------------------------------------------------
    |
    | WARNING: Raw string concatenation. SQL injection possible.
    | Only used when mode === 'vulnerable'.
    |
    */
    public function findByEmailVulnerable(string $email): ?array
    {
        $sql = "
            SELECT
                u.*,
                r.name AS role

            FROM users u

            INNER JOIN roles r
            ON u.role_id = r.id

            WHERE u.email = '$email'

            LIMIT 1
        ";

        try {
            $stmt = $this->db->query($sql);
            $user = $stmt->fetch();
            return $user ?: null;
        } catch (PDOException $e) {
            // VULNERABLE: leak error to attacker
            die("SQL Error: " . $e->getMessage());
        }
    }

        public function findById(int $id): ?array
    {
        $sql = "
            SELECT *
            FROM users
            WHERE id = ?
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$id]);

        $user = $stmt->fetch();

        return $user ?: null;
    }



    public function updatePassword(
        int $userId,
        string $newHash
    ): bool
    {

        $sql = "
            UPDATE users
            SET password_hash = ?
            WHERE id = ?
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $newHash,
            $userId
        ]);

    }

}
