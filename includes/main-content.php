<div class="container mx-auto px-4 py-8 ml-0">
  <div class="max-w-4xl mx-auto">

    <!-- Main Form Card -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
      <script src="includes/paste-form.js"></script>
      <form id="pasteForm" method="POST" action="index.php" class="space-y-6">

        <!-- Header Action Buttons -->
        <div class="flex items-center justify-between mb-3">
          <div>
            <button class="text-blue-500 hover:text-blue-600 flex items-center gap-2 px-4 py-2 rounded transition-colors font-medium">
              <i class="fas fa-plus"></i>
              Create New Paste
            </button>
          </div>
          <div class="flex items-center gap-3">
            <button type="button" class="text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 hover:border-gray-400 transition-colors" onclick="loadTemplate()">
              Load Template
            </button>
            <button type="button" class="text-gray-600 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-200 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 hover:border-gray-400 transition-colors" onclick="importFromFile()">
              Import
            </button>
          </div>
        </div>

        <!-- Title Field -->
        <div class="form-field">
          <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Title (Optional)
          </label>
          <input 
            type="text" 
            id="title" 
            name="title" 
            placeholder="Enter a title for your paste..." 
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
          >
        </div>

        <!-- Zero Knowledge Encryption Toggle -->
        <div class="form-field">
          <div class="flex items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-700">
            <input 
              type="checkbox" 
              id="zeroKnowledge" 
              name="zero_knowledge" 
              class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
            >
            <label for="zeroKnowledge" class="text-sm font-medium text-blue-800 dark:text-blue-200">
              <i class="fas fa-shield-alt mr-2"></i>
              Zero-Knowledge Paste (Client-side encryption)
            </label>
            <div class="ml-auto">
              <span class="text-xs text-blue-600 dark:text-blue-400 bg-blue-100 dark:bg-blue-800 px-2 py-1 rounded">
                🔒 Private
              </span>
            </div>
          </div>
          <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
            Content will be encrypted in your browser before being sent to the server. Even we cannot read it.
          </p>
        </div>

        <!-- Content Area -->
        <div class="form-field">
          <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Content *
          </label>
          <textarea 
            id="content" 
            name="content" 
            required 
            rows="15" 
            placeholder="Paste your code, text, or data here..."
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 font-mono text-sm resize-vertical"
          ></textarea>
        </div>

        <!-- Advanced Options Toggle -->
        <div class="border-t border-gray-200 dark:border-gray-600 pt-6">
          <button 
            type="button" 
            id="advancedToggle" 
            class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors mb-4"
          >
            <i class="fas fa-chevron-right transition-transform" id="advancedIcon"></i>
            <span class="font-medium">Advanced Options</span>
          </button>

          <div id="advancedOptions" class="hidden space-y-6">

            <!-- Language and Expiration Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

              <!-- Language Selection -->
              <div class="form-field">
                <label for="language" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <i class="fas fa-code mr-2"></i>Language
                </label>
                <select 
                  id="language" 
                  name="language" 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="text">Plain Text</option>
                  <option value="javascript">JavaScript</option>
                  <option value="python">Python</option>
                  <option value="php">PHP</option>
                  <option value="html">HTML</option>
                  <option value="css">CSS</option>
                  <option value="json">JSON</option>
                  <option value="xml">XML</option>
                  <option value="sql">SQL</option>
                  <option value="bash">Bash</option>
                  <option value="java">Java</option>
                  <option value="cpp">C++</option>
                  <option value="csharp">C#</option>
                  <option value="ruby">Ruby</option>
                  <option value="go">Go</option>
                  <option value="rust">Rust</option>
                  <option value="typescript">TypeScript</option>
                  <option value="markdown">Markdown</option>
                </select>
              </div>

              <!-- Expiration -->
              <div class="form-field">
                <label for="expiration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  <i class="fas fa-clock mr-2"></i>Expiration
                </label>
                <select 
                  id="expiration" 
                  name="expiration" 
                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                >
                  <option value="never">Never</option>
                  <option value="10m">10 Minutes</option>
                  <option value="1h">1 Hour</option>
                  <option value="1d">1 Day</option>
                  <option value="1w">1 Week</option>
                  <option value="1M">1 Month</option>
                  <option value="6M">6 Months</option>
                  <option value="1y">1 Year</option>
                </select>
              </div>
            </div>

            <!-- Tags -->
            <div class="form-field">
              <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-tags mr-2"></i>Tags (Optional)
              </label>
              <input 
                type="text" 
                id="tags" 
                name="tags" 
                placeholder="Enter tags separated by commas (e.g., tutorial, javascript, api)"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
              >
            </div>

            <!-- Visibility and Options Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

              <!-- Visibility -->
              <div class="form-field">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                  <i class="fas fa-eye mr-2"></i>Visibility
                </label>
                <div class="space-y-3">
                  <label class="flex items-center gap-3 cursor-pointer">
                    <input 
                      type="radio" 
                      name="visibility" 
                      value="public" 
                      checked 
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                      <i class="fas fa-globe text-green-500 mr-2"></i>Public
                    </span>
                  </label>
                  <label class="flex items-center gap-3 cursor-pointer">
                    <input 
                      type="radio" 
                      name="visibility" 
                      value="unlisted" 
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                      <i class="fas fa-link text-orange-500 mr-2"></i>Unlisted
                    </span>
                  </label>
                  <label class="flex items-center gap-3 cursor-pointer <?php echo !$user_id ? 'opacity-50 cursor-not-allowed' : ''; ?>">
                    <input 
                      type="radio" 
                      name="visibility" 
                      value="private" 
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                      <?php echo !$user_id ? 'disabled' : ''; ?> <?php echo !$user_id ? 'title="Login required to create private pastes"' : ''; ?>
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                      <i class="fas fa-lock text-red-500 mr-2"></i>Private
                    </span>
                    <?php if (!$user_id): ?>
                      <span class="text-xs text-gray-500 ml-2">(Registered users only)</span>
                    <?php endif; ?>
                  </label>
                </div>
              </div>

              <!-- Special Options -->
              <div class="form-field">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                  <i class="fas fa-cog mr-2"></i>Special Options
                </label>
                <div class="space-y-3">
                  <label class="flex items-center gap-3 cursor-pointer">
                    <input 
                      type="checkbox" 
                      name="burn_after_read" 
                      class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600"
                    >
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                      <i class="fas fa-fire text-red-500 mr-2"></i>Burn after read
                    </span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Password Protection -->
            <div class="form-field">
              <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                <i class="fas fa-key mr-2"></i>Password Protection (Optional)
              </label>
              <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter password to protect this paste"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
              >
              <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">
                Viewers will need this password to access the paste
              </p>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-600">
          <div class="text-sm text-gray-600 dark:text-gray-400">
            <i class="fas fa-info-circle mr-2"></i>
            By creating a paste, you agree to our terms of service
          </div>
          <button 
            type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-medium transition-colors focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 inline-flex items-center gap-2"
          >
            <i class="fas fa-plus"></i>
            Create Paste
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// DOM ready handler
document.addEventListener('DOMContentLoaded', function() {
  // Advanced Options Toggle
  const advancedToggle = document.getElementById('advancedToggle');
  const advancedOptions = document.getElementById('advancedOptions');
  const advancedIcon = document.getElementById('advancedIcon');

  if (advancedToggle && advancedOptions && advancedIcon) {
    advancedToggle.addEventListener('click', function() {
      if (advancedOptions.classList.contains('hidden')) {
        advancedOptions.classList.remove('hidden');
        advancedIcon.style.transform = 'rotate(90deg)';
      } else {
        advancedOptions.classList.add('hidden');
        advancedIcon.style.transform = 'rotate(0deg)';
      }
    });
  }

  // Zero-Knowledge Encryption
  let encryptionKey = null;

  const contentTextarea = document.getElementById('content');
  const zeroKnowledgeCheckbox = document.getElementById('zeroKnowledge');

  if (contentTextarea && zeroKnowledgeCheckbox) {
    contentTextarea.addEventListener('blur', function() {
      if (zeroKnowledgeCheckbox.checked) {
        encryptContent();
      }
    });
  }

  async function encryptContent() {
    const content = contentTextarea.value;
    if (!content.trim()) return;

    try {
      // Generate 256-bit (32 bytes) encryption key
      const keyBytes = crypto.getRandomValues(new Uint8Array(32));

      // Convert content to bytes
      const contentBytes = new TextEncoder().encode(content);

      // Generate random IV (12 bytes for AES-GCM)
      const iv = crypto.getRandomValues(new Uint8Array(12));

      // Import the key for AES-GCM
      const cryptoKey = await crypto.subtle.importKey(
        'raw',
        keyBytes,
        { name: 'AES-GCM' },
        false,
        ['encrypt']
      );

      // Encrypt the content
      const encryptedBuffer = await crypto.subtle.encrypt(
        { name: 'AES-GCM', iv: iv },
        cryptoKey,
        contentBytes
      );

      // Combine IV and encrypted data
      const combined = new Uint8Array(iv.length + encryptedBuffer.byteLength);
      combined.set(iv, 0);
      combined.set(new Uint8Array(encryptedBuffer), iv.length);

      // Base64 encode the combined data
      const encryptedBase64 = btoa(String.fromCharCode(...combined));

      // Base64 encode the key for the URL fragment
      encryptionKey = btoa(String.fromCharCode(...keyBytes));

      // Store encrypted content in textarea
      contentTextarea.value = encryptedBase64;

      console.log('Content encrypted with AES-GCM 256-bit key');

    } catch (error) {
      console.error('Encryption failed:', error);
      throw error;
    }
  }

  function generateEncryptionKey() {
    // This function is now replaced by the crypto.getRandomValues in encryptContent
    // Keeping for compatibility but not used
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let key = '';
    for (let i = 0; i < 32; i++) {
      key += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return key;
  }

  // Form submission handler for zero-knowledge pastes
  const pasteForm = document.getElementById('pasteForm');
  if (pasteForm) {
    pasteForm.addEventListener('submit', function(e) {
      if (zeroKnowledgeCheckbox && zeroKnowledgeCheckbox.checked) {
        e.preventDefault(); // Prevent normal form submission

        // Show loading indicator
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Encrypting content...';
        submitButton.disabled = true;

        // Encrypt content if not already done
        if (!encryptionKey) {
          encryptContent();
        }

        // Submit form data via fetch
        const formData = new FormData(this);
        formData.append('zero_knowledge', '1');

        fetch('index.php', {
          method: 'POST',
          body: formData
        })
        .then(response => {
          if (response.redirected) {
            // Extract paste ID from redirect URL
            const redirectUrl = response.url;
            const pasteId = redirectUrl.split('/').pop();

            // Build zero-knowledge URL with encryption key
            const zkUrl = `${window.location.origin}/${pasteId}#zk=${encryptionKey}`;

            // Show success message and redirect
            showZeroKnowledgeSuccess(zkUrl, pasteId);
          } else {
            throw new Error('Paste creation failed');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          submitButton.innerHTML = originalText;
          submitButton.disabled = false;
          alert('Failed to create zero-knowledge paste. Please try again.');
        });
      }

      // Let normal pastes submit normally
    });
  }

  function showZeroKnowledgeSuccess(zkUrl, pasteId) {
    // Create success modal
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    modal.innerHTML = `
      <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md mx-4 shadow-xl">
        <div class="text-center mb-4">
          <i class="fas fa-shield-alt text-green-500 text-4xl mb-2"></i>
          <h3 class="text-xl font-bold text-gray-900 dark:text-white">Zero-Knowledge Paste Created!</h3>
        </div>

        <div class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded">
          <p class="text-sm text-green-800 dark:text-green-200 mb-2">
            ✅ Zero-Knowledge paste created! Save this URL – it contains your decryption key.
          </p>
          <p class="text-xs text-green-700 dark:text-green-300">
            🔐 This URL is required to view the paste. We cannot recover it if lost.
          </p>
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Your Zero-Knowledge URL:
          </label>
          <div class="flex gap-2">
            <input type="text" value="${zkUrl}" readonly 
                   class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded text-xs bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white font-mono">
            <button onclick="copyZkUrl('${zkUrl}')" 
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
              <i class="fas fa-copy"></i>
            </button>
          </div>
        </div>

        <div class="flex gap-3">
          <button onclick="window.location.href='${zkUrl}'" 
                  class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded font-medium">
            View Paste
          </button>
          <button onclick="closeZkModal()" 
                  class="flex-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded font-medium">
            Create Another
          </button>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
  }

  // Global functions for modal
  window.copyZkUrl = function(url) {
    navigator.clipboard.writeText(url).then(() => {
      const button = event.target.closest('button');
      const originalHTML = button.innerHTML;
      button.innerHTML = '<i class="fas fa-check"></i>';
      setTimeout(() => {
        button.innerHTML = originalHTML;
      }, 1000);
    });
  };

  window.closeZkModal = function() {
    const modal = document.querySelector('.fixed.inset-0');
    if (modal) {
      modal.remove();
    }
    // Reset form
    document.getElementById('pasteForm').reset();
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.innerHTML = '<i class="fas fa-plus"></i> Create Paste';
    submitButton.disabled = false;
    encryptionKey = null;
  };
});

// Template and Import functions (keep these as global functions since they're called from buttons)
// Note: loadTemplate function is now defined in paste-form.js as window.loadTemplate

function importFromFile() {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = '.txt,.js,.py,.php,.html,.css,.json,.xml,.sql,.md';
  input.onchange = function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(e) {
        const contentTextarea = document.getElementById('content');
        if (contentTextarea) {
          contentTextarea.value = e.target.result;
        }

        // Auto-detect language from file extension
        const ext = file.name.split('.').pop().toLowerCase();
        const languageMap = {
          'js': 'javascript',
          'py': 'python',
          'php': 'php',
          'html': 'html',
          'css': 'css',
          'json': 'json',
          'xml': 'xml',
          'sql': 'sql',
          'md': 'markdown',
          'cpp': 'cpp',
          'c': 'cpp',
          'java': 'java',
          'cs': 'csharp',
          'rb': 'ruby',
          'go': 'go',
          'rs': 'rust',
          'ts': 'typescript',
          'sh': 'bash'
        };

        const languageSelect = document.getElementById('language');
        if (languageMap[ext] && languageSelect) {
          languageSelect.value = languageMap[ext];
        }
      };
      reader.readAsText(file);
    }
  };
  input.click();
}
</script>