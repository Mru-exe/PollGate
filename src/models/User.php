<?php

/**
 * Class User
 * 
 * Represents a user in the system.
 * 
 * @property int|null $id The unique identifier for the user.
 * @property string $username The username of the user.
 * @property string $passwordSalt The salt used for hashing the user's password.
 * @property string $passwordHash The hashed password of the user.
 * @property int $roleId The role ID associated with the user.
 * @property string|null $avatarToken The token for the user's avatar, may be null.
 * @property string|null $roleName The name of the role associated with the user, may be null.
 * @property DateTime|null $created The date and time when the user was created, may be null.
 * @property DateTime|null $modified The date and time when the user was last modified, may be null.
 */
class User {
    public ?int $id;
    public string $username;
    public string $passwordSalt;
    public string $passwordHash;
    public int $roleId;
    public ?string $avatarToken;
    public ?string $roleName;
    public ?DateTime $created;
    public ?DateTime $modified;

    //avatarToken may be null by default
    public function __construct(int $id, string $username, string $passwordSalt, string $passwordHash, int $roleId, string $avatarToken, ?string $roleName = null, ?string $created = null, ?string $modified = null) {
        $this->id = $id;
        $this->username = $username;
        $this->passwordSalt = $passwordSalt;
        $this->passwordHash = $passwordHash;
        $this->roleId = $roleId;
        $this->avatarToken = $avatarToken;
        $this->roleName = $roleName;
        $this->created = $created ? new DateTime($created) : null;
        $this->modified = $modified ? new DateTime($modified) : null;
    }

}

?>