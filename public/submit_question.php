<?php
/**
 * Submit Question Page - FAQ System
 * Users can submit questions for review
 */

$pageTitle = 'Ask a Question';
$extraCSS = 'style';
require_once 'includes/header.php';
require_once 'includes/auth.php';

requireLogin();

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !validateCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $question = trim($_POST['question'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        
        if (empty($question)) {
            $error = 'Please enter your question.';
        } elseif (strlen($question) < 10) {
            $error = 'Question must be at least 10 characters.';
        } elseif (strlen($question) > 500) {
            $error = 'Question must not exceed 500 characters.';
        } else {
            try {
                executeQuery(
                    "INSERT INTO submitted_questions (user_id, question, category_id, status) VALUES (?, ?, ?, 'pending')",
                    [$_SESSION['user_id'], $question, $categoryId]
                );
                
                $success = 'Question submitted successfully! Our team will review it.';
            } catch (Exception $e) {
                error_log("Submit question error: " . $e->getMessage());
                $error = 'Failed to submit question. Please try again.';
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="text-center mb-5">
                <div class="submit-icon mb-3">
                    <i class="bi bi-send"></i>
                </div>
                <h1 class="h3">Submit Your Question</h1>
                <p class="text-muted">Can't find an answer? Submit your question and our team will review it.</p>
            </div>
            
            <?php if ($error): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <?= h($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success d-flex align-items-center">
                <i class="bi bi-check-circle me-2"></i>
                <?= h($success) ?>
            </div>
            <?php endif; ?>
            
            <!-- Form Card -->
            <div class="submit-card p-4 p-lg-5 rounded-4 border-0 shadow-sm">
                <form method="POST" action="submit_question.php">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    
                    <div class="mb-4">
                        <label for="question" class="form-label fw-semibold">
                            Your Question <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="question" name="question" 
                                  rows="4" maxlength="500" required
                                  placeholder="e.g., How do I center a div in CSS? What is the difference between let and const?"></textarea>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">Be specific and clear</small>
                            <small class="text-muted"><span id="charCount">0</span>/500</small>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="category_id" class="form-label fw-semibold">Category (optional)</label>
                        <select class="form-select" id="category_id" name="category_id"><option value="">-- Select a category --</option>
                            <?php
                            $categories = getCategories();
                            foreach ($categories as $cat):
                            ?>
                            <option value="<?= $cat['category_id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= h($cat['category_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Help us categorize your question</small>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                            <i class="bi bi-send me-2"></i>Submit Question
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- How it works -->
            <div class="how-it-works mt-4 p-4 rounded-4">
                <h5 class="mb-3">
                    <i class="bi bi-question-circle text-primary me-2"></i>How it works
                </h5>
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <div class="step-icon mb-2">1</div>
                        <p class="small mb-0">Submit your question</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="step-icon mb-2">2</div>
                        <p class="small mb-0">Our team reviews it</p>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="step-icon mb-2">3</div>
                        <p class="small mb-0">Get notified when published</p>
                    </div>
                </div>
            </div>
            
            <!-- Search first -->
            <div class="mt-4 text-center">
                <p class="text-muted mb-2">Already have an answer?</p>
                <a href="search.php" class="btn btn-outline-primary">
                    <i class="bi bi-search me-2"></i>Search FAQs
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('question').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});
</script>

<style>
.submit-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.submit-card { background: white; }

.how-it-works {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

.step-icon {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
}
</style>

<?php require_once 'includes/footer.php'; ?>
