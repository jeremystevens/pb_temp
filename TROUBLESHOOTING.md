# Troubleshooting Guide

## Load Template Modal Issues

### Issue: Modal Not Loading After Button Click
**Symptoms:**
- Console shows "Load Template button found" but modal doesn't appear
- `createTemplateModal()` function never gets called
- No errors in console related to modal creation

**Root Cause:**
Duplicate `loadTemplate()` function definitions cause conflicts. The button's `onclick="loadTemplate()"` calls the wrong function.

**Solution:**
1. Ensure only one `loadTemplate` function exists - it should be defined as `window.loadTemplate` in `paste-form.js`
2. Remove any duplicate `loadTemplate` functions from other files (especially `main-content.php`)
3. The button should call `window.loadTemplate()` or rely on the global scope

**Files to Check:**
- `includes/paste-form.js` - Should contain `window.loadTemplate = function()`
- `includes/main-content.php` - Should NOT contain a `loadTemplate` function
- Any other PHP files that might have duplicate function definitions

**Debug Steps:**
1. Add `console.log("createTemplateModal called");` at the start of `createTemplateModal()`
2. Add `console.log("Modal inserted into DOM");` after the `insertAdjacentHTML` call
3. Check for "Cannot read properties of null" errors that might interfere
4. Verify the button exists and has the correct `onclick` attribute

**Prevention:**
- Use consistent function naming patterns
- Prefer `window.functionName` for global functions accessed from HTML
- Avoid mixing PHP-generated JavaScript with external JS files

## Paste Form Issues

### Create Paste Button Not Working
**Issue**: Clicking "Create Paste" does nothing or shows JavaScript errors

**Solution**: 
1. Check browser console for JavaScript errors
2. Ensure `includes/paste-form.js` is loading correctly
3. Verify form submission is not being prevented by event handlers
4. Check that the form action points to the correct endpoint

**Fixed in**: Recent modularization update - paste creation logic was restored from `index-backup.php`

### SQL Datatype Mismatch Error
**Issue**: `SQLSTATE[HY000]: General error: 20 datatype mismatch` when creating pastes

**Root Cause**: Variables passed to SQL INSERT query don't match expected column types in SQLite

**Debugging Steps**:
1. Add var_dump before SQL execution to inspect variable types:
```php
var_dump([
    'paste_id' => $paste_id,
    'user_id' => $user_id,
    'title' => $title,
    'content' => $content,
    'language' => $language,
    'password' => $password,
    'expire_time' => $expire_time,
    'is_public' => $is_public,
    'burn_after_read' => $burn_after_read,
    'zero_knowledge' => $zero_knowledge
]);
```

**Solution**:
- Ensure boolean fields are cast to integers: `$burn_after_read = !empty($_POST['burn_after_read']) ? 1 : 0;`
- Handle nullable fields properly: `$password = $password ?: null;`
- Verify database schema matches expected types