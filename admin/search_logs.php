// Search logs admin page
## admin/search_logs.php (Full)

```php
<?php
/**
 * Search Logs Page - Admin
 * View what users are searching for
 */

$pageTitle = 'Search Logs';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$totalCount = fetchOne("SELECT COUNT(*) as cnt FROM search_logs")['cnt'] ?? 0;
$totalPages = ceil($totalCount / $limit);

$searchLogs = fetchAll("
    SELECT sl.*, u.username 
    FROM search_logs sl
    LEFT JOIN users u ON sl.user_id = u.user_id
    ORDER BY sl.searched_at DESC
    LIMIT ? OFFSET ?
", [$limit, $offset]);

$popularSearches = fetchAll("
    SELECT search_query, COUNT(*) as cnt, MAX(searched_at) as last_searched
    FROM search_logs
    GROUP BY search_query
    ORDER BY cnt DESC
    LIMIT 20
");
?>

<div class="admin-page">
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">
                <i class="bi bi-search text-primary me-2"></i>Search Logs
            </h1>
            <span class="badge bg-primary fs-6"><?= $totalCount ?> Total Searches</span>
        </div>
        
        <!-- Popular Searches -->
        <div class="popular-section mb-4 p-4 rounded-4 border-0 shadow-sm">
            <h5 class="mb-4">
                <i class="bi bi-graph-up text-primary me-2"></i>Popular Search Terms
            </h5>
            
            <?php if (!empty($popularSearches)): ?>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($popularSearches as $search): ?>
                <a href="../search.php?q=<?= urlencode($search['search_query']) ?>" 
                   class="search-tag badge bg-light text-dark text-decoration-none">
                    <?= h($search['search_query']) ?> 
                    <span class="badge bg-primary"><?= $search['cnt'] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-muted mb-0">No search data yet.</p>
            <?php endif; ?>
        </div>
        
        <!-- Recent Searches Table -->
        <div class="table-card p-4 rounded-4 border-0 shadow-sm">
            <h5 class="mb-4">
                <i class="bi bi-clock-history text-primary me-2"></i>Recent Searches
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Search Query</th>
                            <th>User</th>
                            <th>Date/Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($searchLogs as $log): ?>
                        <tr>
                            <td>
                                <a href="../search.php?q=<?= urlencode($log['search_query']) ?>" class="text-decoration-none">
                                    <i class="bi bi-search me-2 text-muted"></i>
                                    <?= h($log['search_query']) ?>
                                </a>
                            </td>
                            <td>
                                <?php if ($log['username']): ?>
                                <span class="badge bg-info"><?= h($log['username']) ?></span>
                                <?php else: ?>
                                <span class="text-muted">Guest</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= date('M d, Y h:i A', strtotime($log['searched_at'])) ?></small></td>
                            <td>
                                <a href="../search.php?q=<?= urlencode($log['search_query']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($searchLogs)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">No search logs found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center mb-0">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.admin-page { min-height: 100vh; }
.popular-section, .table-card { background: white; }
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
