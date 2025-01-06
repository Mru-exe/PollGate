<?php
/*
 * This file contains the database context and repository classes for interacting with the database.
 * 
 * It includes the following functionalities:
 * - Database connection configuration using environment variables.
 * - PDO instance creation with error handling.
 * - Repository class with methods for CRUD operations on Users, Polls, PollOptions, and Votes.
 * 
 * The repository methods include:
 * - getUserById(int $id): Retrieves a user by their ID.
 * - getUserByUsername(string $username): Retrieves a user by their username.
 * - getPollById(int $id): Retrieves a poll by its ID.
 * - getPolls(int $limit = 10, int $offset = 0): Retrieves a list of polls with pagination.
 * - getPollOptionsByPollId(int $pollId): Retrieves poll options by poll ID.
 * - getVotesByPollId(int $pollId): Retrieves votes by poll ID.
 * - getUsernameAvailability(string $username): Checks if a username is available.
 * - insertUser(User $user): Inserts a new user into the database.
 * - getRolebyId(int $id): Retrieves a role by its ID.
 * - insertPoll(Poll $poll): Inserts a new poll into the database.
 * - insertPollOptions(array $pollOptions): Inserts multiple poll options into the database.
 * - insertVote(Vote $vote): Inserts a new vote into the database.
 * - updateUser(User $user): Updates an existing user in the database.
 * - updatePoll(Poll $poll): Updates an existing poll in the database.
 * - updatePollOptions(array $pollOptions): Updates multiple poll options in the database.
 * - softDeleteUser(int $id): Soft deletes a user by setting the deleted flag.
 * - softDeletePoll(int $id): Soft deletes a poll by setting the deleted flag.
 * - deletePoll(int $id): Deletes a poll and its options from the database.
 * - deleteUser(int $id): Deletes a user from the database.
 * 
 * The repository class uses prepared statements to prevent SQL injection and handles exceptions by logging errors and throwing exceptions.
 */
//TODO: Set permission of the file to 600
//TODO: Fix QUERIES to match new database

//Include entity classes
require_once "models/User.php";
require_once "models/Role.php";
require_once "models/PollOption.php";
require_once "models/Poll.php";
require_once "models/Vote.php";

//Configure database connection
$dbhost = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch data as associative array
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Use real prepared statements
];

//Create PDO instance
try {
    $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname", $user, $pass, $options);
} catch (PDOException $e) {
    error_log('Could not access database: ' . $e->getMessage());
    header("Location: error.php?code=Leaf");
    exit();
    //Script execution stops here
}

//Define repository class
class Repository {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * Retrieves a user by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return User|null The User object corresponding to the given ID.
     * @throws InvalidArgumentException If the provided ID is not a positive integer.
     * @throws Exception If there is an error while fetching the user from the database.
     */
    public function getUserById(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid user ID');
        }

        $query = "SELECT u.id, username, passwordSalt, passwordHash, roleId, r.displayName AS roleDisplayName, avatarUrl, u.created, u.modified FROM Users u LEFT JOIN Roles r ON roleId = r.id WHERE u.id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            if($result === false){
                return null;
            }
            return new User($result['id'], $result['username'], $result['passwordSalt'], $result['passwordHash'], $result['roleId'], $result['avatarUrl'], $result['roleDisplayName']);
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw new Exception('Unable to fetch user at this time.');
        }
        return null;
    }

    /**
     * Retrieves a user by their username.
     *
     * @param string $username The username of the user to retrieve.
     * @return User|null The User object if found, null otherwise.
     * @throws InvalidArgumentException If the provided username is not a valid string or is empty.
     * @throws Exception If there is an error executing the query.
     */
    public function getUserByUsername(string $username) {
        if (!is_string($username) || empty($username)) {
            throw new InvalidArgumentException('Invalid username');
        }

        $query = "SELECT u.id, username, passwordSalt, passwordHash, roleId, r.displayName AS roleDisplayName, avatarUrl, u.created, u.modified FROM Users u LEFT JOIN Roles r ON roleId = r.id WHERE u.username = :username";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':username' => $username]);
            $result = $stmt->fetch();
            if($result === false) {
                return null;
            }
            return new User($result['id'], $result['username'], $result['passwordSalt'], $result['passwordHash'], $result['roleId'], $result['avatarUrl'], $result['roleDisplayName']);
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw new Exception('Unable to fetch user at this time.');
        }
        return null;
    }

    /**
     * Retrieves a poll by its ID.
     *
     * @param int $id The ID of the poll to retrieve.
     * @return Poll|null The Poll object corresponding to the given ID.
     * @throws InvalidArgumentException If the provided ID is not a positive integer.
     * @throws Exception If there is an error while fetching the poll from the database.
     */
    public function getPollById(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid limit or offset value');
        }

        $query = "SELECT id, title, description, question, pollTypeCode, created, createdBy, modified, modifiedBy FROM Polls WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            if($result === false){
                return null;
            }
            return new Poll($result["id"], $result["title"], $result["description"], $result["question"], $result["pollTypeCode"], $result["created"], $result["createdBy"], $result["modified"], $result["modifiedBy"]);
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw new Exception('Unable to fetch poll at this time.');
        }
        return null;
    }

    /**
     * Retrieves a list of polls from the database.
     *
     * @param int $limit The maximum number of polls to retrieve. Default is 10.
     * @param int $offset The number of polls to skip before starting to retrieve. Default is 0.
     * @return array|null An array of Poll objects if successful, or null if no polls are found or an error occurs.
     * @throws InvalidArgumentException If the provided limit or offset are invalid.
     * @throws Exception If there is an error executing the query.
     */
    public function getPolls(int $limit = 10, int $offset = 0){
        if (!is_int($limit) || !is_int($offset) || $offset < 0 || $limit < 0) {
            throw new InvalidArgumentException('Invalid poll limit or offset');
        }
        $query = "SELECT id, title, description, question, pollTypeCode, created, createdBy, modified, modifiedBy FROM Polls WHERE deleted = 0 LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':limit' => $limit, ':offset' => $offset]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($results === false){
                return null;
            }
            $polls = array_map(function($row) {
                return new Poll($row['id'], $row['title'], $row['description'], $row['question'], $row['pollTypeCode'], $row['created'], $row['createdBy'], $row['modified'], $row['modifiedBy']);
            }, $results);
            return $polls;
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw new Exception('Unable to fetch polls at this time.');
        }
        return null;
    }

    /**
     * Retrieves poll options by poll ID.
     *
     * @param int $pollId The ID of the poll.
     * @return array|null An array of PollOption objects representing the poll options or null if no polls are found or an error occurs.
     * @throws InvalidArgumentException If the provided poll ID is not a positive integer.
     * @throws Exception If there is an error while fetching poll options from the database.
     */
    public function getPollOptionsByPollId(int $pollId) {
        if (!is_int($pollId) || $pollId <= 0) {
            throw new InvalidArgumentException('Invalid poll ID');
        }

        $query = "SELECT * FROM PollOptions WHERE pollId = :pollId";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':pollId' => $pollId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($results === false){
                return null;
            }
            $pollOptions = array_map(function($row) {
                return new PollOption($row['id'], $row['pollID'], $row['optionValue']);
            }, $results);
            return $pollOptions;

        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to fetch poll options at this time.');
        }
        return null;
    }

    public function getVotesByPollId(int $pollId) {
        if (!is_int($pollId) || $pollId <= 0) {
            throw new InvalidArgumentException('Invalid poll ID');
        }

        $query = "SELECT * FROM Votes WHERE pollId = :pollId";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':pollId' => $pollId]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($results === false){
                return null;
            }
            $pollOptions = array_map(function($row) {
                return new Vote($row['id'], $row['pollId'], $row['pollOptionId'], $row['userId'], $row['created'], $row['modified']);
            }, $results);
            return $pollOptions;

        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw new Exception('Unable to fetch votes at this time.');
        }
        return null;
    }

    public function getUsernameAvailability(string $username) {
        if (!is_string($username) || empty($username)) {
            throw new InvalidArgumentException('Invalid username');
        }

        $query = "SELECT COUNT(id) AS count FROM Users WHERE username = :username";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':username' => $username]);
            return $stmt->fetch()['count'];
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to check username availability at this time.');
        }
    }

    public function insertUser(User $user) {
        $query = "INSERT INTO Users (username, passwordSalt, passwordHash, roleId, avatarUrl) VALUES (:username, :passwordSalt, :passwordHash, :roleId, :avatarUrl)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':username' => $user->username,
                ':passwordSalt' => $user->passwordSalt,
                ':passwordHash' => $user->passwordHash,
                ':roleId' => $user->roleId,
                ':avatarUrl' => $user->avatarUrl
            ]);
        } catch (PDOException $e) {
            if($e->errorInfo[1] === 1062) {
                throw new Exception('Username already exists.');
            }
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to insert user at this time.');
        }
        return $this->conn->lastInsertId();
    }

    public function getRolebyId(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid role ID');
        }

        $query = "SELECT * FROM role WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            return new Role($result['id'], $result['name'], $result['created'], $result['modified']);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to fetch role at this time.');
        }
        return null;
    }

    public function insertPoll(Poll $poll) {
        $query = "INSERT INTO poll (question, created, modified) VALUES (:question, :created, :modified)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':question' => $poll->question,
                ':created' => $poll->created->format('Y-m-d H:i:s'),
                ':modified' => $poll->modified->format('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to insert poll at this time.');
        }
    }

    public function insertPollOptions(array $pollOptions) {
        $query = "INSERT INTO poll_option (poll_id, option_text) VALUES ";
        
        $values = [];
        $params = [];
        foreach ($pollOptions as $index => $option) {
            $values[] = "(?, ?)";
            $params[] = $option->pollId;
            $params[] = $option->optionText;
        }

        $query .= implode(", ", $values);

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to insert poll options at this time.');
        }
    }

    public function insertVote(Vote $vote) {
        $query = "INSERT INTO vote (poll_id, poll_option_id, user_id) VALUES (:poll_id, :poll_option_id, :user_id)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':poll_id' => $vote->pollId,
                ':poll_option_id' => $vote->pollOptionId,
                ':user_id' => $vote->createdBy
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to insert vote at this time.');
        }
    }

    public function updateUser(User $user) {
        $query = "UPDATE user SET username = :username, passwordHash = :passwordHash, roleId = :roleId WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':username' => $user->username,
                ':passwordHash' => $user->passwordHash,
                ':roleId' => $user->roleId,
                ':id' => $user->id
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to update user at this time.');
        }
    }

    public function updatePoll(Poll $poll) {
        $query = "UPDATE poll SET question = :question, title = :title, description = :description WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':question' => $poll->question,
                ':title' => $poll->title,
                ':description' => $poll->description,
                ':id' => $poll->id
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to update poll at this time.');
        }
    }

    public function updatePollOptions(array $pollOptions) {
        $query = "UPDATE poll_option SET option_text = CASE id ";
        $ids = [];

        foreach ($pollOptions as $option) {
            $query .= "WHEN ? THEN ? ";
            $ids[] = $option->id;
            $ids[] = $option->optionText;
        }

        $query .= "END WHERE id IN (" . implode(',', array_fill(0, count($pollOptions), '?')) . ")";
        
        error_log("Trying to execute query: " . $query);

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute(array_merge($ids, array_column($pollOptions, 'id')));
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to update poll options at this time.');
        }
    }

    public function softDeleteUser(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid user ID');
        }

        $query = "UPDATE Users SET deleted = 1 WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to delete user at this time.');
        }
    }

    public function softDeletePoll(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid poll ID');
        }

        $query = "UPDATE poll SET deleted = 1 WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to delete poll at this time.');
        }
    }

    public function deletePoll(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid poll ID');
        }

        $queryMain = "DELETE FROM poll_option WHERE poll_id = :id";
        $queryForeign = "DELETE FROM polls WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($queryMain);
            $stmt->execute([':id' => $id]);

            $stmt = $this->conn->prepare($queryForeign);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to delete poll at this time.');
        }
    }

    public function deleteUser(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid user ID');
        }

        $query = "DELETE FROM Users WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw new Exception('Unable to delete user at this time.');
        }

    }
}

if(isset($pdo)) {
    $db = new Repository($pdo);
}

?>