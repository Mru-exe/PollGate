<?php

class User {
    // Properties corresponding to table columns
    private int $id;
    private string $username;
    private string $passwordSalt;
    private string $passwordHash;
    private ?int $roleId;
    private ?string $avatarPath; // Nullable, for cases where there is no avatar
    private DateTime $created;
    private ?DateTime $modified;
    // private bool $deleted;

    public function __construct(array $data = []) {
        $this->id = $data['id'] ?? 0;
        $this->username = $data['username'] ?? '';
        $this->passwordSalt = $data['passwordSalt'] ?? '';
        $this->passwordHash = $data['passwordHash'] ?? '';
        $this->roleId = $data['roleId'] ?? null;
        $this->avatarPath = $data['avatarPath'] ?? null;
        $this->created = new DateTime($data['created'] ?? 'now');
        $this->modified = isset($data['modified']) ? new DateTime($data['modified']) : null;
        $this->deleted = $data['deleted'] ?? false;
    }

    public function insert(PDO $pdo): int {

        try {
            $sql = "INSERT INTO Users (Username, PasswordSalt, PasswordHash, RoleId, AvatarPath) 
                    VALUES (:username, :passwordSalt, :passwordHash, :roleId, :avatarPath)";

            $stmt = $pdo->prepare($sql);
        
            // Bind values to statement parameters
            $stmt->bindValue(':username', $this->username, PDO::PARAM_STR);
            $stmt->bindValue(':passwordSalt', $this->passwordSalt, PDO::PARAM_STR);
            $stmt->bindValue(':passwordHash', $this->passwordHash, PDO::PARAM_STR);
            $stmt->bindValue(':roleId', $this->roleId, PDO::PARAM_INT);
            $stmt->bindValue(':avatarPath', $this->avatarPath, PDO::PARAM_STR);

            // Execute the statement and check for success
            if ($stmt->execute()) {
                // Retrieve and return the last inserted ID as an integer
                return (int)$pdo->lastInsertId();
            }
        } catch (\Throwable $th) {
            if($th->getCode() == 23000){
                echo 'User already exists';
            }
            return 0;
        }
        // Return 0 if insertion failed
        return 0;
    }
}

?>