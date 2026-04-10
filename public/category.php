<?php
/**
 * Category Page - FAQ System
 * Shows all categories or FAQs in a specific category
 */

$pageTitle = 'Categories';
$extraCSS = 'style';
require_once 'includes/header.php';

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($categoryId):
    $category = getCategoryById($categoryId);
    
    if (!$category) {
        echo '<div class="container py-5"><div class="alert alert-danger">Category not found.</div></div>';
        require_once 'includes/footer.php';
        exit;
    }
    
    $faqs = getFaqsByCategory($categoryId);
    $pageTitle = $category['category_name'];
?>
    <div class="container py-5">
        <nav class="breadcrumb-nav mb-4">
            <a href="index.php" class="text-decoration-none">Home</a>
            <span class="mx-2">/</span>
            <a href="category.php" class="text-decoration-none">Categories</a>
            <span class="mx-2">/</span>
            <span class="text-primary"><?= h($category['category_name']) ?></span>
        </nav>
        
        <div class="category-header mb-4 p-4 rounded-4">
            <h1 class="h2 mb-2">
                <i class="bi bi-folder-fill me-2"></i><?= h($category['category_name']) ?>
            </h1>
            <?php if ($category['description']): ?>
            <p class="text-muted mb-0"><?= h($category['description']) ?></p>
            <?php endif; ?>
            <div class="mt-3">
                <span class="badge bg-primary"><?= count($faqs) ?> questions</span>
            </div>
        </div>
        
        <?php if (!empty($faqs)): ?>
        <div class="accordion" id="faqAccordion">
            <?php foreach ($faqs as $index => $faq): ?>
            <div class="accordion-item border-0 shadow-sm mb-3 rounded-3">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $faq['faq_id'] ?>">
                        <?= h($faq['question']) ?>
                    </button>
                </h2>
                <div id="faq<?= $faq['faq_id'] ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        <?= nl2br(h($faq['answer'])) ?>
                        <div class="mt-3 pt-3 border-top">
                            <small class="text-muted">
                                <i class="bi bi-person"></i> <?= h($faq['created_by_username']) ?>
                                <span class="mx-2">|</span>
                                <i class="bi bi-calendar"></i> <?= formatDate($faq['created_at']) ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>No FAQs in this category yet.
        </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <div class="container py-5">
        <h1 class="h3 mb-4">
            <i class="bi bi-folder-fill text-primary me-2"></i>Browse Categories
        </h1>
        
        <div class="row g-4">
            <?php
            $categories = getCategories();
            foreach ($categories as $cat):
                $faqCount = fetchOne("SELECT COUNT(*) as cnt FROM faqs WHERE category_id = ?", [$cat['category_id']]);
                $count = $faqCount['cnt'] ?? 0;
            ?>
            <div class="col-md-6 col-lg-4">
                <a href="category.php?id=<?= $cat['category_id'] ?>" class="text-decoration-none">
                    <div class="category-card h-100 p-4 rounded-4 border-0 shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                            <div class="category-icon rounded-3 me-3">
                                <i class="bi bi-code-slash"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-dark"><?= h($cat['category_name']) ?></h5>
                                <small class="text-muted"><?= $count ?> questions</small>
                            </div>
                        </div>
                        <p class="text-muted mb-0 small"><?= h($cat['description'] ?? 'Learn ' . strtolower($cat['category_name'])) ?></p>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<style>
.breadcrumb-nav { font-size: 0.9rem; }
.category-header { background: linear-gradient(135deg, #1a1a2e, #16213e); color: white; }
.category-card { background: white; transition: all 0.3s ease; }
.category-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important; }
.category-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #6366f1, #8b5cf6); display: flex; align-items: center; justify-content: center; color: white; }
</style>

<?php require_once 'includes/footer.php'; ?>
```
