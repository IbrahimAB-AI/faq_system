<?php
/**
 * FAQ Detail Page - FAQ System
 */

$pageTitle = 'FAQ';
$extraCSS = 'style';
require_once 'includes/header.php';

$faqId = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$faqId) {
    echo '<div class="container py-5"><div class="alert alert-danger">FAQ not specified.</div></div>';
    require_once 'includes/footer.php';
    exit;
}

$faq = getFaqById($faqId);

if (!$faq) {
    echo '<div class="container py-5"><div class="alert alert-warning">FAQ not found.</div></div>';
    require_once 'includes/footer.php';
    exit;
}

$pageTitle = $faq['question'];
?>

<div class="container py-5">
    <nav class="breadcrumb-nav mb-4">
        <a href="index.php" class="text-decoration-none">Home</a>
        <span class="mx-2">/</span>
        <a href="category.php" class="text-decoration-none">Categories</a>
        <span class="mx-2">/</span>
        <a href="category.php?id=<?= $faq['category_id'] ?>" class="text-decoration-none"><?= h($faq['category_name']) ?></a>
        <span class="mx-2">/</span>
        <span class="text-primary">FAQ</span>
    </nav>

    <div class="faq-detail-card p-4 rounded-4 border-0 shadow-sm">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <span class="badge bg-primary bg-opacity-10 text-primary fs-6"><?= h($faq['category_name']) ?></span>
            <button class="btn btn-sm btn-outline-secondary bookmark-btn" title="Bookmark">
                <i class="bi bi-bookmark"></i>
            </button>
        </div>
        
        <h1 class="h3 mb-4"><?= h($faq['question']) ?></h1>
        
        <div class="faq-answer mb-4">
            <?= nl2br(h($faq['answer'])) ?>
        </div>
        
        <div class="faq-meta d-flex justify-content-between align-items-center py-3 border-top">
            <small class="text-muted">
                <i class="bi bi-person"></i> <?= h($faq['created_by_username']) ?>
                <span class="mx-2">|</span>
                <i class="bi bi-calendar"></i> <?= formatDate($faq['created_at']) ?>
            </small>
        </div>
        
        <!-- Was this helpful? -->
        <div class="helpful-section text-center py-4">
            <p class="text-muted mb-2">Was this helpful?</p>
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-outline-success btn-sm">
                    <i class="bi bi-hand-thumbs-up"></i> Yes
                </button>
                <button class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-hand-thumbs-down"></i> No
                </button>
            </div>
        </div>
    </div>

    <!-- Related FAQs -->
    <?php
    $relatedFaqs = fetchAll("
        SELECT faq_id, question 
        FROM faqs 
        WHERE category_id = ? AND faq_id != ? 
        LIMIT 5
    ", [$faq['category_id'], $faqId]);
    
    if (!empty($relatedFaqs)):
    ?>
    <div class="related-faqs mt-4">
        <h5 class="mb-3">
            <i class="bi bi-link-45deg text-primary me-2"></i>Related Questions
        </h5>
        <div class="list-group">
            <?php foreach ($relatedFaqs as $related): ?>
            <a href="faq.php?id=<?= $related['faq_id'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <?= h($related['question']) ?>
                <i class="bi bi-arrow-right text-muted"></i>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.breadcrumb-nav { font-size: 0.9rem; }
.faq-detail-card { background: white; }
.faq-answer { font-size: 1.05rem; line-height: 1.8; }
.bookmark-btn:hover { color: #6366f1; }
</style>

<?php require_once 'includes/footer.php'; ?>
```

---

