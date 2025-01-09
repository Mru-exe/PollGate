<?php

/**
 * Class Role
 * 
 * Represents a user role within the application.
 * 
 * @property int $id The unique identifier for the role.
 * @property string $displayName The display name of the role.
 * @property DateTime $created The date and time when the role was created.
 * @property ?DateTime $modified The date and time when the role was last modified, or null if it has not been modified.
 */
class Role {
    public int $id;
    public string $displayName;
    public DateTime $created;
    public ?DateTime $modified;

    public function __construct(int $id, string $displayName, DateTime $created, ?DateTime $modified = null) {
        $this->id = $id;
        $this->displayName = $displayName;
        $this->created = $created;
        $this->modified = $modified;
    }
}

?>