<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

try {
    // Get database connection
    $conn = getDatabaseConnection();

    // Get current election information
    $election = getCurrentElection($conn);

    // Include header before any output
    include 'includes/header.php';

    if (!$election) {
        // If no active election, show only header and footer with a message
        echo "<main class='container mt-4'><h2 class='text-center'>No active election at the moment.</h2></main>";
        include 'includes/footer.php';
        exit();
    }

    // Get election instructions
    $instructions = getElectionInstructions($conn, $election['id']);

    // Get officer types (SSC and ISC)
    $officerTypes = getOfficerTypes($conn);

} catch (Exception $e) {
    error_log("Error in home.php: " . $e->getMessage());
    echo "<main class='container mt-4'><div class='alert alert-danger'>An error occurred. Please try again later.</div></main>";
    include 'includes/footer.php';
    exit();
}

?>

<main class="container mt-4">
    <section class="mb-4">
        <h2 class="text-center"><?php echo htmlspecialchars($election['title']); ?></h2>
        <p class="text-center">Welcome to the CCSFP Board of Student Election voting system. Please read the following instructions carefully before casting your vote:</p>
        <ul class="list-group">
            <?php foreach ($instructions as $instruction): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($instruction); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php foreach ($officerTypes as $officerType): ?>
        <section id="<?php echo strtolower($officerType['name']); ?>Voting" class="mb-4">
            <h3 class="text-center mb-3"><?php echo htmlspecialchars($officerType['name']); ?> Officer Voting</h3>
            <form id="<?php echo strtolower($officerType['name']); ?>VotingForm">
                <?php
                $positions = getPositions($conn, $officerType['id']);
                foreach ($positions as $position):
                    $candidates = getCandidates($conn, $position['id']);
                ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0"><?php echo htmlspecialchars($position['name']); ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($candidates as $candidate): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($candidate['name']); ?></h5>
                                                <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($candidate['party']); ?></h6>
                                                <div class="candidate-image-container">
                                                    <img src="<?php echo htmlspecialchars($candidate['image_url']); ?>" alt="<?php echo htmlspecialchars($candidate['name']); ?>" class="candidate-image">
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="<?php echo strtolower($officerType['name']) . $position['id']; ?>" id="<?php echo strtolower($officerType['name']) . $position['id'] . $candidate['id']; ?>" value="<?php echo $candidate['id']; ?>">
                                                    <label class="form-check-label" for="<?php echo strtolower($officerType['name']) . $position['id'] . $candidate['id']; ?>">
                                                        Vote for <?php echo htmlspecialchars($candidate['name']); ?>
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-info btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#candidateInfoModal" data-candidate="<?php echo $candidate['id']; ?>">
                                                    View Info
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <!-- Add Abstain option -->
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100">
                                        <div class="abstain-card-body">
                                            <div class="abstain-image-container">
                                                <img src="assets/image/ABSTAIN.png" alt="Abstain" class="abstain-image">
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="<?php echo strtolower($officerType['name']) . $position['id']; ?>" id="<?php echo strtolower($officerType['name']) . $position['id']; ?>Abstain" value="abstain">
                                                <label class="form-check-label" for="<?php echo strtolower($officerType['name']) . $position['id']; ?>Abstain">
                                                    Abstain from voting
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary btn-sm mt-3" onclick="resetVote('<?php echo strtolower($officerType['name']) . $position['id']; ?>')">Reset Vote</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </form>
        </section>
    <?php endforeach; ?>

    <div class="d-flex justify-content-between mb-4">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#votePreviewModal">
            <i class="bi bi-eye"></i> Vote Preview
        </button>
        <button type="button" class="btn btn-success" onclick="submitVote()">
            <i class="bi bi-check-circle"></i> Submit Vote
        </button>
    </div>
</main>

<?php
// Include footer
include 'includes/footer.php';

// Include modals
include 'includes/modals.php';
?>

<!-- Bootstrap JS -->
<script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/home.js"></script>
</body>
</html>