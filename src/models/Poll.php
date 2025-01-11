<?php
/**
 * Class Poll
 *
 * Represents a poll with various attributes such as title, description, question, and metadata about its creation and modification.
 *
 * @property int $id The unique identifier for the poll.
 * @property string $title The title of the poll.
 * @property string $description A brief description of the poll.
 * @property string $question The main question of the poll.
 * @property string $pollTypeCode The type code of the poll.
 * @property int $responses The number of responses the poll has received.
 * @property DateTime $created The date and time when the poll was created.
 * @property int $createdBy The ID of the user who created the poll.
 * @property string $createdByUsername The username of the user who created the poll.
 * @property ?DateTime $modified The date and time when the poll was last modified, or null if it has not been modified.
 * @property ?int $modifiedBy The ID of the user who last modified the poll, or null if it has not been modified.
 * @property string $modifiedByUsername The username of the user who last modified the poll.
 */
class Poll {
    public int $id;
    public string $title;
    public string $description;
    public string $question;
    public string $pollTypeCode;
    public int $responses;
    public DateTime $created;
    public int $createdBy;
    public string $createdByUsername;
    public ?DateTime $modified;
    public ?int $modifiedBy;
    public string $modifiedByUsername;

    public function __construct(int $id, string $title, string $description, string $question, string $pollTypeCode, int $responses, string $created, int $createdBy, string $createdByUsername = "", string $modified = null, int $modifiedBy = 0, string|null $modifiedByUsername = "") {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->question = $question;
        $this->pollTypeCode = $pollTypeCode;
        $this->responses = $responses;
        $this->created = new DateTime($created);
        $this->createdBy = $createdBy;
        $this->createdByUsername = $createdByUsername ?? '';
        $this->modified = new DateTime($modified);
        $this->modifiedBy = $modifiedBy;
        $this->modifiedByUsername = $modifiedByUsername ?? '';
    }
}
?>