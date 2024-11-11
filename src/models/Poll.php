<?php

class Poll {
    private int $id;
    private string $name;
    private ?string $description;
    private string $question;
    private string $pollTypeCode;
    private ?DateTime $created;
    private ?int $createdBy;
    private ?DateTime $modified;
    private ?int $modifiedBy;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'];
        $this->question = $data['question'] ?? '';
        $this->pollTypeCode = $data['pollTypeCode'] ?? 'default';
        $this->created = $data['created'];
        $this->createdBy = $data['createdBy'];
        $this->modified = $data['modified'] ?? null;
        $this->modifiedBy = $data['modifiedBy'] ?? null;
    }

    public function getById(PDO $pdo){
        // $query = "SELECT ";
        return 0;
    }
}