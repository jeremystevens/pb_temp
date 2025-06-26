<?php
// Fallback to prevent undefined variable warnings
if (!isset($pastes)) {
    $pastes = [];
}
?>

<!-- Auto-hiding Sidebar with Mouse Hover Reveal -->
<div id="sidebar" class="fixed left-0 top-16 h-[calc(100vh-4rem)] bg-white dark:bg-gray-800 w-64 transform -translate-x-full transition-transform duration-300 ease-in-out shadow-lg z-50 sidebar">

  <!-- Visual indicator when sidebar is hidden -->
  <div id="sidebarIndicator" class="fixed left-0 top-1/2 transform -translate-y-1/2 w-1 h-16 bg-blue-500 opacity-50 transition-opacity duration-300 z-40"></div>

  <!-- Sidebar Content -->
  <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
    <h2 class="text-xl font-semibold flex items-center">
      <span class="mr-2">🔥</span> Recent Pastes
    </h2>
  </div>

  <div id="latest-pastes" class="divide-y divide-gray-200 dark:divide-gray-700">
    <?php foreach ($pastes as $p): ?>
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
  </div>
</div>

<script>
// Auto-hiding sidebar with mouse hover reveal functionality
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.querySelector('#sidebar');
  const indicator = document.querySelector('#sidebarIndicator');
  let hideTimeout;

  if (!sidebar || !indicator) return;

  // Mouse move detection for left edge trigger
  document.addEventListener('mousemove', function(e) {
    if (e.clientX <= 20) {
      // Mouse is near left edge - show sidebar
      sidebar.classList.remove('-translate-x-full');
      sidebar.classList.add('translate-x-0');
      indicator.style.opacity = '0';

      // Clear any pending hide timeout
      if (hideTimeout) {
        clearTimeout(hideTimeout);
        hideTimeout = null;
      }
    } else if (!sidebar.matches(':hover') && e.clientX > 280) {
      // Mouse is away from sidebar and not hovering - hide after delay
      if (!hideTimeout) {
        hideTimeout = setTimeout(() => {
          sidebar.classList.remove('translate-x-0');
          sidebar.classList.add('-translate-x-full');
          indicator.style.opacity = '0.5';
          hideTimeout = null;
        }, 200);
      }
    }
  });

  // Hide when mouse leaves sidebar
  sidebar.addEventListener('mouseleave', function() {
    if (!hideTimeout) {
      hideTimeout = setTimeout(() => {
        sidebar.classList.remove('translate-x-0');
        sidebar.classList.add('-translate-x-full');
        indicator.style.opacity = '0.5';
        hideTimeout = null;
      }, 200);
    }
  });

  // Show sidebar when focused for keyboard navigation
  sidebar.addEventListener('focusin', function() {
    sidebar.classList.remove('-translate-x-full');
    sidebar.classList.add('translate-x-0');
    indicator.style.opacity = '0';

    if (hideTimeout) {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    }
  });

  // ARIA attributes for accessibility
  sidebar.setAttribute('aria-label', 'Recent pastes sidebar');
  sidebar.setAttribute('role', 'complementary');
});

// Initialize countdown timers for expiration
function initializeCountdownTimers() {
    const countdownElements = document.querySelectorAll('.countdown-timer');

    countdownElements.forEach(function(element) {
        const container = element.closest('[data-expires]');
        if (!container) return;

        const expireTime = parseInt(container.getAttribute('data-expires'));
        if (!expireTime) return;

        function updateCountdown() {
            const now = Math.floor(Date.now() / 1000);
            const remaining = expireTime - now;

            if (remaining <= 0) {
                element.textContent = 'Expired';
                element.style.color = '#ef4444'; // red color
                return;
            }

            let text = 'Expires in ';
            if (remaining < 60) {
                text += remaining + ' second' + (remaining !== 1 ? 's' : '');
            } else if (remaining < 3600) {
                const minutes = Math.floor(remaining / 60);
                text += minutes + ' minute' + (minutes !== 1 ? 's' : '');
            } else if (remaining < 86400) {
                const hours = Math.floor(remaining / 3600);
                text += hours + ' hour' + (hours !== 1 ? 's' : '');
            } else {
                const days = Math.floor(remaining / 86400);
                text += days + ' day' + (days !== 1 ? 's' : '');
            }

            element.textContent = text;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initializeCountdownTimers);

// Re-initialize when sidebar content is updated via AJAX
if (typeof window.reinitializeCountdowns === 'undefined') {
    window.reinitializeCountdowns = initializeCountdownTimers;
}
</script>