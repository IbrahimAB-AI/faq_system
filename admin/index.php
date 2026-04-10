## admin/index.php (Full)

```php
<?php
/**
 * Admin Dashboard - FAQ System
 */

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdmin();

displayFlashMessages();
?>

<div class="admin-page">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-speedometer2 text-primary me-2"></i>Admin Dashboard
            </h1>
            <span class="badge bg-primary">Admin</span>
        </div>
        
        <!-- Stats Cards -->
        <div class="row g-4 mb-5">
            <?php
            $stats = [
                ['icon' => 'bi-journal-text', 'label' => 'Total FAQs', 'count' => fetchOne("SELECT COUNT(*) as cnt FROM faqs")['cnt'] ?? 0, 'color' => 'primary'],
                ['icon' => 'bi-folder', 'label' => 'Categories', 'count' => fetchOne("SELECT COUNT(*) as cnt FROM categories")['cnt'] ?? 0, 'color' => 'success'],
                ['icon' => 'bi-people', 'label' => 'Users', 'count' => fetchOne("SELECT COUNT(*) as cnt FROM users")['cnt'] ?? 0, 'color' => 'info'],
                ['icon' => 'bi-chat-left-text', 'label' => 'Pending', 'count' => fetchOne("SELECT COUNT(*) as cnt FROM submitted_questions WHERE status = 'pending'")['cnt'] ?? 0, 'color' => 'warning'],
            ];
            
            foreach ($stats as $stat):
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="stat-card p-4 rounded-4 border-0 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1"><?= $stat['label'] ?></p>
                            <h2 class="mb-0"><?= $stat['count'] ?></h2>
                        </div>
                        <div class="stat-icon rounded-3">
                            <i class="bi <?= $stat['icon'] ?>"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="action-card p-4 rounded-4 border-0 shadow-sm">
                    <h5 class="mb-4">
                        <i class="bi bi-lightning text-primary me-2"></i>Quick Actions
                    </h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="manage_faqs.php" class="btn btn-outline-primary w-100 py-3">
                                <i class="bi bi-journal-plus d-block mb-2"></i>
                                Manage FAQs
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="manage_categories.php" class="btn btn-outline-success w-100 py-3">
                                <i class="bi bi-folder-plus d-block mb-2"></i>
                                Categories
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="review_submissions.php" class="btn btn-outline-warning w-100 py-3">
                                <i class="bi bi-check-circle d-block mb-2"></i>
                                Review Questions
                                <?php 
                                $pendingCount = fetchOne("SELECT COUNT(*) as cnt FROM submitted_questions WHERE status = 'pending'")['cnt'] ?? 0;
                                if ($pendingCount > 0):
                                ?>
                                <span class="badge bg-danger ms-1"><?= $pendingCount ?></span>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="search_logs.php" class="btn btn-outline-info w-100 py-3">
                                <i class="bi bi-search d-block mb-2"></i>
                                Search Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="action-card p-4 rounded-4 border-0 shadow-sm">
                    <h5 class="mb-4">
                        <i class="bi bi-clock-history text-primary me-2"></i>Recent Activity
                    </h5>
                    <div class="activity-list">
                        <?php
                        $recentFaqs = fetchAll("
                            SELECT f.question, f.created_at, u.username 
                            FROM faqs f 
                            JOIN users u ON f.created_by = u.user_id 
                            ORDER BY f.created_at DESC 
                            LIMIT 5
                        ");
                        
                        if (!empty($recentFaqs)):
                            foreach ($recentFaqs as $faq):
                        ?>
                        <div class="activity-item d-flex align-items-center py-2">
                            <div class="activity-dot me-3"></div>
                            <div class="flex-grow-1">
                                <p class="mb-0 small text-truncate"><?= h($faq['question']) ?></p>
                                <small class="text-muted">by <?= h($faq['username']) ?></small>
                            </div>
                            <small class="text-muted"><?= formatDate($faq['created_at']) ?></small>
                        </div>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <p class="text-muted">No recent activity</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Popular Searches -->
        <div class="row g-4 mt-2">
            <div class="col-12">
                <div class="action-card p-4 rounded-4 border-0 shadow-sm">
                    <h5 class="mb-4">
                        <i class="bi bi-graph-up text-primary me-2"></i>Popular Search Terms
                    </h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php
                        $popularSearches = fetchAll("
                            SELECT search_query, COUNT(*) as cnt 
                            FROM search_logs 
                            GROUP BY search_query 
                            ORDER BY cnt DESC 
                            LIMIT 8
                        ");
                        
                        if (!empty($popularSearches)):
                            foreach ($popularSearches as $search):
                        ?>
                        <a href="../search.php?q=<?= urlencode($search['search_query']) ?>" 
                           class="search-tag badge bg-light text-dark text-decoration-none">
                            <?= h($search['search_query']) ?> 
                            <span class="badge bg-primary"><?= $search['cnt'] ?></span>
                        </a>
                        <?php 
                            endforeach;
                        else:
                        ?>
                        <p class="text-muted mb-0">No search data yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-page { min-height: 100vh; }

.stat-card, .action-card { background: white; }

.stat-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.activity-dot {
    width: 8px;
    height: 8px;
    background: #6366f1;
    border-radius: 50%;
}

.search-tag {
    padding: 0.5rem 1rem;
    transition: all 0.2s;
}

.search-tag:hover {
    background: #6366f1 !important;
    color: white !important;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
```

---

