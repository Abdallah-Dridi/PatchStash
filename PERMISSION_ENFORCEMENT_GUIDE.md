# Permission Enforcement Guide

## Overview
Permissions are now fully enforced across all entity management endpoints in PatchStash. The `PermissionChecker` service validates that users have the required permissions before allowing them to perform create, edit, or delete operations.

## How It Works

1. **User Action**: User attempts to create/edit/delete an entity (e.g., create a new user)
2. **Controller Check**: The controller's action method calls `$permissionChecker->requirePermission('create_user')`
3. **Lookup**: PermissionChecker queries the database for the user's role → finds associated permissions
4. **Validation**: If the permission exists, operation proceeds; if missing, `AccessDeniedException` is thrown
5. **Error**: User sees 403 Forbidden error page

## Enforced Endpoints

### AdminUserController
- `POST /admin/users/new` → requires `create_user` permission
- `POST /admin/users/{id}/edit` → requires `edit_user` permission
- `POST /admin/users/{id}` (delete) → requires `delete_user` permission

### ProjectController
- `POST /project/new` → requires `create_project` permission
- `POST /project/{id}/edit` → requires `edit_project` permission
- `POST /project/{id}` (delete) → requires `delete_project` permission

### AssetController
- `POST /asset/new` → requires `create_asset` permission
- `POST /asset/{id}/edit` → requires `edit_asset` permission
- `POST /asset/{id}` (delete) → requires `delete_asset` permission

### ModuleController
- `POST /module/new` → requires `create_module` permission
- `POST /module/{id}/edit` → requires `edit_module` permission
- `POST /module/{id}` (delete) → requires `delete_module` permission

### VulnerabilityController
- `POST /vulnerability/new` → requires `create_vulnerability` permission
- `POST /vulnerability/{cveId}/edit` → requires `edit_vulnerability` permission
- `POST /vulnerability/{cveId}` (delete) → requires `delete_vulnerability` permission

### PatchCycleController
- `POST /patch/cycle/new` → requires `create_patch_cycle` permission
- `POST /patch/cycle/{cycleId}/edit` → requires `edit_patch_cycle` permission
- `POST /patch/cycle/{cycleId}` (delete) → requires `delete_patch_cycle` permission

### ReportController
- `POST /admin/reports/new` → requires `create_report` permission

## Testing Permission Enforcement

### Test Case: Remove "create_user" Permission

1. Go to `/admin/roles` to view all roles
2. Click "Edit" on the "Operator" role
3. Uncheck the `create_user` permission
4. Click "Save"
5. Log in as a user with the "Operator" role
6. Try to navigate to `/admin/users/new` or click "New User" button
7. **Expected Result**: You should see a 403 Forbidden error, preventing access to create users

### Test Case: Verify Admin Role Unchanged

1. Go to `/admin/roles`
2. Click "Edit" on the "Admin" role
3. Verify all permissions are still checked (Admin has all 36 permissions by default)
4. Click back without making changes
5. Log in as an Admin user
6. Verify you can still create/edit/delete any entity

## Database Schema

Permissions are stored in three tables:

- `permissions` — Lists all 36 available permissions (name, description, category)
- `role_permissions` — Maps roles to their permission sets
- `role_permission_mappings` — Join table linking role_permissions to permissions (ManyToMany)

## 36 Seed Permissions

The system initializes with 36 default permissions across 8 categories:

**Users Category** (3)
- `create_user`
- `edit_user`
- `delete_user`

**Projects Category** (3)
- `create_project`
- `edit_project`
- `delete_project`

**Modules Category** (3)
- `create_module`
- `edit_module`
- `delete_module`

**Assets Category** (3)
- `create_asset`
- `edit_asset`
- `delete_asset`

**Patch Cycles Category** (3)
- `create_patch_cycle`
- `edit_patch_cycle`
- `delete_patch_cycle`

**Vulnerabilities Category** (3)
- `create_vulnerability`
- `edit_vulnerability`
- `delete_vulnerability`

**Reports Category** (1)
- `create_report`

**System Category** (3)
- `export_data`
- `manage_permissions`
- `view_audit_logs`

## Architecture

### PermissionChecker Service
Located in `src/Service/PermissionChecker.php`, this service:
- Gets the currently authenticated user
- Looks up their role
- Queries the database for permissions associated with that role
- Throws `AccessDeniedException` if permission is missing

### Usage Pattern
```php
// In any controller action
public function new(Request $request, PermissionChecker $permissionChecker): Response {
    $permissionChecker->requirePermission('create_user');
    // ... rest of action logic
}
```

## Default Role Permissions

All 4 built-in roles start with the following permissions:

| Role | Permissions |
|------|-------------|
| Admin | All 36 permissions |
| ProjectManager | create_project, edit_project, create_module, edit_module, create_asset, edit_asset, create_patch_cycle, edit_patch_cycle |
| Operator | view_audit_logs, export_data |
| Auditor | view_audit_logs, export_data |

*Note: Initial role-permission mappings can be customized via `/admin/roles` UI*

## Troubleshooting

**Q: User sees 403 Forbidden but should have permission**
- Go to `/admin/roles` and verify the user's role has the required permission checked
- Clear application cache: `php bin/console cache:clear`
- Verify the permission name matches exactly (case-sensitive)

**Q: I removed all permissions from a role but the user can still perform actions**
- Double-check that the role-permission mapping was saved (check the database)
- Verify the user's role assignment is correct in the users table
- The "Admin" role always bypasses permission checks (has ROLE_ADMIN)

**Q: How do I add new permissions?**
- Add the permission manually via database INSERT, or
- Create a new Command class following the pattern in `SeedPermissionsCommand.php`
- Then use `/admin/roles` UI to assign it to roles
