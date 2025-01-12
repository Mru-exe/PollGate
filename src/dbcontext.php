<?php
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

    //USER RELATED METHODS

    /**
     * Retrieves a list of users from the database.
     *
     * @param int $limit The maximum number of users to retrieve. Default is 10.
     * @param int $offset The number of users to skip before starting to collect the result set. Default is 0.
     * @return User[]|null An array of User objects or null if no users are found.
     * @throws PDOException If there is an error executing the query.
     */
    public function getUsers(int $limit = 10, int $offset = 0){
        $query = "SELECT * FROM vUsers LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll();
            if($results === false){
            }
            $users = array_map(function($row) {
                return new User($row['id'], $row['username'], $row['passwordSalt'], $row['passwordHash'], $row['roleId'], $row['avatarToken'], $row['roleName'], $row['created'], $row['modified']);
            }, $results);
            return $users;        
        } catch (PDOException $e) {
            error_log('PDO Exception at getUsers(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves a user by their ID.
     *
     * @param int $id The ID of the user to retrieve.
     * @return User|null The User object if found, null if not found.
     * @throws InvalidArgumentException If the provided user ID is invalid.
     * @throws PDOException If there is an error executing the query.
     */
    public function getUserById(int $id) {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid user ID');
        }

        $query = "SELECT * FROM vUsers WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            if($result === false){
                return null;
            }
            return new User($result['id'], $result['username'], $result['passwordSalt'], $result['passwordHash'], $result['roleId'], $result['avatarToken'], $result['roleName'], $result['created'], $result['modified']);
        } catch (PDOException $e) {
            error_log('PDO Exception at getUserById(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Searches for polls based on the user's query.
     *
     * @param string $userQuery The search query provided by the user.
     * @param int $limit The maximum number of results to return. Default is 3.
     * @param int $offset The number of results to skip before starting to collect the result set. Default is 0.
     * @return array|null An array of Poll objects if results are found, null otherwise.
     * @throws PDOException If a database error occurs.
     */
    public function searchPolls(string $userQuery, int $limit = 3, int $offset = 0) {
        $sql = "WITH FilteredPolls AS (SELECT * FROM vPolls WHERE title LIKE :titleLike OR question LIKE :questionLike) SELECT p.*, (SELECT COUNT(*) FROM FilteredPolls) AS count FROM FilteredPolls p LIMIT :limit OFFSET :offset;";
        $userQuery = '%'.$userQuery.'%';
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':titleLike' => $userQuery, ':questionLike' => $userQuery, ':limit' => $limit, ':offset' => $offset]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($results === false) {
            }
            $polls = array_map(function($row) {
                return new Poll($row['id'], $row['title'], $row['description'], $row['question'], $row['pollTypeCode'], $row['responses'], $row['created'], $row['createdBy'], $row['createdByUsername'], $row['modified'], $row['modifiedBy'], $row['modifiedByUsername']);
            }, $results);
            return ["count"=>$results[0]['count'], "polls"=>$polls];
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw $e;
            // throw new Exception('Unable to search polls at this time.');
        }
    }

    /**
     * Retrieves a user by their username.
     *
     * @param string $username The username of the user to retrieve.
     * @return User|null The User object if found, null otherwise.
     * @throws InvalidArgumentException If the provided username is not a valid string or is empty.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getUserByUsername(string $username) {
        if (!is_string($username) || empty($username)) {
            throw new InvalidArgumentException('Invalid username');
        }

        $query = "SELECT u.id, username, passwordSalt, passwordHash, roleId, r.displayName AS roleDisplayName, avatarToken, u.created, u.modified FROM Users u LEFT JOIN Roles r ON roleId = r.id WHERE u.username = :username";
        $query = "SELECT u.id, username, passwordSalt, passwordHash, roleId, r.displayName AS roleDisplayName, avatarToken, u.created, u.modified FROM Users u INNER JOIN Roles r ON roleId = r.id WHERE u.username = :username";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':username' => $username]);
            $result = $stmt->fetch();
            if($result === false) {
                return null;
            }
            return new User($result['id'], $result['username'], $result['passwordSalt'], $result['passwordHash'], $result['roleId'], $result['avatarToken'], $result['roleDisplayName'], $result['created'], $result['modified']);
        } catch (PDOException $e) {
            error_log('PDO Exception at getUserByUsernane(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves user's statistics by their ID.
     * 
     * @param int $it The ID of the user.
     * @return array Associative array of stats.
     * @throws InvalidArgumentException If the provided ID is not a valid integer.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getUserStats(int $id){
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid user ID');
        }

        $query = "SELECT (SELECT COUNT(*) FROM Votes v WHERE v.createdBy = :userIdv) AS votes, (SELECT COUNT(*) FROM Polls p WHERE p.createdBy = :userIdp) AS polls";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':userIdv' => $id, ':userIdp' => $id]);
            $result = $stmt->fetch();
            return ["polls" => $result['polls'], 'votes' => $result['votes']];
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Updates a user in the database.
     *
     * @param User $user An instance of the User class containing user details.
     * @throws PDOException If the insertion fails.
     */    
    public function updateUser(User $user) {
        $query = "UPDATE Users SET username = :username, passwordHash = :passwordHash, roleId = :roleId, avatarToken = :avatarToken WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':username' => $user->username,
                ':passwordHash' => $user->passwordHash,
                ':roleId' => $user->roleId,
                'avatarToken' => $user->avatarToken,
                ':id' => $user->id
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deletes a user from the database.
     *
     * @param User $id The ID of the users to be deleted.
     * @throws PDOException If the insertion fails.
     */
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

    /**
     * Retreives User's vote in a specified Poll.
     *
     * @param int $userId The ID of the user.
     * @param int $pollId The ID of the poll.
     * @return int|bool ID of the chosen answer if the user has voted, false otherwise.
     * @throws InvalidArgumentException If the provided user ID or poll ID are not positive integers.
     * @throws PDOException If there is an error while checking the vote in the database.
     */
    public function getUserVote(int $userId, int $pollId) {
        if (!is_int($userId) || $userId <= 0 || !is_int($pollId) || $pollId <= 0) {
            throw new InvalidArgumentException('Invalid user ID or poll ID');
        }

        $query = "SELECT pollOptionId FROM Votes WHERE createdBy = :userId AND pollId = :pollId";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':userId' => $userId, ':pollId' => $pollId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result !== false){
                return $result['pollOptionId'];
            }
            return $result;
        } catch (PDOException $e) {
            error_log('PDO Exception occurred: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves a amount of users with the desired username.
     *
     * @param int $username The desider username.
     * @return int Amount of users with such username, 0 if none.
     * @throws InvalidArgumentException If the provided Username is not valid.
     * @throws PDOException If there is an error executing the database query.
     */
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
            error_log('PDO Exception at getUsernameAvailability(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inserts a new user into the database.
     *
     * @param User $user An instance of the User class containing user details.
     * @return int The ID of the newly inserted user.
     * @throws PDOException If the insertion fails.
     */
    public function insertUser(User $user) {
        $query = "INSERT INTO Users (username, passwordSalt, passwordHash, roleId, avatarToken) VALUES (:username, :passwordSalt, :passwordHash, :roleId, :avatarToken)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':username' => $user->username,
                ':passwordSalt' => $user->passwordSalt,
                ':passwordHash' => $user->passwordHash,
                ':roleId' => $user->roleId,
                ':avatarToken' => $user->avatarToken
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception at insertUser(): ' . $e->getMessage());
            throw $e;
        }
        return $this->conn->lastInsertId();
    }

    //POLL RELATED METHODS

    /**
     * Retrieves a poll by its ID.
     *
     * @param int $id The ID of the poll to retrieve.
     * @return Poll|null The Poll object corresponding to the given ID.
     * @throws InvalidArgumentException If the provided ID is not a valid integer.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getPollById(int $id) {
        if (!is_int($id) || $id <= 0) {
            throw new InvalidArgumentException('Invalid limit or offset value');
        }

        $query = "SELECT * FROM vPolls WHERE id = :id";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':id' => $id]);
            $result = $stmt->fetch();
            if($result === false){
                return null;
            }
            return new Poll($result["id"], $result["title"], $result["description"], $result["question"], $result["pollTypeCode"], $result['responses'], $result["created"], $result["createdBy"], $result['createdByUsername'], $result["modified"], $result["modifiedBy"], $result['modifiedByUsername']);
        } catch (PDOException $e) {
            error_log('PDO Exception at getPollById(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves a list of polls from the database.
     *
     * @param int $limit The maximum number of polls to retrieve. Default is 10.
     * @param int $offset The number of polls to skip before starting to retrieve. Default is 0.
     * @param string $order Column name to order by. Default is id.
     * @param string $filter Additional SQL condition.
     * @return array|null An array of Poll objects if successful, or null if no polls are found or an error occurs.
     * @throws InvalidArgumentException If the provided limit or offset are invalid.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getPolls(int $limit = 10, int $offset = 0, string $filter = "1=1", string $order = "id"){
        if ($limit <= 0 || $offset < 0) {
            throw new InvalidArgumentException('Invalid limit or offset');
        }
        $query = "SELECT * FROM vPolls WHERE $filter ORDER BY $order LIMIT :limit OFFSET :offset";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([':limit' => $limit, ':offset' => $offset]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if($results === false){
                return null;
            }
            $polls = array_map(function($row) {
                return new Poll($row['id'], $row['title'], $row['description'], $row['question'], $row['pollTypeCode'], $row['responses'], $row['created'], $row['createdBy'], $row['createdByUsername'], $row['modified'], $row['modifiedBy'], $row['modifiedByUsername']);
            }, $results);
            return $polls;
        } catch (PDOException $e) {
            error_log('PDO Exception at getPolls(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves poll options by poll ID.
     *
     * @param int $pollId The ID of the poll.
     * @return array|null An array of PollOption objects representing the poll options or null if no polls are found or an error occurs.
     * @throws InvalidArgumentException If the provided poll ID is not a positive integer.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getPollOptionsByPollId(int $pollId) {
        if ($pollId <= 0) {
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
                return new PollOption($row['id'], $row['pollId'], $row['optionValue']);
            }, $results);
            return $pollOptions;

        } catch (PDOException $e) {
            error_log('PDO Exception at getPollOptionsByPollId(): ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves votes associated with a specific poll ID.
     * This method is not implemented yet!
     *
     * @param int $pollId The ID of the poll for which votes are to be retrieved.
     * @return array|null An array of Vote objects if votes are found, null otherwise.
     * @throws InvalidArgumentException If the provided poll ID is not a positive integer.
     * @throws PDOException If there is an error executing the database query.
     */
    public function getVotesByPollId(int $pollId) {
        if ($pollId <= 0) {
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
            throw $e;
        }
    }

    /**
     * Inserts a new poll into the database.
     *
     * @param Poll $poll An instance of the Poll class containing details.
     * @return int The ID of the newly inserted poll.
     * @throws PDOException If the insertion fails.
     */
    public function insertPoll(Poll $poll) {
        $query = "INSERT INTO Polls (title, description, question, createdBy) VALUES (:title, :description, :question, :createdBy)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':title' => $poll->title,
                ':description' => $poll->description,
                ':question' => $poll->question,
                ':createdBy' => $poll->createdBy
            ]);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inserts a batch of pollOptions into the database.
     *
     * @param array $pollOptions An array of instances of the PollOptions class containing pollOptions details.
     * @throws PDOException If the insertion fails.
     */
    public function insertPollOptions(array $pollOptions) {
        $query = "INSERT INTO PollOptions (pollId, optionValue) VALUES ";
        
        $values = [];
        $params = [];
        foreach ($pollOptions as $index => $option) {
            $values[] = "(?, ?)";
            $params[] = $option->pollId;
            $params[] = $option->optionValue;
        }
        
        $query .= implode(", ", $values);

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Inserts a new vote into the database.
     *
     * @param vote $vote An instance of the Vote class containing vote details.
     * @return int The ID of the newly inserted vote.
     * @throws PDOException If the insertion fails.
     */
    public function insertVote(Vote $vote) {
        $query = "INSERT INTO Votes (pollId, pollOptionId, createdBy) VALUES (:pollId, :pollOptionId, :userId)";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':pollId' => $vote->pollId,
                ':pollOptionId' => $vote->pollOptionId,
                ':userId' => $vote->createdBy
            ]);
        } catch (PDOException $e) {
            error_log('PDO Exception occured: ' . $e->getMessage());
            throw  $e;
        }
    }

}

if(isset($pdo)) {
    $db = new Repository($pdo);
} else {
header("Location: /~kindlma7/PollGate/error.php?code=leaf");
}
?>
