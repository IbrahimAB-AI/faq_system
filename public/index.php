<?php
/**
 * Home Page - FAQ System
 * Dark/Light mode with modern cards layout
 */

$pageTitle = 'Home';
$extraCSS = 'style';
require_once 'includes/header.php';
?>

<!-- Hero Section with Search -->
<section class="hero-section position-relative overflow-hidden">
    <div class="hero-bg-pattern"></div>
    <div class="container position-relative py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="display-4 fw-bold mb-3">
                    <span class="gradient-text">Programming FAQ</span>
                </h1>
                <p class="lead text-muted mb-4">Find answers to your coding questions. Fast, clear, with examples.</p>
                
                <!-- Search Bar -->
                <form action="search.php" method="GET" class="search-form">
                    <div class="input-group input-group-lg shadow-lg">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="Search for topics (e.g., 'center div', 'php session')..." required>
                        <button class="btn btn-primary px-4" type="submit">Search</button>
                    </div>
                </form>
                
                <!-- Quick Tags -->
                <div class="mt-3 d-flex flex-wrap justify-content-center gap-2">
                    <span class="text-muted small">Popular:</span>
                    <a href="search.php?q=html" class="badge bg-light text-dark text-decoration-none">HTML</a>
                    <a href="search.php?q=css" class="badge bg-light text-dark text-decoration-none">CSS</a>
                    <a href="search.php?q=javascript" class="badge bg-light text-dark text-decoration-none">JavaScript</a>
                    <a href="search.php?q=php" class="badge bg-light text-dark text-decoration-none">PHP</a>
                    <a href="search.php?q=git" class="badge bg-light text-dark text-decoration-none">Git</a>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <!-- Categories Section -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 mb-0">
                <i class="bi bi-folder-fill text-primary me-2"></i>Browse Categories
            </h2>
            <a href="category.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        
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
    </section>

    <!-- Recent FAQs -->
    <section class="mb-5">
        <h2 class="h4 mb-4">
            <i class="bi bi-clock-history text-primary me-2"></i>Recently Added
        </h2>
        <div class="row g-3">
            <?php
            $recentFaqs = fetchAll("
                SELECT f.faq_id, f.question, f.created_at, c.category_name 
                FROM faqs f 
                JOIN categories c ON f.category_id = c.category_id 
                ORDER BY f.created_at DESC 
                LIMIT 6
            ");
            foreach ($recentFaqs as $faq):
            ?>
            <div class="col-md-6">
                <a href="faq.php?id=<?= $faq['faq_id'] ?>" class="text-decoration-none">
                    <div class="faq-card p-3 rounded-3 border-0 shadow-sm">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 text-dark"><?= h($faq['question']) ?></h6>
                                <small class="text-muted">
                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= h($faq['category_name']) ?></span>
                                </small>
                            </div>
                            <i class="bi bi-arrow-right text-muted"></i>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Stats -->
    <section>
        <div class="row g-4">
            <?php
            $stats = [
                ['icon' => 'bi-journal-text', 'label' => 'Total FAQs', 'value' => fetchOne("SELECT COUNT(*) as cnt FROM faqs")['cnt'] ?? 0],
                ['icon' => 'bi-folder', 'label' => 'Categories', 'value' => fetchOne("SELECT COUNT(*) as cnt FROM categories")['cnt'] ?? 0],
                ['icon' => 'bi-people', 'label' => 'Community', 'value' => fetchOne("SELECT COUNT(*) as cnt FROM users")['cnt'] ?? 0],
            ];
            foreach ($stats as $stat):
            ?>
            <div class="col-4">
                <div class="stat-card text-center p-4 rounded-4 border-0 shadow-sm">
                    <i class="bi <?= $stat['icon'] ?> display-6 text-primary mb-2"></i>
                    <h3 class="mb-1 fw-bold"><?= $stat['value'] ?></h3>
                    <small class="text-muted"><?= $stat['label'] ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<style>
.hero-section {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    color: white;
}

.hero-bg-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.15) 0%, transparent 50%),
                      radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.15) 0%, transparent 50%);
}

.gradient-text {
    background: linear-gradient(135deg, #6366f1, #06b6d4);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.category-card, .faq-card, .stat-card {
    background: white;
    transition: all 0.3s ease;
}

.category-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.1) !important;
}

.category-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}
</style>

<?php require_once 'includes/footer.php'; ?>
```

