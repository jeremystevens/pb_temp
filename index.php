<?php
// Session and configuration setup
session_start();

// PHP-based routing fallback for clean URLs
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = trim($uri, '/');

if ($uri && !isset($_GET['view']) && !isset($_GET['page']) && !file_exists($uri)) {
    $_GET['view'] = $uri;
}

// Database connection
require_once 'database.php';
$db = Database::getInstance()->getConnection();

// User session variables
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;

// Helper functions
function generatePasteId($length = 8) {
    global $db;
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    do {
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= $chars[random_int(0, strlen($chars) - 1)];
        }

        // Check if this ID already exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM pastes WHERE paste_id = ?");
        $stmt->execute([$id]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);

    return $id;
}

function human_time_diff($timestamp) {
    $time = time() - $timestamp;

    if ($time < 60) {
        return 'just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        $months = floor($time / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: /');
    exit;
}

// Handle AJAX request for latest pastes
if (isset($_GET['action']) && $_GET['action'] === 'latest_pastes') {
    $stmt = $db->prepare("
        SELECT p.*, u.username 
        FROM pastes p 
        LEFT JOIN users u ON p.user_id = u.id 
        WHERE p.is_public = 1 AND (p.expire_time IS NULL OR p.expire_time > ?) 
        ORDER BY p.created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([time()]);
    $pastes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pastes as $p): ?>
        <a href="/<?= $p['paste_id'] ?>" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <div class="flex items-start gap-3">
                <i class="fas fa-code text-blue-500 mt-1 flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <div class="font-medium truncate text-gray-900 dark:text-white">
                        "<?= htmlspecialchars($p['title']) ?>"
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        by <?= $p['username'] ? '@'.htmlspecialchars($p['username']) : 'Anonymous' ?>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        <?= $p['views'] ?> views · <?= human_time_diff($p['created_at']) ?>
                        <?php if ($p['expire_time']): ?>
                            <div class="text-xs mt-1" data-expires="<?= $p['expire_time'] ?>">
                                <i class="fas fa-clock"></i> <span class="countdown-timer">Calculating...</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
    <script>
      // Reinitialize countdown timers for the new content
            if (typeof window.reinitializeCountdowns === 'function') {
              setTimeout(window.reinitializeCountdowns, 100);
            }
    </script>
    <?php
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle paste creation
    if (isset($_POST['content']) && !empty($_POST['content'])) {
        $title = $_POST['title'] ?: 'Untitled Paste';
        $content = $_POST['content'];
        $language = $_POST['language'] ?? 'plaintext';
        $expire = $_POST['expiration'] ?? 'never';  // Fixed field name from 'expire' to 'expiration'
        $password = $_POST['password'] ?? null;
        $tags = $_POST['tags'] ?? '';

        // Handle visibility radio buttons
        $visibility = $_POST['visibility'] ?? 'public';
        
        // Enforce authentication requirement for private pastes
        if ($visibility === 'private' && !$user_id) {
            // Override to unlisted for anonymous users attempting private
            $visibility = 'unlisted';
        }
        
        $is_public = ($visibility === 'public') ? 1 : 0;

        // Properly cast boolean fields to integers for SQLite
        $burn_after_read = !empty($_POST['burn_after_read']) ? 1 : 0;
        $zero_knowledge = !empty($_POST['zero_knowledge']) ? 1 : 0;

        // Convert expiration values to seconds for expire_time calculation
        $expiration_seconds = [
            '10m' => 10 * 60,
            '1h' => 60 * 60,
            '1d' => 24 * 60 * 60,
            '1w' => 7 * 24 * 60 * 60,
            '1M' => 30 * 24 * 60 * 60,
            '6M' => 6 * 30 * 24 * 60 * 60,
            '1y' => 365 * 24 * 60 * 60
        ];

        $expire_time = null;
        if ($expire !== 'never' && isset($expiration_seconds[$expire])) {
            $expire_time = time() + $expiration_seconds[$expire];
        }

        // Ensure expire_time is integer or null as per schema requirements
        $expire_time = isset($_POST['expire_time']) && is_numeric($_POST['expire_time']) ? (int)$_POST['expire_time'] : $expire_time;

        // Generate unique paste ID
        $paste_id = generatePasteId();

        // Ensure nullable fields are properly handled
        $expire_time = $expire_time ?: null;
        $password = $password ?: null;

        // Use integer timestamp for created_at to match schema
        $created_at = time();

        $stmt = $db->prepare("
            INSERT INTO pastes (paste_id, user_id, title, content, language, password, expire_time, is_public, burn_after_read, zero_knowledge, created_at, tags) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if ($stmt->execute([$paste_id, $user_id, $title, $content, $language, $password, $expire_time, $is_public, $burn_after_read, $zero_knowledge, $created_at, $tags])) {
            // Redirect to clean URL without query parameters
            header("Location: /$paste_id");
            exit;
        } else {
            $error = "Failed to create paste. Please try again.";
        }
    }

    // Handle login
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: /');
            exit;
        } else {
            $login_error = "Invalid username or password";
        }
    }
}

// Handle raw paste viewing first (before any HTML processing)
if (isset($_GET['raw']) && $_GET['raw'] == '1' && isset($_GET['view'])) {
    $requested_id = $_GET['view'];

    $stmt = $db->prepare("
        SELECT content, zero_knowledge FROM pastes 
        WHERE paste_id = ? AND (expire_time IS NULL OR expire_time > ?)
    ");
    $stmt->execute([$requested_id, time()]);
    $paste_raw = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paste_raw) {
        // Block raw access for ZKE if no key in fragment
        if ($paste_raw['zero_knowledge']) {
            // Show security message instead of content
            header('Content-Type: text/plain; charset=utf-8');
            echo "⚠️ This is a zero-knowledge paste.\n\nTo view this paste, include the decryption key in the URL (after #zk=...).";
            exit;
        }

        // Normal raw paste output
        header('Content-Type: text/plain; charset=utf-8');
        echo $paste_raw['content'];
        exit;
    } else {
        header('HTTP/1.0 404 Not Found');
        echo 'Paste not found or expired.';
        exit;
    }
}

// Get paste if ID is provided (support both ?id= and clean URLs via ?view=)
$paste = null;
$password_error = null;

$requested_id = null;
if (isset($_GET['view'])) {
    $requested_id = $_GET['view'];
}

if ($requested_id) {

    $stmt = $db->prepare("
        SELECT p.*, u.username as author_username 
        FROM pastes p 
        LEFT JOIN users u ON p.user_id = u.id 
        WHERE p.paste_id = ? AND (p.expire_time IS NULL OR p.expire_time > ?)
    ");
    $stmt->execute([$requested_id, time()]);
    $paste = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paste) {
        // Handle password protection
        if ($paste['password'] && !isset($_POST['paste_password'])) {
            // Show password form - will be handled in view display
        } elseif ($paste['password'] && isset($_POST['paste_password'])) {
            if ($_POST['paste_password'] !== $paste['password']) {
                $password_error = "Incorrect password";
                $paste = null;
            }
        }

        if ($paste) {
            // Only increment view count once per IP address
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

            // Check if this IP has already viewed this paste
            $stmt = $db->prepare("SELECT COUNT(*) FROM paste_views WHERE paste_id = ? AND ip_address = ?");
            $stmt->execute([$requested_id, $ip]);
            $already_viewed = $stmt->fetchColumn() > 0;

            if (!$already_viewed) {
                // Record the view
                $stmt = $db->prepare("INSERT INTO paste_views (paste_id, ip_address, viewed_at) VALUES (?, ?, ?)");
                $stmt->execute([$requested_id, $ip, time()]);

                // Increment view count
                $stmt = $db->prepare("UPDATE pastes SET views = views + 1 WHERE paste_id = ?");
                $stmt->execute([$requested_id]);
            }

            // Display the paste view instead of main form
            include 'includes/header.php';
            include 'includes/sidebar.php';
            include 'includes/view-paste.php';
            include 'includes/footer.php';
            exit;
        }
    } else {
        // Paste not found or expired
        include 'includes/header.php';
        include 'includes/sidebar.php';
        echo '<div class="container mx-auto px-4 py-8 ml-0">';
        echo '<div class="max-w-4xl mx-auto">';
        echo '<div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700 text-center">';
        echo '<i class="fas fa-exclamation-triangle text-yellow-500 text-4xl mb-4"></i>';
        echo '<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Paste Not Found</h1>';
        echo '<p class="text-gray-600 dark:text-gray-400 mb-4">The paste you\'re looking for doesn\'t exist or has expired.</p>';
        echo '<a href="/" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">';
        echo '<i class="fas fa-home mr-2"></i>Go Home</a>';
        echo '</div></div></div>';
        include 'includes/footer.php';
        exit;
    }
}

// Get recent pastes for sidebar
$stmt = $db->prepare("
    SELECT p.*, u.username 
    FROM pastes p 
    LEFT JOIN users u ON p.user_id = u.id 
    WHERE p.is_public = 1 AND (p.expire_time IS NULL OR p.expire_time > ?) 
    ORDER BY p.created_at DESC 
    LIMIT 10
");
$stmt->execute([time()]);
$pastes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user pastes for dashboard
$user_pastes = [];
if ($user_id) {
    $stmt = $db->prepare("
        SELECT * FROM pastes 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user_id]);
    $user_pastes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}



// Check for page routing
if (isset($_GET['page'])) {
    switch($_GET['page']) {
        case 'about':
            include 'includes/header.php';
            include 'includes/sidebar.php';
            echo '<div class="container mx-auto px-4 py-8 ml-0">';
            echo '<div class="max-w-4xl mx-auto">';
            include 'includes/about.php';
            echo '</div></div>';
            include 'includes/footer.php';
            exit;
            break;
        default:
            // Continue with normal flow for other pages
            break;
    }
}

// Include the modular components for main page
include 'includes/header.php';
include 'includes/sidebar.php'; 
include 'includes/main-content.php';
include 'includes/footer.php';
?>
</replit_final_file>