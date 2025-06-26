// Paste form functionality
window.loadTemplate = function() {
    console.log('loadTemplate called - starting modal creation');

    try {
        // Remove any existing modal first to prevent duplicates
        try {
            const existingModal = document.getElementById('templateModal');
            if (existingModal) {
                existingModal.remove();
                console.log('Removed old modal');
            }
        } catch (e) {
            console.warn('Modal removal failed:', e);
        }

        // Always create a fresh modal
        createTemplateModal();
    } catch (error) {
        console.error('Error in loadTemplate:', error);
        alert('Error loading template modal: ' + error.message);
    }
}

function createTemplateModal() {
    console.log('createTemplateModal called');

    try {
        const modalHTML = `
        <div id="templateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4" style="display: flex !important; z-index: 9999; position: fixed; top: 0; left: 0; width: 100%; height: 100%;">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-md w-full">
                <div class="flex justify-between items-center p-6 border-b dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Load Template</h3>
                    <button onclick="closeTemplateModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Close">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            <div class="p-6 space-y-6">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Select a programming language to load a starter template:</p>
                    <div id="languageList" class="grid grid-cols-2 gap-3">
                        <button type="button" data-lang="python" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">Python</button>
                        <button type="button" data-lang="javascript" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">JavaScript</button>
                        <button type="button" data-lang="php" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">PHP</button>
                        <button type="button" data-lang="cpp" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">C++</button>
                        <button type="button" data-lang="java" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">Java</button>
                        <button type="button" data-lang="go" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">Go</button>
                        <button type="button" data-lang="ruby" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">Ruby</button>
                        <button type="button" data-lang="rust" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">Rust</button>
                        <button type="button" data-lang="csharp" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">C#</button>
                        <button type="button" data-lang="swift" class="language-item px-3 py-2 border border-gray-300 dark:border-gray-600 rounded hover:bg-blue-50 dark:hover:bg-blue-900 text-gray-700 dark:text-gray-300 transition-colors">Swift</button>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">Select a language first, then click Load</p>
                    <div class="flex justify-end">
                        <button type="button" id="loadTemplateConfirm" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">Load Template</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;

        // Insert modal into DOM
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        console.log('Modal inserted into DOM');

        // Verify element ID exists
        const modalEl = document.getElementById('templateModal');
        console.log('Modal exists in DOM:', !!modalEl);

        // Initialize the modal immediately
        if (modalEl) {
            console.log('Modal found after insertion, should be visible');
            initializeTemplateModal();
        } else {
            console.error('CRITICAL: Modal not found after insertion');
            alert('Error: Could not create template modal. Please try refreshing the page.');
        }

    } catch (error) {
        console.error('Error in createTemplateModal:', error);
        alert('Error creating modal: ' + error.message);
    }
}

function initializeTemplateModal() {
    console.log('Initializing template modal');

    try {
        const languageItems = document.querySelectorAll("#languageList .language-item");
        const templateLoad = document.getElementById("loadTemplateConfirm");
        let selectedLang = null;

        // Template snippets with practical starter code
        const templateSnippets = {
            python: `#!/usr/bin/env python3
"""
Python Starter Template
Description: [Brief description of what this script does]
Author: [Your name]
Date: ${new Date().toISOString().split('T')[0]}
"""

def main():
    # Your code here
    print("Hello, World!")

    # Example: Simple calculation
    # result = 10 + 20
    # print(f"Result: {result}")

if __name__ == "__main__":
    main()`,

            javascript: `/**
 * JavaScript Starter Template
 * Description: [Brief description of what this script does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

function main() {
    // Your code here
    console.log("Hello, World!");

    // Example: Simple calculation
    // const result = 10 + 20;
    // console.log(\`Result: \${result}\`);
}

// Call the main function
main();`,

            php: `<?php
/**
 * PHP Starter Template
 * Description: [Brief description of what this script does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

function main() {
    // Your code here
    echo "Hello, World!\\n";

    // Example: Simple calculation
    // $result = 10 + 20;
    // echo "Result: $result\\n";
}

// Call the main function
main();
?>`,

            cpp: `#include <iostream>
#include <string>

/**
 * C++ Starter Template
 * Description: [Brief description of what this program does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

int main() {
    // Your code here
    std::cout << "Hello, World!" << std::endl;

    // Example: Simple calculation
    // int result = 10 + 20;
    // std::cout << "Result: " << result << std::endl;

    return 0;
}`,

            java: `/**
 * Java Starter Template
 * Description: [Brief description of what this program does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

public class Main {
    public static void main(String[] args) {
        // Your code here
        System.out.println("Hello, World!");

        // Example: Simple calculation
        // int result = 10 + 20;
        // System.out.println("Result: " + result);
    }
}`,

            go: `package main

import "fmt"

/**
 * Go Starter Template
 * Description: [Brief description of what this program does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

func main() {
    // Your code here
    fmt.Println("Hello, World!")

    // Example: Simple calculation
    // result := 10 + 20
    // fmt.Printf("Result: %d\\n", result)
}`,

            ruby: `#!/usr/bin/env ruby
# Ruby Starter Template
# Description: [Brief description of what this script does]
# Author: [Your name]
# Date: ${new Date().toISOString().split('T')[0]}

def main
    # Your code here
    puts "Hello, World!"

    # Example: Simple calculation
    # result = 10 + 20
    # puts "Result: #{result}"
end

# Call the main function if this file is executed directly
main() if __FILE__ == $0`,

            rust: `// Rust Starter Template
// Description: [Brief description of what this program does]
// Author: [Your name]
// Date: ${new Date().toISOString().split('T')[0]}

fn main() {
    // Your code here
    println!("Hello, World!");

    // Example: Simple calculation
    // let result = 10 + 20;
    // println!("Result: {}", result);
}`,

            csharp: `using System;

/**
 * C# Starter Template
 * Description: [Brief description of what this program does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

class Program {
    static void Main() {
        // Your code here
        Console.WriteLine("Hello, World!");

        // Example: Simple calculation
        // int result = 10 + 20;
        // Console.WriteLine($"Result: {result}");
    }
}`,

            swift: `import Foundation

/**
 * Swift Starter Template
 * Description: [Brief description of what this script does]
 * Author: [Your name]
 * Date: ${new Date().toISOString().split('T')[0]}
 */

func main() {
    // Your code here
    print("Hello, World!")

    // Example: Simple calculation
    // let result = 10 + 20
    // print("Result: \\(result)")
}

// Call the main function
main()`
        };

        if (!languageItems || languageItems.length === 0) {
            console.error('Language items not found in modal');
            return;
        }

        // Add click handlers for language selection
        languageItems.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                // Remove selection from all buttons
                languageItems.forEach(b => {
                    b.classList.remove('bg-blue-500', 'text-white', 'border-blue-500');
                    b.classList.add('border-gray-300', 'dark:border-gray-600');
                });

                // Add selection to clicked button
                btn.classList.add('bg-blue-500', 'text-white', 'border-blue-500');
                btn.classList.remove('border-gray-300', 'dark:border-gray-600');
                selectedLang = btn.dataset.lang;

                console.log('Selected language:', selectedLang);
            });
        });

        // Handle template loading
        if (templateLoad) {
            templateLoad.addEventListener('click', (e) => {
                e.preventDefault();

                if (!selectedLang) {
                    alert('Please select a programming language first before loading a template.');
                    return;
                }

                const textarea = document.getElementById('content');
                const langSelect = document.getElementById('language');
                const titleInput = document.getElementById('title');

                if (textarea && templateSnippets[selectedLang]) {
                    textarea.value = templateSnippets[selectedLang];

                    // Trigger input event for any handlers (like auto-resize)
                    const inputEvent = new Event('input', { bubbles: true });
                    textarea.dispatchEvent(inputEvent);

                    console.log('Template inserted for:', selectedLang);
                } else {
                    console.error('Content textarea not found or template not available');
                    alert('Error: Could not insert template');
                    return;
                }

                // Update language dropdown if available
                if (langSelect) {
                    langSelect.value = selectedLang;
                }

                // Set a default title if empty
                if (titleInput && titleInput.value.trim() === '') {
                    const languageNames = {
                        python: 'Python',
                        javascript: 'JavaScript', 
                        php: 'PHP',
                        cpp: 'C++',
                        java: 'Java',
                        go: 'Go',
                        ruby: 'Ruby',
                        rust: 'Rust',
                        csharp: 'C#',
                        swift: 'Swift'
                    };
                    titleInput.value = `${languageNames[selectedLang]} Starter Template`;
                }

                closeTemplateModal();

                // Focus on the content area
                if (textarea) {
                    textarea.focus();
                }
            });
        } else {
            console.error('Load template button not found');
        }

        console.log('Template modal initialized successfully');

    } catch (error) {
        console.error('Error initializing template modal:', error);
    }
}

function closeTemplateModal() {
    try {
        const modal = document.getElementById('templateModal');
        if (modal) {
            modal.remove();
            console.log('Modal closed and removed');
        } else {
            console.log('No modal found to close');
        }
    } catch (error) {
        console.error('Error closing modal:', error);
    }
}

// Add backdrop click to close modal
document.addEventListener('click', function(event) {
    const modal = document.getElementById('templateModal');
    if (modal && event.target === modal) {
        closeTemplateModal();
    }
});

function importFromFile() {
    // Create file input element
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = '.txt,.js,.py,.html,.css,.json,.xml,.md,.sql,.sh,.php,.cpp,.c,.java,.rb,.go,.rs,.swift,.kt,.ts,.jsx,.vue,.scss,.less,.yaml,.yml';

    fileInput.onchange = function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const content = e.target.result;
            const contentTextarea = document.getElementById('content');
            const titleInput = document.getElementById('title');
            const languageSelect = document.getElementById('language');

            if (contentTextarea) {
                contentTextarea.value = content;

                // Trigger input event for any handlers
                const inputEvent = new Event('input', { bubbles: true });
                contentTextarea.dispatchEvent(inputEvent);
            }

            // Set title based on filename if title is empty
            if (titleInput && titleInput.value.trim() === '') {
                const fileName = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                titleInput.value = fileName;
            }

            // Try to detect language from file extension
            if (languageSelect) {
                const extension = file.name.split('.').pop().toLowerCase();
                const languageMap = {
                    'js': 'javascript',
                    'py': 'python',
                    'html': 'html',
                    'css': 'css',
                    'json': 'json',
                    'xml': 'xml',
                    'md': 'markdown',
                    'sql': 'sql',
                    'sh': 'bash',
                    'php': 'php',
                    'cpp': 'cpp',
                    'c': 'cpp',
                    'java': 'java',
                    'rb': 'ruby',
                    'go': 'go',
                    'rs': 'rust',
                    'swift': 'swift',
                    'kt': 'kotlin',
                    'ts': 'typescript',
                    'jsx': 'jsx',
                    'vue': 'vue',
                    'scss': 'scss',
                    'less': 'less',
                    'yaml': 'yaml',
                    'yml': 'yaml'
                };

                if (languageMap[extension]) {
                    languageSelect.value = languageMap[extension];
                }
            }

            console.log('File imported successfully:', file.name);
        };

        reader.readAsText(file);
    };

    // Trigger file selection
    fileInput.click();
}

// Auto-resize textarea function
function autoResizeTextarea() {
    const textarea = document.getElementById('content');
    if (textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = Math.max(200, textarea.scrollHeight) + 'px';
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded - paste-form.js initializing');

    try {
        const contentTextarea = document.getElementById('content');

        if (contentTextarea) {
            // Add auto-resize functionality
            contentTextarea.addEventListener('input', autoResizeTextarea);

            // Initial resize
            autoResizeTextarea();
        }

        // Check if template modal already exists and initialize it
        const existingModal = document.getElementById('templateModal');
        if (existingModal) {
            initializeTemplateModal();
        }

        // Ensure the Load Template button works
        const loadTemplateBtn = document.querySelector('button[onclick="loadTemplate()"]');
        if (loadTemplateBtn) {
            console.log('Load Template button found and event listener added');
            // Keep the onclick but also add event listener as backup
            loadTemplateBtn.addEventListener('click', function(e) {
                console.log('Load Template button clicked via event listener');
                e.preventDefault();
                e.stopPropagation();
                window.loadTemplate();
            });
        } else {
            console.error('Load Template button not found in DOM');
            // Try to find it with different selectors
            const altBtn = document.querySelector('[onclick*="loadTemplate"]');
            if (altBtn) {
                console.log('Found Load Template button with alternate selector');
                altBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.loadTemplate();
                });
            }
        }

        console.log('paste-form.js initialization complete');

    } catch (error) {
        console.error('Error in DOMContentLoaded:', error);
    }
});

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeFormHandlers();
    initializeAdvancedOptions();
    initializeLoadTemplate();
    initializeLanguageSelector();
    initializeFormValidation();
    initializeAnimations();
    initializeZeroKnowledge();
    initializePasteDecryption();

    // Auto-resize textarea
    const textarea = document.getElementById('content');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});

// Zero-Knowledge functionality
function initializeZeroKnowledge() {
    // Handle copy button in ZK modal
    document.querySelector('#zk-copy-btn')?.addEventListener('click', function () {
        const urlInput = document.querySelector('#zk-url');
        if (urlInput) {
            urlInput.select();
            urlInput.setSelectionRange(0, 99999); // For mobile devices

            try {
                document.execCommand('copy');

                // Show confirmation toast
                const toast = document.createElement('div');
                toast.innerText = '🔗 URL copied to clipboard!';
                toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 transition-opacity duration-300';
                document.body.appendChild(toast);

                // Fade out and remove toast
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 2500);

                // Change button text temporarily
                const originalText = this.innerText;
                this.innerText = 'Copied!';
                this.classList.add('bg-green-600');
                setTimeout(() => {
                    this.innerText = originalText;
                    this.classList.remove('bg-green-600');
                }, 1000);

            } catch (err) {
                console.error('Failed to copy: ', err);
                // Fallback - show manual copy instruction
                const toast = document.createElement('div');
                toast.innerText = '⚠️ Please copy the URL manually';
                toast.className = 'fixed top-4 right-4 bg-yellow-600 text-white px-4 py-2 rounded shadow-lg z-50';
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        }
    });
}

// Paste decryption functionality
function initializePasteDecryption() {
    const hash = window.location.hash;
    if (hash.startsWith('#zk=')) {
        const key = decodeURIComponent(hash.slice(4));
        const encryptedElem = document.querySelector('[data-encrypted]');

        if (encryptedElem && typeof CryptoJS !== 'undefined') {
            try {
                const decrypted = CryptoJS.AES.decrypt(encryptedElem.dataset.encrypted, key).toString(CryptoJS.enc.Utf8);

                if (decrypted) {
                    encryptedElem.innerText = decrypted;
                    encryptedElem.classList.remove('opacity-50');
                    encryptedElem.classList.add('opacity-100');

                    // Update any syntax highlighting if Prism is available
                    if (window.Prism) {
                        Prism.highlightElement(encryptedElem);
                    }

                    // Show success indicator
                    const indicator = document.querySelector('.encryption-indicator');
                    if (indicator) {
                        indicator.innerHTML = '<i class="fas fa-unlock text-green-500"></i> <span class="text-green-600">Decrypted</span>';
                    }
                } else {
                    throw new Error('Decryption resulted in empty content');
                }
            } catch (e) {
                console.error('Decryption failed:', e);
                encryptedElem.innerHTML = '<div class="text-red-500 p-4 bg-red-50 rounded border border-red-200">' +
                    '<i class="fas fa-exclamation-triangle"></i> ' +
                    '🔐 Failed to decrypt paste. Please check the URL and ensure you have the correct decryption key.' +
                    '</div>';
                encryptedElem.classList.remove('opacity-50');
            }
        } else if (encryptedElem) {
            // CryptoJS not loaded
            encryptedElem.innerHTML = '<div class="text-yellow-600 p-4 bg-yellow-50 rounded border border-yellow-200">' +
                '<i class="fas fa-exclamation-triangle"></i> ' +
                'Encryption library not loaded. Please refresh the page.' +
                '</div>';
            encryptedElem.classList.remove('opacity-50');
        }
    }
}

function showZeroKnowledgeModal(encryptedText, encryptionKey) {
    const zkModal = document.getElementById('zk-modal');
    const zkUrl = document.getElementById('zk-url');

    if (!zkModal || !zkUrl) {
        console.error('Zero-Knowledge modal or URL input not found.');
        return;
    }

    // Construct the full URL with the zk parameter
    const baseUrl = window.location.href.split('#')[0]; // Remove existing hash
    const fullUrl = `${baseUrl}#zk=${encodeURIComponent(encryptionKey)}`;

    // Show success modal
    document.getElementById('zk-modal').classList.remove('hidden');
    document.getElementById('zk-url').value = fullUrl;

    // Wire up copy button
    const copyBtn = document.getElementById('zk-copy-btn');
    if (copyBtn) {
        copyBtn.onclick = function() {
            const urlInput = document.getElementById('zk-url');
            if (urlInput) {
                urlInput.select();
                navigator.clipboard.writeText(urlInput.value).then(() => {
                    // Show success toast
                    const toast = document.createElement('div');
                    toast.innerText = '🔗 URL copied to clipboard!';
                    toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 2500);
                }).catch(() => {
                    // Fallback for older browsers
                    document.execCommand('copy');
                    const toast = document.createElement('div');
                    toast.innerText = '🔗 URL copied to clipboard!';
                    toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50';
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 2500);
                });
            }
        };
    }
}

// Add visibility restriction handler
    function handleVisibilityRestrictions() {
        const privateRadio = document.querySelector('input[name="visibility"][value="private"]');
        if (privateRadio && privateRadio.disabled) {
            privateRadio.addEventListener('click', function(e) {
                e.preventDefault();
                showToast('Please login to create private pastes', 'warning');
            });
        }
    }

    // Initialize all form functionality when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializePasteForm();
            handleVisibilityRestrictions();
        });
    } else {
        initializePasteForm();
        handleVisibilityRestrictions();
    }