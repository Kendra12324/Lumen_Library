<?php
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/model/Book.php';

$db = new Database();
$bookModel = new Book();

$query = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];

if ($query) {
    $results = $bookModel->searchBooks($query);
}

function getBookColor($id) {
    $colors = [
        'linear-gradient(135deg, #006064 0%, #00838f 100%)', 
        'linear-gradient(135deg, #1565c0 0%, #42a5f5 100%)', 
        'linear-gradient(135deg, #ad1457 0%, #ec407a 100%)', 
        'linear-gradient(135deg, #e65100 0%, #ff9800 100%)', 
        'linear-gradient(135deg, #2e7d32 0%, #66bb6a 100%)', 
        'linear-gradient(135deg, #4527a0 0%, #7e57c2 100%)', 
    ];
    
    return $colors[$id % count($colors)];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search: <?= htmlspecialchars($query) ?> | Lumen Lore</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <link rel="stylesheet" href="search_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <nav>
        <a href="index.php" class="logo"><i class="fas fa-book-open"></i> Lumen Lore</a>
        <a href="index.php" class="nav-link">Back to Home</a>
    </nav>

    <header class="results-header">
        <div>
            <h2>Archive Search</h2>
            <p style="font-family: sans-serif; font-size: 0.9rem; margin-top: 5px;">
                <?php if ($query): ?> Results for: <strong>"<?= htmlspecialchars($query) ?>"</strong>
                <?php else: ?> Search our collection <?php endif; ?>
            </p>
        </div>
        <form action="search.php" method="GET" class="compact-search">
            <input type="text" name="search" value="<?= htmlspecialchars($query) ?>" placeholder="Search again..." required>
            <button type="submit"><i class="fas fa-search"></i></button>
        </form>
    </header>

    <main class="main-container">
        <?php if (!empty($results)): ?>
            <div class="book-grid">
                <?php foreach ($results as $book): 
                    $isAvailable = ($book['Copies_Available'] > 0 && $book['Status'] == 'Available');
                    $bgStyle = getBookColor($book['Book_ID']); // Get Random Color
                ?>
                <div class="book-card">
                    
                    <div class="book-cover" style="background: <?= $bgStyle ?>;">
                        <?php if($isAvailable): ?>
                            <span class="status-badge">Available</span>
                        <?php else: ?>
                            <span class="status-badge out">Out of Stock</span>
                        <?php endif; ?>
                        
                        <i class="fas fa-book cover-icon"></i>
                        <div class="cover-title"><?= htmlspecialchars($book['Title']) ?></div>
                        <div class="cover-author">by <?= htmlspecialchars($book['Author']) ?></div>
                    </div>

                    <div class="book-info">
                        <div class="info-row">
                            <span><i class="fas fa-tag"></i> <?= htmlspecialchars($book['Category']) ?></span>
                            <span><?= htmlspecialchars($book['Year']) ?></span>
                        </div>
                        <div class="info-row">
                            <span><i class="fas fa-map-marker-alt"></i> Shelf: <?= htmlspecialchars($book['Location']) ?></span>
                        </div>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results">
                <i class="fas fa-search fa-4x" style="margin-bottom:20px; opacity:0.3;"></i>
                <h3>No scrolls found.</h3>
                <p>Try a different keyword.</p>
            </div>
        <?php endif; ?>
    </main>

    <footer>&copy; 2025 Lumen Lore Library</footer>

</body>
</html>