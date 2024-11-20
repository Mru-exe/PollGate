<?php

function getPollById(PDO $pdo, int $id){
    $query = "SELECT * FROM vPolls WHERE Id = :pollId";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(":pollId", $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        $res = $stmt->fetch();
    } catch (PDOException $e) {
        // echo $e->getMessage();
        return $e; // returns PDOException instance
    }
    if(!empty($res)) return new Poll($res); //returns Poll instance
    return null;
}

class Poll {
    private int $id;
    private string $name;
    private ?string $description;
    private string $question;
    private string $pollTypeCode;
    private ?DateTime $created;
    private ?string $createdBy;
    private ?DateTime $modified;
    private ?string $modifiedBy;

    public function __construct(array $data) {
        $this->id = $data['Id'];
        $this->name = $data['Name'] ?? '';
        $this->description = $data['Description'];
        $this->question = $data['Question'] ?? '';
        $this->pollTypeCode = $data['PollTypeCode'] ?? 'default';
        $this->created = new DateTime($data['Created']) ?? null;
        $this->createdBy = $data['CreatedBy'] ?? null;
        $this->modified = new DateTime($data['Modified']) ?? null;
        $this->modifiedBy = $data['ModifiedBy'] ?? null;
    }

    public function getById(PDO $pdo){
        // $query = "SELECT ";
        return 0;
    }

    public function debug() {
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true); // Allow access to private properties
            $name = $property->getName();
            $value = $property->getValue($this);
            echo "$name: ";
            if ($value instanceof DateTime) {
                echo $value->format('Y-m-d H:i:s') . "<br>";
            } else {
                echo (is_null($value) ? 'null' : $value) . "<br>";
            }
        }
    }
}