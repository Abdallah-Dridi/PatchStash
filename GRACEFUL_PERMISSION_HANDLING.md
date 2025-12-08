## Permission Denial Handling - Graceful Error Messages

### Overview
When users attempt to perform an action they don't have permission for, the application now handles it gracefully by:
1. Checking if the user has the required permission
2. If missing, creating a flash message to inform the user
3. Redirecting them back to the appropriate page instead of throwing a 403 error

### How It Works

#### PermissionChecker Service (src/Service/PermissionChecker.php)

**New Method: `requirePermissionOrRedirect()`**
```php
public function requirePermissionOrRedirect(
    string $permissionName, 
    string $redirectRoute = 'app_dashboard'
): ?RedirectResponse
```

**Parameters:**
- `$permissionName` (string): The permission to check (e.g., 'create_user')
- `$redirectRoute` (string): Route name to redirect to if permission denied (defaults to 'app_dashboard')

**Returns:**
- `null` if permission is granted (proceed with action)
- `RedirectResponse` if permission is denied (with flash message)

**Example:**
```php
$redirect = $permissionChecker->requirePermissionOrRedirect('create_user', 'app_admin_users');
if ($redirect) return $redirect; // Stop execution and redirect with error message
```

### Flash Message Categories

Flash messages are color-coded based on type:

| Type | Color | Used For |
|------|-------|----------|
| `error` | Red | Permission denied, access denied, errors |
| `success` | Green | Operation completed successfully |
| `warning` | Orange | Warnings and notices |
| `info` | Blue | Information and notices |

### Controller Implementation

All entity controllers now use the graceful permission check pattern:

**Example from AdminUserController:**
```php
#[Route('/new', name: 'app_admin_users_new', methods: ['GET', 'POST'])]
public function new(
    Request $request, 
    UserRepository $userRepository, 
    UserPasswordHasherInterface $passwordHasher,
    PermissionChecker $permissionChecker
): Response {
    // Check permission; redirect if denied
    $redirect = $permissionChecker->requirePermissionOrRedirect('create_user', 'app_admin_users');
    if ($redirect) return $redirect;
    
    // Permission granted - continue with logic
    $user = new User();
    $form = $this->createForm(AdminUserType::class, $user);
    // ... rest of action
}
```

### Implemented In

**All 7 entity controllers:**
- ✅ AdminUserController (new, edit, delete)
- ✅ ProjectController (new, edit, delete)
- ✅ AssetController (new, edit, delete)
- ✅ ModuleController (new, edit, delete)
- ✅ VulnerabilityController (new, edit, delete)
- ✅ PatchCycleController (new, edit, delete)
- ✅ ReportController (new)

### Flash Message Display

**Location:** `templates/base.html.twig`

Flash messages are displayed below the header in a styled alert box:
- Red with left border for errors
- Green for success messages
- Orange for warnings
- Blue for info messages

**HTML Output:**
```html
<div class="flash-message flash-error" style="...">
    You do not have permission to perform this action. Required: create user
</div>
```

### User Experience Flow

#### Scenario: User Without Permission Tries to Create User

1. **User clicks "New User" button**
   - Navigates to `/admin/users/new`

2. **Controller checks permission**
   ```php
   $redirect = $permissionChecker->requirePermissionOrRedirect('create_user', 'app_admin_users');
   ```

3. **Permission check fails**
   - Permission "create_user" not assigned to user's role

4. **Graceful handling**
   - Flash message created: "You do not have permission to perform this action. Required: create user"
   - User redirected to `/admin/users` (the users list page)

5. **User sees**
   - Red error banner at top of users list page
   - Error message explaining what permission is needed
   - Can view the list but cannot create/edit/delete

### Redirecting to Appropriate Pages

Each controller redirects to a sensible page when permission is denied:

| Controller | Action | Denied Redirect |
|-----------|--------|-----------------|
| AdminUserController | new/edit/delete | app_admin_users (users list) |
| ProjectController | new/edit/delete | app_project_index (projects list) |
| AssetController | new/edit/delete | app_asset_index (assets list) |
| ModuleController | new/edit/delete | app_module_index (modules list) |
| VulnerabilityController | new/edit/delete | app_vulnerability_index (vulnerabilities list) |
| PatchCycleController | new/edit/delete | app_patch_cycle_index (patch cycles list) |
| ReportController | new | app_dashboard (home) |

### Testing Permission Denial

**Test Case 1: Remove Permission and Verify Graceful Handling**

1. Go to `/admin/roles`
2. Click "Edit" on "Operator" role
3. Uncheck `create_project` permission
4. Save changes
5. Log in as Operator user
6. Try to access `/project/new` or click "New Project"
7. **Expected Result:**
   - Red error message appears: "You do not have permission to perform this action. Required: create project"
   - User redirected to `/project` (projects list)
   - No 403 page or crash

**Test Case 2: Verify Admin Can Still Perform Actions**

1. Log in as Admin user
2. Navigate to `/project/new`
3. **Expected Result:**
   - New project form loads normally
   - Admin can create project successfully

### Error Message Format

The error message is automatically formatted from the permission name:
- `create_user` → "You do not have permission to perform this action. Required: create user"
- `edit_project` → "You do not have permission to perform this action. Required: edit project"
- `delete_asset` → "You do not have permission to perform this action. Required: delete asset"

The permission name is converted by:
1. Replacing underscores with spaces
2. Converting to title case

### Customizing Redirect Behavior

If you want to redirect to a different page when permission is denied:

```php
// Default: redirects to 'app_dashboard'
$redirect = $permissionChecker->requirePermissionOrRedirect('create_user');

// Custom: redirect to users list instead
$redirect = $permissionChecker->requirePermissionOrRedirect('create_user', 'app_admin_users');

// Handle redirect
if ($redirect) return $redirect;
```

### Using hasPermission() for Conditional UI

For optional permission checks (e.g., hide buttons from unauthorized users):

```php
// In controller
$canCreate = $permissionChecker->hasPermission('create_user');

// In Twig template
{% if canCreate %}
    <a href="{{ path('app_admin_users_new') }}" class="btn">New User</a>
{% endif %}
```

### Debugging Permission Issues

**If user sees error but should have permission:**
1. Check `/admin/roles` - verify user's role has permission checked
2. Clear cache: `php bin/console cache:clear`
3. Check database: `SELECT * FROM role_permissions WHERE role = 'Operator'`
4. Verify user's role: `SELECT role FROM users WHERE username = 'user@example.com'`

**If no error message appears:**
1. Verify flash message is being set: Check `$this->session->getFlashBag()` in controller
2. Verify base template has flash display code
3. Check browser: Is response a redirect? Flash messages only survive one redirect.

### Backwards Compatibility

The old `requirePermission()` method still exists and works the same way (throws AccessDeniedException), so existing code won't break. The new `requirePermissionOrRedirect()` is the preferred approach going forward.

### Session Configuration

Flash messages use Symfony's native session flash bags. No additional configuration is needed if you have sessions enabled in your Symfony configuration.

To verify sessions are configured:
```bash
php bin/console config:dump framework | grep -A 5 session
```
