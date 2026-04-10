// Manage categories admin page
## admin/manage_categories.php (Full)

```php
<?php
/**
 * Manage Categories Page - Admin
 */

$pageTitle = 'Manage Categories';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $categoryId = (int)$_GET['delete'];
    
    $faqCount = fetchOne("SELECT COUNT(*) as cnt FROM faqs WHERE category_id = ?", [$categoryId]);
    if ($faqCount['cnt'] > 0) {
        setError('Cannot delete category with FAQs. Move or delete FAQs first.');
    } else {
        try {
            executeQuery("DELETE FROM categories WHERE category_id = ?", [$categoryId]);
            setSuccess('Category deleted successfully.');
        } catch (Exception $e) {
            setError('Failed to delete category.');
        }
    }
    redirect('manage_categories.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    if (!validateCSRFToken($_POST['csrf_token'])) {
        setError('Invalid request.');
    } else {
        $categoryName = trim($_POST['category_name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $categoryId = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        
        if (empty($categoryName)) {
            setError('Category name is required.');
        } else {
            try {
                if ($categoryId) {
                    executeQuery(
                        "UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?",
                        [$categoryName, $description, $categoryId]
                    );
                    setSuccess('Category updated successfully.');
                } else {
                    $existing = fetchOne("SELECT category_id FROM categories WHERE category_name = ?", [$categoryName]);
                    if ($existing) {
                        setError('Category name already exists.');
                    } else {
                        executeQuery(
                            "INSERT INTO categories (category_name, description) VALUES (?, ?)",
                            [$categoryName, $description]
                        );
                        setSuccess('Category created successfully.');
                    }
                }
            } catch (Exception $e) {
                setError('Failed to save category.');
            }
        }
    }
}

displayFlashMessages();

$editCategory = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editCategory = getCategoryById((int)$_GET['edit']);
}

$categories = fetchAll("
    SELECT c.*, COUNT(f.faq_id) as faq_count 
    FROM categories c 
    LEFT JOIN faqs f ON c.category_id = f.category_id 
    GROUP BY c.category_id 
    ORDER BY c.category_name
");
?>

<div class="admin-page">
    <div class="container py-5">
        <h1 class="h3 mb-4">
            <i class="bi bi-folder text-primary me-2"></i>Manage Categories
        </h1>
        
        <!-- Add/Edit Form -->
        <div class="form-card mb-4 p-4 rounded-4 border-0 shadow-sm">
            <h5 class="mb-4">
                <i class="bi bi-<?= $editCategory ? 'pencil' : 'plus-circle' ?> me-2"></i>
                <?= $editCategory ? 'Edit Category' : 'Add New Category' ?>
            </h5>
            
            <form method="POST" action="manage_categories.php">
                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                <?php if ($editCategory): ?>
                <input type="hidden" name="category_id" value="<?= $editCategory['category_id'] ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="category_name" 
                               value="<?= h($editCategory['category_name'] ?? '') ?>" 
                               required maxlength="100">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control" name="description" 
                               value="<?= h($editCategory['description'] ?? '') ?>" 
                               maxlength="255">
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" name="save_category" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Save Category
                    </button>
                    <?php if ($editCategory): ?>
                    <a href="manage_categories.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Categories Table -->
        <div class="table-card p-4 rounded-4 border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>FAQs</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><strong><?= h($cat['category_name']) ?></strong></td>
                            <td><small class="text-muted"><?= h($cat['description'] ?? '-') ?></small></td>
                            <td><span class="badge bg-primary"><?= $cat['faq_count'] ?></span></td>
                            <td><small class="text-muted"><?= formatDate($cat['created_at']) ?></small></td>
                            <td>
                                <a href="manage_categories.php?edit=<?= $cat['category_id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($cat['faq_count'] == 0): ?>
                                <a href="manage_categories.php?delete=<?= $cat['category_id'] ?>" class="btn btn-sm btn-outline-danger" data-confirm="Delete this category?">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php else: ?>
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Has FAQs">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No categories found.</td>
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

Copy this into your `admin/manage_categories.php` file. It includes add/edit form, categories table with FAQ counts, and delete protection!