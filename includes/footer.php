<!-- <?php
$guidelines = getGuidelines($conn);
$faqs = getFAQs($conn);
?> -->

<footer class="bg-light text-center text-lg-start mt-4">
    <div class="container p-4">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">CCSFP Board of Student Election</h5>
                <div class="d-flex flex-column align-items-center">
                    <p>
                        Empowering students through fair and transparent elections.
                    </p>
                </div>
            </div>
            
            <!-- Links Section -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">LINKS</h5>
                <div class="d-flex flex-column align-items-center">
                    <a href="#" class="text-dark text-decoration-none mb-2" data-bs-toggle="modal" data-bs-target="#guidelinesModal">
                        Election Guidelines
                    </a>
                    <a href="#" class="text-dark text-decoration-none mb-2" data-bs-toggle="modal" data-bs-target="#profilesModal">
                        Candidate Profiles
                    </a>
                    <a href="#" class="text-dark text-decoration-none mb-2" data-bs-toggle="modal" data-bs-target="#faqsModal">
                        FAQs
                    </a>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">CONTACT</h5>
                <div class="d-flex flex-column align-items-center">
                    <a href="https://www.facebook.com/profile.php?id=100093158757949" target="_blank" class="text-dark text-decoration-none mb-2">
                        <i class="bi bi-facebook me-1"></i>Facebook Page
                    </a>
                    <a href="mailto:ccsfpbse@gmail.com" class="text-dark text-decoration-none mb-2">
                        <i class="bi bi-envelope-fill me-2"></i>ccsfpbse@gmail.com
                    </a>
                    <a href="tel:+639123456789" class="text-dark text-decoration-none mb-2">
                        <i class="bi bi-telephone-fill me-2"></i>(+63) 912-345-6789
                    </a>
                    <address class="mb-0 text-center">
                        <i class="bi bi-geo-alt-fill me-2"></i>
                        City College of San Fernando Pampanga<br>
                        Brgy. Alasas, City of San Fernando,<br>
                        Pampanga, Philippines
                    </address>
                </div>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        © <?php echo date("Y"); ?> CCSFP Board of Student Election. All rights reserved.
    </div>
</footer>

<!-- Election Guidelines Modal -->
<div class="modal fade" id="guidelinesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Election Guidelines</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="guidelines-content">
                    <?php foreach ($guidelines as $guideline): ?>
                        <section class="mb-4">
                            <h6 class="fw-bold"><?php echo htmlspecialchars($guideline['title']); ?></h6>
                            <?php echo nl2br(htmlspecialchars($guideline['content'])); ?>
                        </section>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- FAQs Modal -->
<div class="modal fade" id="faqsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Frequently Asked Questions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="accordion" id="faqAccordion">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button <?php echo $index === 0 ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?php echo $index; ?>">
                                    <?php echo htmlspecialchars($faq['question']); ?>
                                </button>
                            </h2>
                            <div id="faq<?php echo $index; ?>" class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>