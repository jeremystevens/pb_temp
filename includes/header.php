<?php
$theme = $_COOKIE['theme'] ?? 'dark';
?>
<!DOCTYPE html>
<html class="<?= $theme ?>">
<head>
  <title><?php 
    if (isset($_GET['page'])) {
        switch($_GET['page']) {
            case 'about':
                echo 'About - ';
                break;
            case 'archive':
                echo 'Archive - ';
                break;
            case 'projects':
                echo 'Projects - ';
                break;
            case 'collections':
                echo 'Collections - ';
                break;
            default:
                break;
        }
    }
    ?>PasteForge</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css" class="light-theme" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism-okaidia.min.css" class="dark-theme" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-core.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/autoloader/prism-autoloader.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/plugins/line-numbers/prism-line-numbers.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="/style.css">
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    /* Code display styling */
    pre {
      max-width: 100%;
    }
    code {
      word-break: break-word;
    }

    /* Responsive container */
    .container {
      max-width: 960px;
      width: 100%;
      margin: 0 auto;
      padding: 0 15px;
      box-sizing: border-box;
    }

    /* Countdown styling */
    .countdown-timer {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      font-family: monospace;
      font-weight: bold;
    }

    .countdown-urgent {
      color: #ef4444;
      animation: pulse 1s infinite;
    }

    .countdown-warning {
      color: #f59e0b;
    }

    .countdown-normal {
      color: #6b7280;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }

    /* Discussion-specific styles */
    .discussion-thread:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .discussion-post {
      transition: all 0.2s ease-in-out;
    }

    .discussion-post:hover {
      background-color: rgba(147, 51, 234, 0.05);
    }

    .dark .discussion-post:hover {
      background-color: rgba(147, 51, 234, 0.1);
    }

    /* Metadata and tag layout */
    .meta-info {
      display: flex;
      flex-wrap: wrap;
      gap: 5px;
      font-size: 0.9em;
    }


  .meta-info .tag {
    white-space: nowrap;
    background: #2c2f33;
    padding: 2px 6px;
    border-radius: 4px;
  }

  /* Responsive statistics grid */
  .stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
  }

  /* Base font sizing */
  body {
    font-size: 1em;
  }

  @media (max-width: 480px) {
    body {
      font-size: 1.1em;
    }

    button {
      padding: 10px;
      font-size: 1em;
    }
  }

  </style>
  <script>
    // Countdown timer initialization
    function initializeCountdownTimers() {
      document.querySelectorAll('[data-expires]').forEach(element => {
        const expiresAt = parseInt(element.getAttribute('data-expires'));
        const timerElement = element.querySelector('.countdown-timer');

        if (timerElement && expiresAt) {
          updateCountdown(timerElement, expiresAt);

          // Update every minute
          setInterval(() => {
            updateCountdown(timerElement, expiresAt);
          }, 60000);
        }
      });
    }

    function updateCountdown(element, expiresAt) {
      const now = Math.floor(Date.now() / 1000);
      const timeLeft = expiresAt - now;

      if (timeLeft <= 0) {
        element.textContent = 'Expired';
        element.className = 'countdown-timer countdown-urgent';
        return;
      }

      const days = Math.floor(timeLeft / 86400);
      const hours = Math.floor((timeLeft % 86400) / 3600);
      const minutes = Math.floor((timeLeft % 3600) / 60);

      let timeString = '';
      let className = 'countdown-timer ';

      if (days > 0) {
        timeString = `${days}d ${hours}h`;
        className += 'countdown-normal';
      } else if (hours > 0) {
        timeString = `${hours}h ${minutes}m`;
        className += timeLeft < 3600 ? 'countdown-warning' : 'countdown-normal';
      } else {
        timeString = `${minutes}m`;
        className += timeLeft < 600 ? 'countdown-urgent' : 'countdown-warning';
      }

      element.textContent = timeString;
      element.className = className;
    }

    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            primary: '#0097FB'
          },
          animation: {
            'fade-in': 'fadeIn 0.5s ease-out',
            'slide-in': 'slideIn 0.3s ease-out'
          },
          keyframes: {
            fadeIn: {
              '0%': { opacity: '0' },
              '100%': { opacity: '1' }
            },
            slideIn: {
              '0%': { transform: 'translateX(-100%)' },
              '100%': { transform: 'translateX(0)' }
            }
          }
        }
      }
    }
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const sidebar = document.getElementById('sidebar');
      const mainContent = document.getElementById('mainContent');
      const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');

      if (sidebarToggleBtn && sidebar) {
        const updateLayout = (isOpen) => {
          sidebar.style.transform = isOpen ? 'translateX(0)' : 'translateX(-100%)';
          mainContent.style.marginLeft = isOpen ? '16rem' : '0';
          const icon = sidebarToggleBtn.querySelector('i');
          icon.className = isOpen ? 'fas fa-chevron-left' : 'fas fa-chevron-right';
        };

        // Initialize sidebar state
        let isOpen = window.innerWidth >= 1024; // lg breakpoint
        updateLayout(isOpen);

        sidebarToggleBtn.addEventListener('click', () => {
          isOpen = !isOpen;
          updateLayout(isOpen);
        });

        // Handle window resize
        window.addEventListener('resize', () => {
          if (window.innerWidth >= 1024) {
            isOpen = true;
            updateLayout(true);
          } else {
            isOpen = false;
            updateLayout(false);
          }
        });
      }

      // Initialize countdown timers
      initializeCountdownTimers();

      // Form animations
      gsap.from('.paste-form-element', {
        y: 20,
        opacity: 0,
        duration: 0.5,
        ease: 'power2.out',
        stagger: 0.1
      });
    });
  </script>
  <script>

    function toggleTheme() {
      const html = document.documentElement;
      const newTheme = html.classList.contains('dark') ? 'light' : 'dark';
      html.classList.remove('dark', 'light');
      html.classList.add(newTheme);
      document.cookie = `theme=${newTheme};path=/`;
    }

    // Live updates with improved error handling
    function fetchLatestPastes() {
      const sidebarContainer = document.getElementById('latest-pastes');
      if (!sidebarContainer) {
        // Sidebar container doesn't exist on this page, skip update
        return;
      }

      // Check if we're viewing a specific paste - don't update sidebar during paste viewing
      const pasteId = new URLSearchParams(window.location.search).get('id');
      if (pasteId) {
        // Skip updates when viewing a paste to prevent interference
        return;
      }

      // Check if we're on a page that should have sidebar updates
      const currentPage = new URLSearchParams(window.location.search).get('page');
      if (currentPage && ['login', 'signup', 'profile', 'settings'].includes(currentPage)) {
        // Skip updates on pages that don't need live sidebar
        return;
      }

      fetch('?action=latest_pastes')
        .then(response => {
          if (!response.ok) {
            // Don't throw error for non-critical sidebar updates
            return null;
          }
          return response.text();
        })
        .then(html => {
          if (html && html.trim() && sidebarContainer) {
            sidebarContainer.innerHTML = html;

            // Reinitialize countdown timers for the new content
            if (typeof initializeCountdownTimers === 'function') {
              setTimeout(initializeCountdownTimers, 100);
            }
          }
        })
        .catch(error => {
          // Silently handle errors for non-critical sidebar updates
          // Only log in development/debug mode
          if (window.location.hostname === 'localhost' || window.location.hostname.includes('replit')) {
            console.debug('Sidebar update skipped:', error.message);
          }
        });
    }

    // Only start interval updates if sidebar exists and page is appropriate
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarContainer = document.getElementById('latest-pastes');
      const currentPage = new URLSearchParams(window.location.search).get('page');
      const pasteId = new URLSearchParams(window.location.search).get('id');

      // Don't start live updates when viewing a paste or on specific pages
      if (sidebarContainer && !pasteId && (!currentPage || !['login', 'signup', 'settings'].includes(currentPage))) {
        // Initial update
        fetchLatestPastes();

        // Update every 45 seconds (reduced frequency to be less aggressive)
        setInterval(fetchLatestPastes, 45000);
      }
    });
  </script>
  <!-- Prism.js for syntax highlighting -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/prism.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-autoloader.min.js"></script>

    <!-- CryptoJS for Zero-Knowledge decryption -->
    
</head>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
  <!-- Modern Navigation Bar -->
  <nav x-data="{ mobileMenu: false }" class="bg-blue-600 dark:bg-blue-800 text-white shadow-lg fixed w-full z-10">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex justify-between h-16">
        <div class="flex items-center space-x-6">
          <a href="/" class="flex items-center space-x-3">
            <i class="fas fa-paste text-2xl"></i>
            <span class="text-xl font-bold">PasteForge</span>
          </a>
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2">
              <i class="fas fa-bars"></i>
            </button>
          <div class="hidden lg:flex space-x-4">
            <a href="/" class="hover:bg-blue-700 px-3 py-2 rounded">Home</a>
            <a href="?page=archive" class="hover:bg-blue-700 px-3 py-2 rounded">Archive</a>
            <a href="/?page=projects" class="hover:bg-blue-700 px-3 py-2 rounded">Projects</a>
            <?php if ($user_id): ?>
              <a href="?page=collections" class="hover:bg-blue-700 px-3 py-2 rounded">Collections</a>
            <?php else: ?>
              <a href="?page=about" class="hover:bg-blue-700 px-3 py-2 rounded">About</a>
            <?php endif; ?>
          </div>
        </div>
        <div x-show="mobileMenu" @click.away="mobileMenu = false" class="lg:hidden flex flex-col space-y-2 pb-4">
          <a href="/" class="hover:bg-blue-700 px-3 py-2 rounded">Home</a>
          <a href="?page=archive" class="hover:bg-blue-700 px-3 py-2 rounded">Archive</a>
          <a href="/?page=projects" class="hover:bg-blue-700 px-3 py-2 rounded">Projects</a>
          <?php if ($user_id): ?>
            <a href="?page=collections" class="hover:bg-blue-700 px-3 py-2 rounded">Collections</a>
          <?php else: ?>
            <a href="?page=about" class="hover:bg-blue-700 px-3 py-2 rounded">About</a>
          <?php endif; ?>
        </div>
        <div class="flex items-center space-x-4">
          <?php if ($user_id): ?>
            <!-- Notification Bell -->
            <a href="notifications.php" class="relative p-2 rounded hover:bg-blue-700 transition-colors">
              <i class="fas fa-bell text-lg"></i>
              <?php
              // Get unread notification count for navigation
              $stmt = $db->prepare("SELECT COUNT(*) FROM comment_notifications WHERE user_id = ? AND is_read = 0");
              $stmt->execute([$user_id]);
              $nav_unread_notifications = $stmt->fetchColumn();
              if ($nav_unread_notifications > 0):
              ?>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center min-w-[20px] animate-pulse">
                  <?= $nav_unread_notifications > 99 ? '99+' : $nav_unread_notifications ?>
                </span>
              <?php endif; ?>
            </a>
          <?php endif; ?>
          <button onclick="toggleTheme()" class="p-2 rounded hover:bg-blue-700">
            <i class="fas fa-moon"></i>
          </button>
          <?php if (!$user_id): ?>
            <div class="flex items-center space-x-2">
              <a href="?page=login" class="flex items-center space-x-2 hover:bg-blue-700 px-3 py-2 rounded">
                <i class="fas fa-sign-in-alt"></i>
                <span>Login</span>
              </a>
              <a href="?page=signup" class="flex items-center space-x-2 hover:bg-blue-700 px-3 py-2 rounded">
                <i class="fas fa-user-plus"></i>
                <span>Sign Up</span>
              </a>
            </div>
          <?php else: ?>
            <div class="relative" x-data="{ open: false }">
              <button @click="open = !open" class="flex items-center space-x-2 hover:bg-blue-700 px-3 py-2 rounded">
                <?php
                $stmt = $db->prepare("SELECT profile_image FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $user_avatar = $stmt->fetch()['profile_image'];
              ?>
              <img src="<?= $user_avatar ?? 'https://www.gravatar.com/avatar/'.md5(strtolower($username)).'?d=mp&s=32' ?>" 
                   class="w-8 h-8 rounded-full" alt="Profile">
                <span><?= htmlspecialchars($username) ?></span>
                <i class="fas fa-chevron-down ml-1"></i>
              </button>
              <div x-show="open" 
                   @click.away="open = false"
                   x-transition:enter="transition ease-out duration-100"
                   x-transition:enter-start="transform opacity-0 scale-95"
                   x-transition:enter-end="transform opacity-100 scale-100"
                   x-transition:leave="transition ease-in duration-75"
                   x-transition:leave-start="transform opacity-100 scale-100"
                   x-transition:leave-end="transform opacity-0 scale-95"
                   class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5">
                <div class="py-1">
                  <!-- Account Group -->
                  <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Account</div>
                  <a href="?page=edit-profile" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-user-edit mr-2"></i> Edit Profile
                  </a>
                  <a href="?page=profile&username=<?= urlencode($username) ?>" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-user mr-2"></i> View Profile
                  </a>
                  <a href="?page=account" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-crown mr-2"></i> Account
                  </a>
                  <a href="?page=settings" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-cog mr-2"></i> Edit Settings
                  </a>

                  <hr class="my-1 border-gray-200 dark:border-gray-700">

                  <!-- Messages Group -->
                  <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Messages</div>
                  <a href="threaded_messages.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-envelope mr-2"></i> My Messages
                  </a>

                  <hr class="my-1 border-gray-200 dark:border-gray-700">

                  <!-- Tools Group -->
                  <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tools</div>
                  <a href="project_manager.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-folder-tree mr-2"></i> Projects
                  </a>
                  <a href="following.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-users mr-2"></i> Following
                  </a>
                  <a href="?page=import-export" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-exchange-alt mr-2"></i> Import/Export
                  </a>

                  <hr class="my-1 border-gray-200 dark:border-gray-700">

                  <!-- Logout -->
                  <a href="?logout=1" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                  </a>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </nav>

    <!-- Header Controls Section -->
    <div class="container mx-auto px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900">
      <div class="flex items-center justify-center gap-3">
        <button class="text-blue-500 hover:text-blue-600 flex items-center gap-2 px-3 py-1 rounded transition-colors">
          <i class="fas fa-plus"></i>
          Create New Paste
        </button>
        <button class="text-gray-500 hover:text-gray-600 px-3 py-1 rounded border border-gray-300 dark:border-gray-600 hover:border-gray-400 transition-colors">
          Load Template
        </button>
        <button class="text-gray-500 hover:text-gray-600 px-3 py-1 rounded border border-gray-300 dark:border-gray-600 hover:border-gray-400 transition-colors">
          Import
        </button>
      </div>
    </div>
  </header>

<!-- Main Content Container -->
    <div class="flex min-h-screen bg-gray-50 dark:bg-gray-900">

        <!-- Main Content Area (full width since sidebar auto-hides) -->
        <div class="flex-1 transition-all duration-300" id="mainContent">