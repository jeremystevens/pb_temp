<script>
// Copy text function with notification
function printRawCode() {
  const codeElement = document.querySelector('pre code');
  if (!codeElement) {
    alert('Content not available for printing.');
    return;
  }

  const codeContent = codeElement.textContent;
  const printContent = document.body.innerHTML;
  document.body.innerHTML = `
    <pre style="padding: 20px; white-space: pre-wrap; font-family: monospace;">${codeContent}</pre>`;
  window.print();
  document.body.innerHTML = printContent;
}

function forkPaste(pasteId) {
  if (confirm('Fork this paste? This will create a copy that you can edit.')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
      <input type="hidden" name="action" value="fork_paste">
      <input type="hidden" name="paste_id" value="${pasteId}">
    `;
    document.body.appendChild(form);
    form.submit();
  }
}

function clonePaste() {
  const codeElement = document.querySelector('pre code');
  if (!codeElement) {
    alert('Content not available for cloning.');
    return;
  }

  const content = codeElement.textContent;

  // Store in sessionStorage
  sessionStorage.setItem('clonedContent', content);

  // Redirect to home page
  window.location.href = '/?clone=1';
}

async function copyText(text) {
  try {
    await navigator.clipboard.writeText(text);
    alert('Text copied to clipboard!');
  } catch (err) {
    alert('Failed to copy text');
  }
}

// Initialize Prism syntax highlighting
document.addEventListener('DOMContentLoaded', () => {
  if (document.querySelector('pre code')) {
    // Force re-highlighting with line numbers
    document.querySelectorAll('pre code').forEach((block) => {
      // Get the language from the class
      const language = block.className.match(/language-(\w+)/)?.[1] || 'plaintext';
      // Set the class explicitly
      block.className = `language-${language}`;
      // Add line-numbers class to parent pre
      const pre = block.parentElement;
      if (pre && pre.tagName === 'PRE') {
        pre.classList.add('line-numbers');
      }
      Prism.highlightElement(block);
    });
  }
});

async function toggleLike(pasteId) {
  try {
    const response = await fetch('', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `toggle_favorite=1&paste_id=${pasteId}`
    });

    if (response.ok) {
      const btn = document.querySelector(`button[onclick="toggleLike(${pasteId})"]`);
      const heart = btn.querySelector('.fa-heart');
      const countSpan = document.getElementById(`like-count-${pasteId}`);
      const currentCount = parseInt(countSpan.textContent.replace(/,/g, ''));

      if (heart.classList.contains('text-red-500')) {
        heart.classList.remove('text-red-500');
        heart.classList.add('text-gray-400');
        countSpan.textContent = (currentCount - 1).toLocaleString();
      } else {
        heart.classList.remove('text-gray-400');
        heart.classList.add('text-red-500');
        countSpan.textContent = (currentCount + 1).toLocaleString();
      }
    }
  } catch (error) {
    console.error('Error toggling like:', error);
  }
}
</script>

<style>
  pre code {
    word-wrap: break-word;
    white-space: pre-wrap;
    max-width: 100%;
    display: block;
  }
  .line-numbers {
    overflow-x: auto;
    max-width: 100%;
    position: relative;
    padding-left: 3.8em !important;
    counter-reset: linenumber;
  }
  .line-numbers .line-numbers-rows {
    position: absolute;
    pointer-events: none;
    top: 0;
    font-size: 100%;
    left: -3.8em;
    width: 3em;
    letter-spacing: -1px;
    border-right: 1px solid #999;
    user-select: none;
  }
  .line-numbers .line-numbers-rows > span {
    pointer-events: none;
    display: block;
    counter-increment: linenumber;
  }
  .line-numbers .line-numbers-rows > span:before {
    content: counter(linenumber);
    color: #999;
    display: block;
    padding-right: 0.8em;
    text-align: right;
  }
</style>

<script>
// Live updates for sidebar pastes
function fetchLatestPastes() {
  const sidebarContainer = document.getElementById('latest-pastes');
  if (!sidebarContainer) {
    return;
  }

  // Check if we're on a page that should have sidebar updates
  const currentPage = new URLSearchParams(window.location.search).get('page');
  if (currentPage && ['login', 'signup', 'profile', 'settings'].includes(currentPage)) {
    return;
  }

  fetch('?action=latest_pastes')
    .then(response => {
      if (!response.ok) {
        return null;
      }
      return response.text();
    })
    .then(html => {
      if (html && html.trim() && sidebarContainer) {
        sidebarContainer.innerHTML = html;

        // Reinitialize countdown timers for the new content
        if (typeof window.reinitializeCountdowns === 'function') {
          setTimeout(window.reinitializeCountdowns, 100);
        }
      }
    })
    .catch(error => {
      // Silently handle errors for non-critical sidebar updates
      if (window.location.hostname === 'localhost' || window.location.hostname.includes('replit')) {
        console.log('Sidebar update failed:', error);
      }
    });
}

// Start live updates every 30 seconds
if (document.getElementById('latest-pastes')) {
  setInterval(fetchLatestPastes, 30000);
}
</script>

<script>
// Auto-hide sidebar functionality
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content-area');
        const leftEdge = document.getElementById('left-edge-detector');

        let hideTimeout;
        let isVisible = true;

        function hideSidebar() {
            try {
                if (sidebar && mainContent) {
                    sidebar.style.transform = 'translateX(-100%)';
                    mainContent.style.marginLeft = '0';
                    if (leftEdge) leftEdge.style.display = 'block';
                    isVisible = false;
                }
            } catch (error) {
                console.error('Error hiding sidebar:', error);
            }
        }

        function showSidebar() {
            try {
                if (sidebar && mainContent) {
                    sidebar.style.transform = 'translateX(0)';
                    mainContent.style.marginLeft = '16rem';
                    if (leftEdge) leftEdge.style.display = 'none';
                    isVisible = true;
                }
            } catch (error) {
                console.error('Error showing sidebar:', error);
            }
        }

// Mouse leave sidebar - start hide timer
        if (sidebar) {
            sidebar.addEventListener('mouseleave', () => {
                try {
                    hideTimeout = setTimeout(() => {
                        hideSidebar();
                    }, 1000);  // 1 second delay
                } catch (error) {
                    console.error('Error in sidebar mouseleave:', error);
                }
            });
        }

        // Mouse enter main content when sidebar is visible - start hide timer
        if (mainContent) {
            mainContent.addEventListener('mouseenter', () => {
                try {
                    if (isVisible) {
                        hideTimeout = setTimeout(() => {
                            hideSidebar();
                        }, 2000);  // 2 second delay when entering main content
                    }
                } catch (error) {
                    console.error('Error in main content mouseenter:', error);
                }
            });

            // Cancel hide timer when leaving main content
            mainContent.addEventListener('mouseleave', () => {
                try {
                    clearTimeout(hideTimeout);
                } catch (error) {
                    console.error('Error in main content mouseleave:', error);
                }
            });
        }

        // Left edge detector for revealing hidden sidebar
        if (leftEdge) {
            leftEdge.addEventListener('mouseenter', () => {
                try {
                    clearTimeout(hideTimeout);
                    showSidebar();
                } catch (error) {
                    console.error('Error in left edge detector:', error);
                }
            });
        }
</script>

<!-- Custom JavaScript -->
    <script src="/includes/paste-form.js"></script>
    <script src="/includes/zk-decrypt.js"></script>
    <script>
    function toggleNav() {
      const links = document.getElementById("navLinks");
      if (!links) return;
      links.style.display = (links.style.display === "block") ? "none" : "block";
    }
    </script>
</body>
</html>