<!-- User Profile Modal -->
<div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userProfileModalLabel">User Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row">
                    <dt class="col-sm-4">Student ID</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['student_id']); ?></dd>

                    <dt class="col-sm-4">First Name</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['first_name']); ?></dd>

                    <dt class="col-sm-4">Last Name</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['last_name']); ?></dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['email']); ?></dd>

                    <dt class="col-sm-4">Course</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['course']); ?></dd>

                    <dt class="col-sm-4">Year</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['year']); ?></dd>

                    <dt class="col-sm-4">Section</dt>
                    <dd class="col-sm-8"><?php echo htmlspecialchars($_SESSION['section']); ?></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Candidate Info Modal -->
<div class="modal fade" id="candidateInfoModal" tabindex="-1" aria-labelledby="candidateInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="candidateInfoModalLabel">Candidate Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Candidate information will be dynamically populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Vote Preview Modal -->
<div class="modal fade" id="votePreviewModal" tabindex="-1" aria-labelledby="votePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="votePreviewModalLabel">Vote Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4>SSC Officers</h4>
                <ul id="sscVotePreview"></ul>
                <h4>ISC Officers</h4>
                <ul id="iscVotePreview"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>