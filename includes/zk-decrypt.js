
document.addEventListener('DOMContentLoaded', async () => {
  const hash = window.location.hash;
  
  if (hash.startsWith('#zk=')) {
    const keyMatch = hash.match(/#zk=([^&]+)/);
    const statusBanner = document.querySelector('#zk-success');
    const failBanner = document.querySelector('#zk-failure');
    const defaultBanner = document.querySelector('#zk-default');
    const pasteContent = document.querySelector('#paste-content');
    const timerEl = document.querySelector('#zk-decrypt-timer');

    // Hide the default banner since we're attempting decryption
    if (defaultBanner) {
      defaultBanner.style.display = 'none';
    }

    if (keyMatch && keyMatch[1] && pasteContent) {
      const keyStr = decodeURIComponent(keyMatch[1]);
      const encryptedData = pasteContent.dataset.encrypted;

      if (encryptedData) {
        try {
          const start = performance.now();
          
          // Convert base64 key to Uint8Array
          const keyBytes = Uint8Array.from(atob(keyStr), c => c.charCodeAt(0));
          
          // Convert base64 encrypted data to Uint8Array
          const encryptedBytes = Uint8Array.from(atob(encryptedData), c => c.charCodeAt(0));
          
          // Extract IV (first 12 bytes) and ciphertext (rest)
          const iv = encryptedBytes.slice(0, 12);
          const ciphertext = encryptedBytes.slice(12);
          
          // Import the key
          const cryptoKey = await crypto.subtle.importKey(
            'raw', 
            keyBytes, 
            { name: 'AES-GCM' }, 
            false, 
            ['decrypt']
          );
          
          // Decrypt the data
          const decryptedBuffer = await crypto.subtle.decrypt(
            { name: 'AES-GCM', iv: iv }, 
            cryptoKey, 
            ciphertext
          );
          
          // Convert to string
          const decrypted = new TextDecoder().decode(decryptedBuffer);

          if (decrypted && decrypted.length > 0) {
            // Decryption successful
            const end = performance.now();
            const decryptTime = Math.round(end - start);

            // Show success banner
            if (statusBanner) {
              statusBanner.style.display = 'block';
              
              // Add decryption time
              if (timerEl) {
                timerEl.textContent = `Decrypted in ${decryptTime}ms`;
              }

              // Add client-side badge
              const badge = document.createElement('span');
              badge.innerHTML = ' • Decrypted client-side 🔐';
              badge.className = 'text-green-500 text-xs font-semibold';
              const titleEl = statusBanner.querySelector('.font-medium');
              if (titleEl) {
                titleEl.appendChild(badge);
              }
            }

            // Hide failure banner
            if (failBanner) {
              failBanner.style.display = 'none';
            }

            // Create new pre element with decrypted content
            const pre = document.createElement('pre');
            const code = document.createElement('code');
            
            // Get language from the original code element
            const originalCode = pasteContent.querySelector('code');
            const languageClass = originalCode ? originalCode.className : 'language-plaintext';
            code.className = languageClass;
            code.textContent = decrypted;
            
            pre.appendChild(code);
            pre.className = 'bg-gray-50 dark:bg-gray-800 p-4 rounded border overflow-x-auto';

            // Replace the encrypted content
            pasteContent.innerHTML = '';
            pasteContent.appendChild(pre);

            // Apply syntax highlighting if Prism is available
            if (window.Prism) {
              setTimeout(() => {
                Prism.highlightElement(code);
              }, 100);
            }

            // Show hidden tabs after successful decryption
            const commentsTab = document.querySelector('[data-tab="comments"]');
            const discussionsTab = document.querySelector('[data-tab="discussions"]');
            
            if (commentsTab && commentsTab.style.display === "none") {
              commentsTab.style.display = "";
            }
            if (discussionsTab && discussionsTab.style.display === "none") {
              discussionsTab.style.display = "";
            }

            // Remove privacy notice if it exists
            const privacyNotice = document.querySelector('.bg-blue-50.dark\\:bg-blue-900\\/20');
            if (privacyNotice && privacyNotice.textContent.includes('Zero-Knowledge Privacy Protection')) {
              privacyNotice.remove();
            }

          } else {
            throw new Error('Decryption resulted in empty content');
          }

        } catch (error) {
          console.error('Decryption failed:', error);
          
          // Hide success banner
          if (statusBanner) {
            statusBanner.style.display = 'none';
          }
          
          // Show failure banner
          if (failBanner) {
            failBanner.style.display = 'block';
          }

          // Update content to show error
          pasteContent.innerHTML = `
            <div class="text-red-500 p-6 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
              <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <strong>Decryption Failed</strong>
              </div>
              <p class="text-sm">This paste could not be decrypted. Please ensure:</p>
              <ul class="text-sm mt-2 ml-4 list-disc">
                <li>You have the complete URL including the #zk= part</li>
                <li>The decryption key hasn't been modified</li>
                <li>The paste hasn't been corrupted</li>
              </ul>
              <p class="text-xs mt-2 text-gray-500">Error: ${error.message}</p>
            </div>
          `;
        }
      } else {
        // No encrypted data found
        if (failBanner) {
          failBanner.style.display = 'block';
        }
        
        pasteContent.innerHTML = `
          <div class="text-yellow-600 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-800">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            No encrypted data found for this paste.
          </div>
        `;
      }
    } else {
      // No valid key found
      if (failBanner) {
        failBanner.style.display = 'block';
      }
      if (defaultBanner) {
        defaultBanner.style.display = 'block';
      }
    }
  }
});
