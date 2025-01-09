<?php

/**
 * Class Vote
 * 
 * Represents a vote in a poll.
 * 
 * @property int $id The unique identifier for the vote.
 * @property int $pollId The unique identifier for the poll.
 * @property int $pollOptionId The unique identifier for the poll option.
 * @property DateTime $created The date and time when the vote was created.
 * @property int $createdBy The unique identifier for the user who created the vote.
 */
class Vote {
    public int $id;
    public int $pollId;
    public int $pollOptionId;
    public DateTime $created;
    public int $createdBy;

    public function __construct(int $id, int $pollId, int $pollOptionId, string $created, int $createdBy) {
        $this->id = $id;
        $this->pollId = $pollId;
        $this->pollOptionId = $pollOptionId;
        $this->created = $created ?? new DateTime($created);
        $this->createdBy = $createdBy;
    }
}

?>