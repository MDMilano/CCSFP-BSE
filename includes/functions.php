<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

// Initialize database connection
function getDatabaseConnection() {
    static $conn = null;
    if ($conn === null) {
        try {
            $database = new Database();
            $conn = $database->getMysqliConnection();
        } catch (Exception $e) {
            error_log("Failed to connect to database: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    return $conn;
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function redirect_to($location) {
    header("Location: " . $location);
    exit;
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function getCurrentElection($conn = null) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM elections WHERE is_active = TRUE AND start_date <= NOW() AND end_date >= NOW() LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result === false) {
            throw new Exception("Error executing query: " . $conn->error);
        }
        
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error in getCurrentElection: " . $e->getMessage());
        return null;
    }
}

function getElectionInstructions($conn = null, $election_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT instruction FROM election_instructions WHERE election_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $instructions = [];
        while ($row = $result->fetch_assoc()) {
            $instructions[] = $row['instruction'];
        }
        
        return $instructions;
    } catch (Exception $e) {
        error_log("Error in getElectionInstructions: " . $e->getMessage());
        return [];
    }
}

function getOfficerTypes($conn = null) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM officer_types ORDER BY display_order";
        $result = $conn->query($sql);
        
        if ($result === false) {
            throw new Exception("Error executing query: " . $conn->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getOfficerTypes: " . $e->getMessage());
        return [];
    }
}

function getPositions($conn = null, $officer_type_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM positions WHERE officer_type_id = ? ORDER BY display_order";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $officer_type_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getPositions: " . $e->getMessage());
        return [];
    }
}

function getCandidates($conn = null, $position_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM candidates WHERE position_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $position_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getCandidates: " . $e->getMessage());
        return [];
    }
}

function getGuidelines($conn = null) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM guidelines ORDER BY id";
        $result = $conn->query($sql);
        
        if ($result === false) {
            throw new Exception("Error executing query: " . $conn->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getGuidelines: " . $e->getMessage());
        return [];
    }
}

function getFAQs($conn = null) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM faqs ORDER BY id";
        $result = $conn->query($sql);
        
        if ($result === false) {
            throw new Exception("Error executing query: " . $conn->error);
        }
        
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (Exception $e) {
        error_log("Error in getFAQs: " . $e->getMessage());
        return [];
    }
}

function getUserProfile($conn = null, $user_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT * FROM users WHERE id = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error in getUserProfile: " . $e->getMessage());
        return null;
    }
}

function hasVoted($conn = null, $user_id, $election_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT COUNT(*) as vote_count FROM votes WHERE user_id = ? AND election_id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("ii", $user_id, $election_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['vote_count'] > 0;
    } catch (Exception $e) {
        error_log("Error in hasVoted: " . $e->getMessage());
        return false;
    }
}

function submitVote($conn = null, $user_id, $election_id, $votes) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Check if user has already voted
        if (hasVoted($conn, $user_id, $election_id)) {
            throw new Exception("User has already voted in this election");
        }
        
        // Insert each vote
        $sql = "INSERT INTO votes (user_id, candidate_id, election_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        foreach ($votes as $candidate_id) {
            if ($candidate_id !== 'abstain') {
                $stmt->bind_param("iii", $user_id, $candidate_id, $election_id);
                if (!$stmt->execute()) {
                    throw new Exception("Error recording vote: " . $stmt->error);
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Error in submitVote: " . $e->getMessage());
        throw $e;
    }
}

// Function to get candidate information
function getCandidateInfo($conn = null, $candidate_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT c.*, p.name as position_name, ot.name as officer_type 
                FROM candidates c 
                JOIN positions p ON c.position_id = p.id 
                JOIN officer_types ot ON p.officer_type_id = ot.id 
                WHERE c.id = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $candidate_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_assoc();
    } catch (Exception $e) {
        error_log("Error in getCandidateInfo: " . $e->getMessage());
        return null;
    }
}

// Function to check if election is active
function isElectionActive($conn = null, $election_id) {
    if ($conn === null) {
        $conn = getDatabaseConnection();
    }
    
    try {
        $sql = "SELECT is_active FROM elections WHERE id = ? AND start_date <= NOW() AND end_date >= NOW()";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        
        $stmt->bind_param("i", $election_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row && $row['is_active'] == 1;
    } catch (Exception $e) {
        error_log("Error in isElectionActive: " . $e->getMessage());
        return false;
    }
}
?>