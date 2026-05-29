# User Management & Permissions System - Complete Documentation

## Overview
A comprehensive role-based access control (RBAC) system has been implemented for the Digifyce admin panel. This system allows admins to create users, assign roles, and control what each user can see and do throughout the admin panel.

## Features

### 1. **Role-Based Access Control**
- **Super Admin**: Complete system access with all permissions
- **Admin**: Full admin access to all content management features (except user/role deletion)
- **Editor**: Can create, edit, and delete blog content and pages
- **Viewer**: Read-only access to all modules
- **Custom Roles**: Create additional custom roles with specific permission combinations

### 2. **Permission System**
The system has 45 granular permissions organized by module:

#### Content Management
- `blog.view`, `blog.create`, `blog.edit`, `blog.delete`, `blog.publish`
- `page.view`, `page.create`, `page.edit`, `page.delete`
- `category.view`, `category.create`, `category.edit`, `category.delete`
- `tag.view`, `tag.create`, `tag.edit`, `tag.delete`
- `author.view`, `author.create`, `author.edit`, `author.delete`

#### Lead Management
- `lead.view`, `lead.export`, `lead.delete`

#### Job Management
- `job.view`, `job.create`, `job.edit`, `job.delete`
- `job_application.view`, `job_application.manage`

#### Site Settings
- `settings.view`, `settings.edit`
- `hero.edit`, `metrics.edit`, `brands.edit`, `navigation.edit`

#### User & Role Management
- `user.view`, `user.create`, `user.edit`, `user.delete`
- `role.view`, `role.create`, `role.edit`, `role.delete`
- `permission.manage`

### 3. **User Management**
- **Create Users**: Add new admin users with specific roles
- **Edit Users**: Modify user details, email, role, and status
- **Change Passwords**: Update user passwords securely
- **Deactivate Users**: Set users to inactive status
- **Delete Users**: Remove users from the system
- **Track Login**: Automatically track last login times

### 4. **Dynamic Navigation**
The admin sidebar automatically shows/hides menu items based on user permissions:
- Users only see modules they have access to
- Buttons for create, edit, delete actions only appear if user has permissions
- Current user info displayed in header with role badge

### 5. **Permission Manager Utility**
A powerful `PermissionManager` class handles all permission checks:
```php
$permissionManager = new PermissionManager($pdo);

// Check single permission
if ($permissionManager->hasPermission('blog.view')) { ... }

// Check multiple permissions (any)
if ($permissionManager->hasAnyPermission(['blog.view', 'page.view'])) { ... }

// Enforce permission (exit if no access)
$permissionManager->requirePermission('blog.create');

// Get user permissions
$permissions = $permissionManager->getUserPermissions();

// Get accessible modules
$modules = $permissionManager->getAccessibleModules();
```

## Database Structure

### Tables

#### `permissions`
```sql
id (int, PK)
name (varchar 128, UNIQUE) - e.g., "blog.view"
description (text)
module (varchar 64) - e.g., "blogs"
action (varchar 64) - e.g., "view"
created_at (datetime)
```

#### `roles`
```sql
id (int, PK)
name (varchar 64, UNIQUE) - e.g., "Editor"
description (text)
status (enum: 'active', 'inactive')
created_at (datetime)
updated_at (datetime)
```

#### `role_permissions` (Junction Table)
```sql
role_id (int, FK) + permission_id (int, FK) = PRIMARY KEY
```

#### `users`
```sql
id (int, PK)
username (varchar 64, UNIQUE)
password_hash (varchar 255)
email (varchar 128, UNIQUE)
full_name (varchar 255)
role_id (int, FK) -> roles
status (enum: 'active', 'inactive')
last_login (datetime)
created_at (datetime)
updated_at (datetime)
```

## Default Credentials

**Super Admin Account (Change Immediately!)**
- Username: `admin`
- Password: `admin123`
- Email: `admin@digifyce.com`

⚠️ **IMPORTANT**: Change the admin password immediately after first login!

## How to Use

### 1. Manage Users
Navigate to **User Management** in the admin panel:
- View all users with their roles and status
- Create new users
- Edit existing users
- Delete users (cannot delete your own account)

### 2. Manage Roles & Permissions
Navigate to **Roles & Permissions**:
- View all roles and their assigned permissions
- Create custom roles
- Edit role details and assigned permissions
- Delete unused roles (if no users assigned)

### 3. Assign Permissions to Roles
When creating/editing a role:
1. Enter role name and description
2. Click to organize permissions by module
3. Check/uncheck permissions for the role
4. Save - changes apply immediately to all users with that role

### 4. Best Practices
- Create role **first**, then assign permissions
- Use meaningful role names (e.g., "Content Editor", "Lead Manager")
- Regularly audit user access and remove inactive accounts
- Document custom roles and their purposes
- Don't modify built-in roles without good reason

## Implementation Details

### Adding Permission Checks to Pages
To add permission enforcement to any admin page:

```php
<?php
session_start();
require_once __DIR__ . '/admin_bootstrap.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../utilities/PermissionManager.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . admin_login_url());
    exit;
}

$pdo = Database::getInstance();
$permissionManager = new PermissionManager($pdo);

// Enforce permission
$permissionManager->requirePermission('blog.view');

// Rest of your code...
?>
```

### Conditional UI Elements
Show buttons only if user has permission:

```php
<?php if ($permissionManager->hasPermission('blog.create')): ?>
    <a href="blog_edit.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> New Blog
    </a>
<?php endif; ?>
```

### Permission-Based Navigation
The admin header automatically handles this, but you can also check:

```php
$modules = $permissionManager->getAccessibleModules();
// Returns: ['blogs' => ['view', 'create', 'edit'], 'pages' => ['view', 'edit'], ...]
```

## Files Created/Modified

### New Files
- `app/utilities/PermissionManager.php` - Core permission management class
- `app/admin/users.php` - User management interface
- `app/admin/user_edit.php` - Create/edit users
- `app/admin/user_save.php` - Process user form
- `app/admin/user_delete.php` - Delete users
- `app/admin/roles_permissions.php` - Roles & permissions interface
- `app/admin/role_edit.php` - Create/edit roles
- `app/admin/role_save.php` - Process role form
- `app/admin/role_delete.php` - Delete roles

### Modified Files
- `schema.sql` - Added permissions, roles, role_permissions tables
- `app/views/admin_header.php` - Dynamic menu based on permissions + user info
- `app/admin/login.php` - Track last login time
- `app/admin/blogs.php` - Added permission checks (example)

### Configuration Files
- `permissions_init.sql` - Initialize all permissions and default roles
- `create_permissions_tables.sql` - Create permission-related tables

## Security Features

1. **Password Hashing**: All passwords use bcrypt hashing
2. **Session Management**: Sessions required for all admin pages
3. **Permission Enforcement**: All critical actions check permissions
4. **Access Control**: Sidebar items hidden for unauthorized users
5. **Audit Trail**: Last login tracked for security monitoring
6. **Role-Based Restrictions**: Users can only perform assigned actions

## Future Enhancements

1. Add 2FA (Two-Factor Authentication) for admin accounts
2. Implement activity logging for all admin actions
3. Add permission groups for easier management
4. Implement time-based role assignments
5. Add bulk user imports
6. Email notifications for permission changes
7. API token-based authentication for integrations
8. Advanced audit logging and reporting

## Troubleshooting

### User can't access a module
1. Check user is active (`status = 'active'`)
2. Verify user's role has required permission
3. Check role_permissions table for role-permission mapping
4. Clear browser cache and try again

### Permission check showing "Access Denied"
1. Verify the permission name is correct (e.g., 'blog.view')
2. Check the permission exists in `permissions` table
3. Verify role has permission assigned
4. Check SQL query syntax in PermissionManager

### Default admin login not working
1. Check `users` table for admin user (username='admin')
2. Verify password hash is correct
3. Ensure user status is 'active'
4. Check if roles table has role with id matching user's role_id

## Support
For issues or questions about the permission system, check the `PermissionManager` class documentation or review the SQL initialization scripts.
