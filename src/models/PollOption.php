<?php

/**
 * Class PollOption
 * 
 * Represents an answer option in a poll if supported.
 * 
 * @property int $id The unique identifier for the poll option.
 * @property int $pollId The identifier of the poll to which this option belongs.
 * @property string $optionValue The value or text of the poll option.
 */
class PollOption {
    public int $id;
    public int $pollId;
    public string $optionValue;

    public function __construct(int $id, int $pollId, string $optionValue) {
        $this->id = $id;
        $this->pollId = $pollId;
        $this->optionValue = $optionValue;
    }
}

?>