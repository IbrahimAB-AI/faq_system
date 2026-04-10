
//Review submissions admin page
// admin/review_submissions.php (Full)

```php
<?php
/**
 * Review Submissions Page - Admin
 * Review user-submitted questions - approve or dismiss
 */

$pageTitle = 'Review Submissions';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Handle submission review
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_submission'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        setError('Invalid request.');
    } else {
        $submissionId = (int)($_POST['submission_id'] ?? 0);
        $action = $_POST['action'] ?? '';
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        
        if (!$submissionId || !in_array($action, ['approve', 'dismiss'])) {
            setError('Invalid request.');
        } elseif ($action === 'approve' && (empty($question) || empty($answer) || $categoryId === 0)) {
            setError('Question, answer, and category are required to approve.');
        } else {
            try {
                if ($action === 'approve') {
                    executeQuery(
                        "INSERT INTO faqs (question, answer, category_id, created_by) VALUES (?, ?, ?, ?)",
                        [$question, $answer, $categoryId, $_SESSION['user_id']]
                    );
                    
                    executeQuery(
                        "UPDATE submitted_questions SET status = 'approved', reviewed_by = ?, reviewed_at = NOW() WHERE submission_id = ?",
                        [$_SESSION['user_id'], $submissionId]
                    );
                    
                    setSuccess('Question approved and added to FAQs!');
                } else {
                    executeQuery(
                        "UPDATE submitted_questions SET status = 'dismissed', reviewed_by = ?, reviewed_at = NOW() WHERE submission_id = ?",
                        [$_SESSION['user_id'], $submissionId]
                    );
                    
                    setSuccess('Submission dismissed.');
                }
            } catch (Exception $e) {
                error_log("Review submission error: " . $e->getMessage());
                setError('Failed to process submission.');
            }
        }
    }
}

displayFlashMessages();

$submissions = fetchAll("
    SELECT sq.*, u.username, c.category_name 
    FROM submitted_questions sq
    JOIN users u ON sq.user_id = u.user_id
    LEFT JOIN categories c ON sq.category_id = c.category_id
    WHERE sq.status = 'pending'
    ORDER BY sq.submitted_at DESC
");

$categories = getCategories();
?>

<div class="admin-page">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-check-circle text-primary me-2"></i>Review Submissions
            </h1>
            <span class="badge bg-warning fs-6"><?= count($submissions) ?> Pending</span>
        </div>
        
        <?php if (!empty($submissions)): ?>
        
        <?php foreach ($submissions as $sub): ?>
        <div class="submission-card mb-4 p-4 rounded-4 border-0 shadow-sm">
            <div class="submission-header d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom">
                <div>
                    <span class="badge bg-info me-2"><?= h($sub['username']) ?></span>
                    <span class="text-muted small">submitted <?= formatDate($sub['submitted_at']) ?></span>
                </div>
                <?php if ($sub['category_name']): ?>
                <span class="badge bg-primary"><?= h($sub['category_name']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="submission-question p-3 rounded-3 mb-4">
                <h5 class="mb-0">
                    <i class="bi bi-question-circle text-warning me-2"></i>
                    <?= h($sub['question']) ?>
                </h5>
            </div>
            
            <form method="POST" action="review_submissions.php" class="review-form">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <input type="hidden" name="submission_id" value="<?= $sub['submission_id'] ?>">
                <input type="hidden" name="question" value="<?= h($sub['question']) ?>">
                
                <h6 class="mb-3">
                    <i class="bi bi-plus-circle text-success me-2"></i>Approve as FAQ
                </h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">-- Select --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= ($sub['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= h($cat['category_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Answer *</label>
                    <textarea class="form-control" name="answer" rows="4" required placeholder="Write the answer..."></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="review_submission" value="approve" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Approve & Create FAQ
                    </button>
                    <button type="submit" name="review_submission" value="dismiss" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle me-2"></i>Dismiss
                    </button>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
        
        <?php else: ?>
        <div class="empty-state text-center p-5 rounded-4">
            <div class="empty-icon mb-3">
                <i class="bi bi-check-circle"></i>
            </div>
            <h4>All Caught Up!</h4>
            <p class="text-muted">No pending submissions to review.</p>
        </div>
        <?php endif; ?>
        
        <!-- Recently Reviewed -->
        <div class="mt-5">
            <h5 class="mb-4">
                <i class="bi bi-clock-history text-primary me-2"></i>Recently Reviewed
            </h5>
            
            <?php
            $reviewed = fetchAll("
                SELECT sq.*, u.username, u2.username as reviewer 
                FROM submitted_questions sq
                JOIN users u ON sq.user_id = u.user_id
                LEFT JOIN users u2 ON sq.reviewed_by = u2.user_id
                WHERE sq.status != 'pending'
                ORDER BY sq.reviewed_at DESC
                LIMIT 10
            ");
            ?>
            
            <div class="table-card p-4 rounded-4 border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>By</th>
                                <th>Status</th>
                                <th>Reviewed By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviewed as $r): ?>
                            <tr>
                                <td><small><?= h(truncate($r['question'], 40)) ?></small></td>
                                <td><small><?= h($r['username']) ?></small></td>
                                <td>
                                    <span class="badge bg-<?= $r['status'] === 'approved' ? 'success' : 'secondary' ?>">
                                        <?= $r['status'] ?>
                                    </span>
                                </td>
                                <td><small><?= h($r['reviewer'] ?? '-') ?></small></td>
                                <td><small><?= $r['reviewed_at'] ? formatDate($r['reviewed_at']) : '-' ?></small></td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($reviewed)): ?>
                            <tr><td colspan="5" class="text-center text-muted">No reviewed submissions yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-page { min-height: 100vh; }
.submission-card, .table-card, .empty-state { background: white; }
.submission-question { background: #f8f9fa; }
.empty-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #10b981, #34d399);
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

Copy this into your `admin/review_submissions.php` file. It handles pending submissions, approve (creates FAQ) or dismiss actions, and shows recently reviewed history!