<?php
// Ensure we have a paste to display
if (!$paste) {
    echo '<div class="container mx-auto px-4 py-8">';
    echo '<div class="text-center">';
    echo '<h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Paste Not Found</h1>';
    echo '<p class="text-gray-600 dark:text-gray-400">The paste you\'re looking for doesn\'t exist or has expired.</p>';
    echo '</div>';
    echo '</div>';
    return;
}

// Handle password protection
if ($paste['password'] && !isset($_POST['paste_password'])) {
    echo '<div class="container mx-auto px-4 py-8">';
    echo '<div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">';
    echo '<h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Password Protected</h2>';
    echo '<form method="POST">';
    echo '<input type="password" name="paste_password" placeholder="Enter password" class="w-full px-4 py-2 border rounded-lg mb-4" required>';
    echo '<button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">Access Paste</button>';
    echo '</form>';
    if ($password_error) {
        echo '<p class="text-red-500 mt-2">' . htmlspecialchars($password_error) . '</p>';
    }
    echo '</div>';
    echo '</div>';
    return;
}

// Handle burn after read
if ($paste['burn_after_read']) {
    $stmt = $db->prepare("DELETE FROM pastes WHERE paste_id = ?");
    $stmt->execute([$paste['paste_id']]);
}

// Calculate paste statistics
$content_lines = substr_count($paste['content'], "\n") + 1;
$content_chars = strlen($paste['content']);
$content_size = $content_chars < 1024 ? $content_chars . ' B' : 
                ($content_chars < 1048576 ? round($content_chars/1024, 1) . ' KB' : 
                round($content_chars/1048576, 1) . ' MB');

// Time formatting
function time_ago($timestamp) {
    $time = time() - $timestamp;
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minute' . (floor($time/60) > 1 ? 's' : '') . ' ago';
    if ($time < 86400) return floor($time/3600) . ' hour' . (floor($time/3600) > 1 ? 's' : '') . ' ago';
    if ($time < 2592000) return floor($time/86400) . ' day' . (floor($time/86400) > 1 ? 's' : '') . ' ago';
    return floor($time/2592000) . ' month' . (floor($time/2592000) > 1 ? 's' : '') . ' ago';
}
?>

<div class="container mx-auto px-4 py-8 ml-0">
    <div class="max-w-6xl mx-auto">

        <!-- Paste Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-6 border border-gray-200 dark:border-gray-700">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                        <?= htmlspecialchars($paste['title']) ?>
                    </h1>
                    <div class="meta-info items-center text-gray-600 dark:text-gray-400 mt-2">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-user"></i>
                            <?= $paste['author_username'] ? '@'.htmlspecialchars($paste['author_username']) : 'Anonymous' ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-clock"></i>
                            <?= time_ago($paste['created_at']) ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-eye"></i>
                            <?= number_format($paste['views']) ?> views
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-code"></i>
                            <?= ucfirst($paste['language']) ?>
                        </span>
                        <?php if ($paste['expire_time']): ?>
                        <span class="flex items-center gap-1 text-orange-600 dark:text-orange-400" data-expires="<?= $paste['expire_time'] ?>">
                            <i class="fas fa-hourglass-half"></i>
                            <span class="countdown-timer">Calculating...</span>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($paste['tags'])): ?>
                            <?php foreach (explode(',', $paste['tags']) as $tag): ?>
                                <?php if (trim($tag)): ?>
                                    <a href="/?search=<?= urlencode(trim($tag)) ?>" class="tag">
                                        #<?= htmlspecialchars(trim($tag)) ?>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2">
                    <a href="/<?= $paste['paste_id'] ?>?raw=1" target="_blank" id="rawButton" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors text-sm" <?= $paste['zero_knowledge'] ? 'data-zke="true"' : '' ?>>
                        <i class="fas fa-file-alt mr-2"></i>Raw<?= $paste['zero_knowledge'] ? ' <i class="fas fa-lock ml-1 text-xs"></i>' : '' ?>
                    </a>
                    <button onclick="downloadPaste()" class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors text-sm">
                        <i class="fas fa-download mr-2"></i>Download
                    </button>
                    <button onclick="forkPaste()" class="inline-flex items-center px-3 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900 dark:hover:bg-blue-800 text-blue-700 dark:text-blue-300 rounded-lg transition-colors text-sm">
                        <i class="fas fa-code-branch mr-2"></i>Fork
                    </button>
                    <button onclick="reportPaste()" class="inline-flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-700 dark:text-red-300 rounded-lg transition-colors text-sm">
                        <i class="fas fa-flag mr-2"></i>Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <button onclick="switchTab('overview')" id="tab-overview" class="tab-button active border-b-2 border-blue-500 py-4 px-1 text-sm font-medium text-blue-600 dark:text-blue-400">
                        <i class="fas fa-file-alt mr-2"></i>Overview
                    </button>
                    <button onclick="switchTab('comments')" id="tab-comments" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="comments">
                        <i class="fas fa-comments mr-2"></i>Comments
                    </button>
                    <button onclick="switchTab('discussions')" id="tab-discussions" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300" data-tab="discussions">
                        <i class="fas fa-users mr-2"></i>Discussions
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="tab-content-overview" class="tab-content">
                <div class="p-6">
                    <!-- Paste Statistics -->
                    <div class="stats-grid mb-6">
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($paste['views']) ?></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Views</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($content_chars) ?></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Characters</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?= number_format($content_lines) ?></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Lines</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-2xl font-bold text-gray-900 dark:text-white"><?= $content_size ?></div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Size</div>
                        </div>
                    </div>

                    <!-- AI Summary Section (placeholder) -->
                    <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
                        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            <i class="fas fa-brain mr-2"></i>AI Summary
                        </h3>
                        <p class="text-blue-800 dark:text-blue-200 text-sm">
                            AI-powered content analysis coming soon...
                        </p>
                    </div>

                    <!-- Code Content -->
                    <div class="relative">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                <i class="fas fa-code mr-2"></i><?= ucfirst($paste['language']) ?>
                            </span>
                            <div class="flex items-center gap-2">
                                <button onclick="copyToClipboard()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                                    <i class="fas fa-copy mr-1"></i>Copy code
                                </button>
                                <button onclick="printCodeContent()" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" title="Print code">
                                    <i class="fas fa-print mr-1"></i>Print
                                </button>
                            </div>
                        </div>

                        <?php if ($paste['zero_knowledge']): ?>
                            <!-- Success banner (hidden by default, shown when decryption succeeds) -->
                            <div id="zk-success" class="hidden mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded">
                                <div class="flex items-center gap-2 text-green-800 dark:text-green-200">
                                    <i class="fas fa-unlock text-green-600"></i>
                                    <span class="font-medium">Content decrypted successfully</span>
                                    <span id="zk-decrypt-timer" class="text-xs ml-2 text-gray-400"></span>
                                </div>
                                <p class="text-xs text-green-700 dark:text-green-300 mt-1">
                                    This zero-knowledge paste was decrypted in your browser using the key from the URL.
                                </p>
                            </div>

                            <!-- Failure banner (hidden by default, shown when decryption fails) -->
                            <div id="zk-failure" class="hidden mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded">
                                <div class="flex items-center gap-2 text-red-800 dark:text-red-200">
                                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                                    <span class="font-medium">Decryption failed</span>
                                </div>
                                <p class="text-xs text-red-700 dark:text-red-300 mt-1">
                                    The encryption key in the URL may be incorrect or corrupted.
                                </p>
                            </div>

                            <!-- Default encrypted state banner (hidden when decryption is attempted) -->
                            <div id="zk-default" class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded">
                                <i class="fas fa-lock text-yellow-600 mr-2"></i>
                                <span class="text-yellow-800 dark:text-yellow-200">This is an encrypted paste. Decryption key required.</span>
                            </div>

                            <div id="paste-content" data-encrypted="<?= htmlspecialchars($paste['content']) ?>">
                                <pre class="opacity-50"><code class="language-<?= htmlspecialchars($paste['language']) ?>"><?= '🔐 Encrypted content - add #zk=YOUR_KEY to URL to decrypt' ?></code></pre>
                            </div>
                        <?php else: ?>
                            <div id="paste-content">
                                <pre><code class="language-<?= htmlspecialchars($paste['language']) ?>"><?= htmlspecialchars($paste['content']) ?></code></pre>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Burn After Read Warning -->
                    <?php if ($paste['burn_after_read']): ?>
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700">
                        <div class="flex items-center">
                            <i class="fas fa-fire text-red-500 mr-2"></i>
                            <span class="text-red-800 dark:text-red-200 font-medium">This paste has been deleted after viewing (burn after read)</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="tab-content-comments" class="tab-content hidden">
                <div class="p-6">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-comments text-4xl mb-4"></i>
                        <p>Comments feature coming soon...</p>
                    </div>
                </div>
            </div>

            <div id="tab-content-discussions" class="tab-content hidden">
                <div class="p-6">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-users text-4xl mb-4"></i>
                        <p>Discussions feature coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add zero-knowledge badge if this is an encrypted paste
if (<?= $paste['zero_knowledge'] ? 'true' : 'false' ?>) {
    document.addEventListener('DOMContentLoaded', function() {
        const titleElement = document.querySelector('h1');
        if (titleElement) {
            const badge = document.createElement('span');
            badge.className = 'ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200';
            badge.innerHTML = '<i class="fas fa-shield-alt mr-1"></i>Zero-Knowledge';
            badge.title = 'This paste is encrypted and can only be decrypted with the proper key';
            titleElement.appendChild(badge);
        }
    });
}
// Tab switching functionality
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active state from all tabs
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
        button.classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
    });

    // Show selected tab content
    document.getElementById('tab-content-' + tabName).classList.remove('hidden');

    // Add active state to selected tab
    const activeTab = document.getElementById('tab-' + tabName);
    activeTab.classList.add('active', 'border-blue-500', 'text-blue-600', 'dark:text-blue-400');
    activeTab.classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
}

// Copy to clipboard functionality with enhanced validation and toast notifications
function copyToClipboard() {
    const pasteContent = document.getElementById('paste-content');

    // Check if content is available and not encrypted without key
    if (!pasteContent || pasteContent.textContent.includes('🔐 Encrypted content')) {
        showToast('Content is not available for copying. Please decrypt the paste first if it\'s encrypted.', 'error');
        return;
    }

    const content = pasteContent.textContent;

    navigator.clipboard.writeText(content).then(() => {
        // Show success message on button
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
        button.classList.add('text-green-600', 'dark:text-green-400');

        // Show toast notification
        showToast('Copied to clipboard ✅', 'success');

        setTimeout(() => {
            button.innerHTML = originalText;
            button.classList.remove('text-green-600', 'dark:text-green-400');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy: ', err);
        // Fallback for older browsers
        try {
            const textArea = document.createElement('textarea');
            textArea.value = content;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showToast('Copied to clipboard ✅', 'success');
        } catch (fallbackErr) {
            showToast('Copy failed', 'error');
        }
    });
}

// Show toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.className = `copy-toast ${type}`;

    // Set toast styles
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '30px',
        right: '30px',
        padding: '12px 20px',
        background: type === 'error' ? '#ef4444' : '#10b981',
        color: '#ffffff',
        borderRadius: '8px',
        boxShadow: '0 4px 12px rgba(0, 0, 0, 0.15)',
        zIndex: '10000',
        fontSize: '14px',
        fontWeight: '500',
        opacity: '0',
        transform: 'translateY(20px)',
        transition: 'all 0.3s ease-in-out',
        maxWidth: '300px',
        wordWrap: 'break-word'
    });

    document.body.appendChild(toast);

    // Animate in
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    }, 10);

    // Remove after delay
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        setTimeout(() => toast.remove(), 300);
    }, type === 'error' ? 1500 : 2500);
}

// Print code content functionality (for code area button)
function printCodeContent() {
    const pasteContent = document.getElementById('paste-content');

    // Check if content is available and not encrypted without key
    if (!pasteContent || pasteContent.textContent.includes('🔐 Encrypted content')) {
        alert('Content is not available for printing. Please decrypt the paste first if it\'s encrypted.');
        return;
    }

    const content = pasteContent.textContent;
    const title = <?= json_encode($paste['title']) ?>;
    const language = <?= json_encode($paste['language']) ?>;

    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Print Code - ${title}</title>
                <style>
                    body { 
                        font-family: 'Courier New', monospace; 
                        margin: 20px; 
                        line-height: 1.4;
                        font-size: 12px;
                    }
                    .header { 
                        border-bottom: 1px solid #333; 
                        padding-bottom: 10px; 
                        margin-bottom: 15px; 
                    }
                    .title { 
                        font-size: 14px; 
                        font-weight: bold; 
                        margin-bottom: 3px; 
                    }
                    .language { 
                        font-size: 10px; 
                        color: #666; 
                    }
                    .content { 
                        white-space: pre-wrap; 
                        word-wrap: break-word;
                    }
                    @media print {
                        body { margin: 10px; }
                        .header { page-break-inside: avoid; }
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <div class="title">${title}</div>
                    <div class="language">Language: ${language.charAt(0).toUpperCase() + language.slice(1)}</div>
                </div>
                <div class="content">${content}</div>
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();

    // Small delay to ensure content is fully loaded
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}

// Download paste functionality
function downloadPaste() {
    const pasteContent = document.getElementById('paste-content');

    // Check if content is available and not encrypted without key
    if (!pasteContent || pasteContent.textContent.includes('🔐 Encrypted content')) {
        showToast('Content is not available for download. Please decrypt the paste first if it\'s encrypted.', 'error');
        return;
    }

    // Get the raw content, handling different content structures
    let content;

    // Try to get content from code element first (syntax highlighted)
    const codeElement = pasteContent.querySelector('code');
    if (codeElement) {
        content = codeElement.textContent;
    } else {
        // Fallback to direct textContent
        content = pasteContent.textContent;
    }

    // Clean up any extra whitespace while preserving intentional formatting
    // Remove leading/trailing empty lines but preserve internal structure
    content = content.replace(/^\s*\n+/, '').replace(/\n+\s*$/, '');

    const title = <?= json_encode($paste['title']) ?>;
    const language = <?= json_encode($paste['language']) ?>;

    const extensions = {
        'javascript': 'js',
        'python': 'py',
        'php': 'php',
        'html': 'html',
        'css': 'css',
        'json': 'json',
        'xml': 'xml',
        'sql': 'sql',
        'bash': 'sh',
        'java': 'java',
        'cpp': 'cpp',
        'csharp': 'cs',
        'ruby': 'rb',
        'go': 'go',
        'rust': 'rs',
        'typescript': 'ts',
        'markdown': 'md'
    };

    const ext = extensions[language] || 'txt';
    const filename = (title || 'paste') + '.' + ext;

    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    // Show confirmation toast
    showToast(`📄 Downloaded as ${filename}`, 'success');
}

// Fork paste functionality
function forkPaste() {
    const content = document.getElementById('paste-content').textContent;
    const title = <?= json_encode($paste['title']) ?>;
    const language = <?= json_encode($paste['language']) ?>;

    // Create a form to submit the forked paste
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/';

    const fields = {
        'title': 'Fork of ' + title,
        'content': content,
        'language': language,
        'visibility': 'public'
    };

    Object.keys(fields).forEach(key => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

// Report paste functionality
function reportPaste() {
    if (confirm('Are you sure you want to report this paste for inappropriate content?')) {
        // Implement reporting logic here
        alert('Paste reported. Thank you for helping keep our community safe.');
    }
}

// Countdown timer for expiration
function updateCountdowns() {
    document.querySelectorAll('.countdown-timer').forEach(timer => {
        const expires = parseInt(timer.closest('[data-expires]').getAttribute('data-expires'));
        const now = Math.floor(Date.now() / 1000);
        const remaining = expires - now;

        if (remaining <= 0) {
            timer.textContent = 'Expired';
            timer.closest('span').classList.add('text-red-600', 'dark:text-red-400');
        } else {
            const days = Math.floor(remaining / 86400);
            const hours = Math.floor((remaining % 86400) / 3600);
            const minutes = Math.floor((remaining % 3600) / 60);

            if (days > 0) {
                timer.textContent = `${days}d ${hours}h remaining`;
            } else if (hours > 0) {
                timer.textContent = `${hours}h ${minutes}m remaining`;
            } else {
                timer.textContent = `${minutes}m remaining`;
            }
        }
    });
}

// Initialize countdown timers
updateCountdowns();
setInterval(updateCountdowns, 60000); // Update every minute

// Initialize syntax highlighting if Prism is available
if (typeof Prism !== 'undefined') {
    Prism.highlightAll();
}

// Secure Raw button for Zero-Knowledge pastes
document.addEventListener('DOMContentLoaded', function() {
    const rawButton = document.getElementById('rawButton');
    const isZKE = rawButton && rawButton.hasAttribute('data-zke');

    if (isZKE) {
        const hasZkKey = window.location.hash.includes('#zk=');

        if (!hasZkKey) {
            // Disable raw access for ZKE pastes without decryption key
            rawButton.addEventListener('click', function(e) {
                e.preventDefault();
                showToast('Content is not available. Please decrypt the paste first by including the #zk= key in the URL.', 'error');
            });

            // Add visual indication
            rawButton.classList.add('opacity-75', 'cursor-not-allowed');
            rawButton.title = 'Decryption key required to view raw content';
        } else {
            // For ZKE pastes with valid key, use decrypted content
            rawButton.addEventListener('click', function(e) {
                e.preventDefault();

                const pasteContent = document.getElementById('paste-content');
                if (pasteContent && !pasteContent.textContent.includes('🔐 Encrypted content')) {
                    // Content is decrypted, show in new window
                    const content = pasteContent.textContent;
                    const title = <?= json_encode($paste['title']) ?>;

                    const newWindow = window.open('', '_blank');
                    newWindow.document.write(`
                        <html>
                            <head>
                                <title>Raw - ${title}</title>
                                <style>
                                    body { 
                                        font-family: 'Courier New', monospace; 
                                        margin: 20px; 
                                        white-space: pre-wrap; 
                                        word-wrap: break-word;
                                        line-height: 1.4;
                                    }
                                </style>
                            </head>
                            <body>${content}</body>
                        </html>
                    `);
                    newWindow.document.close();
                } else {
                    showToast('Content is not yet decrypted. Please wait for decryption to complete.', 'error');
                }
            });
        }
    }
});
</script>

<script>
// Zero-Knowledge configuration
window.PASTE_CONFIG = {
    isZeroKnowledge: <?= $paste['zero_knowledge'] ? 'true' : 'false' ?>,
    pasteId: '<?= htmlspecialchars($paste['id']) ?>'
};

// Hide Comments and Discussions tabs for ZK pastes without decryption key
document.addEventListener("DOMContentLoaded", () => {
    const zkKeyPresent = window.location.hash.includes("#zk=");
    const isZeroKnowledge = window.PASTE_CONFIG?.isZeroKnowledge;

    if (isZeroKnowledge && !zkKeyPresent) {
        // Hide the Comments and Discussions tabs
        const commentsTab = document.querySelector('[data-tab="comments"]');
        const discussionsTab = document.querySelector('[data-tab="discussions"]');

        if (commentsTab) {            commentsTab.style.display = "none";
        }
        if (discussionsTab) {
            discussionsTab.style.display = "none";
        }

        // Remove their content panels
        const commentsPanel = document.getElementById("comments-panel");
        const discussionsPanel = document.getElementById("discussions-panel");

        if (commentsPanel) commentsPanel.remove();
        if (discussionsPanel) discussionsPanel.remove();

        // Add privacy notice to Overview panel
        const overviewPanel = document.getElementById("overview-panel");
        if (overviewPanel) {
            const privacyNotice = document.createElement("div");
            privacyNotice.className = "bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4";
            privacyNotice.innerHTML = `
                <div class="flex items-center gap-2 text-blue-800 dark:text-blue-200">
                    <i class="fas fa-shield-alt"></i>
                    <strong>Zero-Knowledge Privacy Protection</strong>
                </div>
                <p class="text-sm text-blue-700 dark:text-blue-300 mt-2">
                    🔐 Comments and discussions are disabled for Zero-Knowledge pastes unless the decryption key is provided in the URL.
                </p>
            `;
            overviewPanel.insertBefore(privacyNotice, overviewPanel.firstChild);
        }

        // Ensure Overview tab is selected if Comments/Discussions were active
        const activeTab = document.querySelector('.tab-link.active');
        if (activeTab && (activeTab.dataset.tab === 'comments' || activeTab.dataset.tab === 'discussions')) {
            // Switch to Overview tab
            const overviewTab = document.querySelector('[data-tab="overview"]');
            if (overviewTab) {
                // Remove active class from all tabs
                document.querySelectorAll('.tab-link').forEach(tab => {
                    tab.classList.remove('active');
                });

                // Add active class to Overview tab
                overviewTab.classList.add('active');

                // Show Overview panel
                document.querySelectorAll('.tab-panel').forEach(panel => {
                    panel.classList.add('hidden');
                });
                const overviewPanel = document.getElementById("overview-panel");
                if (overviewPanel) {
                    overviewPanel.classList.remove('hidden');
                }
            }
        }
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/prismjs@1.29.0/components/prism-core.min.js"></script>