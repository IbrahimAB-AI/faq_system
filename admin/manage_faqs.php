// Manage FAQs admin page
## admin/manage_faqs.php (Full)

```php
<?php
/**
 * Manage FAQs Page - Admin
 */

$pageTitle = 'Manage FAQs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $faqId = (int)$_GET['delete'];
    try {
        executeQuery("DELETE FROM faqs WHERE faq_id = ?", [$faqId]);
        setSuccess('FAQ deleted successfully.');
    } catch (Exception $e) {
        setError('Failed to delete FAQ.');
    }
    redirect('manage_faqs.php');
}

// Handle form submission (add/edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_faq'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        setError('Invalid request.');
    } else {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        
        // Validate and sanitize input
        if (empty($question)) {
            setError('Question is required.');
        } elseif (strlen($question) < 5) {
            setError('Question must be at least 5 characters.');
        } elseif (strlen($question) > 500) {
            setError('Question must not exceed 500 characters.');
        } elseif (empty($answer)) {
            setError('Answer is required.');
        } elseif (strlen($answer) < 10) {
            setError('Answer must be at least 10 characters.');
        } elseif ($categoryId === 0) {
            setError('Please select a category.');
        } else {
            // Basic HTML sanitization for admin content
            $question = strip_tags($question);
            $answer = strip_tags($answer, '<p><br><strong><em><ul><ol><li><a><code><pre>');
            
            try {
                if ($faqId) {
                    executeQuery(
                        "UPDATE faqs SET question = ?, answer = ?, category_id = ?, updated_at = NOW() WHERE faq_id = ?",
                        [$question, $answer, $categoryId, $faqId]
                    );
                    setSuccess('FAQ updated successfully.');
                } else {
                    executeQuery(
                        "INSERT INTO faqs (question, answer, category_id, created_by) VALUES (?, ?, ?, ?)",
                        [$question, $answer, $categoryId, $_SESSION['user_id']]
                    );
                    setSuccess('FAQ created successfully.');
                }
            } catch (Exception $e) {
                error_log("FAQ save error: " . $e->getMessage());
                setError('Failed to save FAQ. Please try again.');
            }
        }
    }
}

displayFlashMessages();

$editFaq = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editFaq = getFaqById((int)$_GET['edit']);
}

$faqs = fetchAll("
    SELECT f.*, c.category_name 
    FROM faqs f 
    JOIN categories c ON f.category_id = c.category_id 
    ORDER BY f.created_at DESC
");

$categories = getCategories();
?>

<div class="admin-page">
    <div class="container py-5">
        <h1 class="h3 mb-4">
            <i class="bi bi-journal-text text-primary me-2"></i>Manage FAQs
        </h1>
        
        <!-- Add/Edit Form -->
        <div class="form-card mb-4 p-4 rounded-4 border-0 shadow-sm">
            <h5 class="mb-4">
                <i class="bi bi-<?= $editFaq ? 'pencil' : 'plus-circle' ?> me-2"></i>
                <?= $editFaq ? 'Edit FAQ' : 'Add New FAQ' ?>
            </h5>
            
            <form method="POST" action="manage_faqs.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <?php if ($editFaq): ?>
                <input type="hidden" name="faq_id" value="<?= $editFaq['faq_id'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-select" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= ($editFaq && $editFaq['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                <?= h($cat['category_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Question *</label>
                        <input type="text" class="form-control" name="question" value="<?= h($editFaq['question'] ?? '') ?>" required maxlength="500">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Answer *</label>
                    <textarea class="form-control" name="answer" rows="6" required><?= h($editFaq['answer'] ?? '') ?></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="save_faq" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save FAQ
                    </button>
                    <?php if ($editFaq): ?>
                    <a href="manage_faqs.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- FAQs Table -->
        <div class="table-card p-4 rounded-4 border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Category</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($faqs as $faq): ?>
                        <tr>
                            <td>
                                <a href="../faq.php?id=<?= $faq['faq_id'] ?>" target="_blank" class="text-decoration-none">
                                    <?= h(truncate($faq['question'], 50)) ?>
                                </a>
                            </td>
                            <td><span class="badge bg-primary"><?= h($faq['category_name']) ?></span></td>
                            <td><small class="text-muted"><?= formatDate($faq['created_at']) ?></small></td>
                            <td>
                                <a href="manage_faqs.php?edit=<?= $faq['faq_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="manage_faqs.php?delete=<?= $faq['faq_id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this FAQ?">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($faqs)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No FAQs found. Create one above.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.admin-page { min-height: 100vh; }
.form-card, .table-card { background: white; }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

Copy this into your `admin/manage_faqs.php` file. It includes add/edit form, FAQs table with category badges, and delete functionality!