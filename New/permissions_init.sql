-- Digifyce Permissions and Roles Initialization
-- This file sets up default roles and permissions for the admin panel

-- ==================== PERMISSIONS ====================
-- Blogs Module
INSERT INTO permissions (name, description, module, action) VALUES
('blog.view', 'View all blogs', 'blogs', 'view'),
('blog.create', 'Create new blogs', 'blogs', 'create'),
('blog.edit', 'Edit blogs', 'blogs', 'edit'),
('blog.delete', 'Delete blogs', 'blogs', 'delete'),
('blog.publish', 'Publish/Unpublish blogs', 'blogs', 'publish');

-- Categories Module
INSERT INTO permissions (name, description, module, action) VALUES
('category.view', 'View all categories', 'categories', 'view'),
('category.create', 'Create new categories', 'categories', 'create'),
('category.edit', 'Edit categories', 'categories', 'edit'),
('category.delete', 'Delete categories', 'categories', 'delete');

-- Tags Module
INSERT INTO permissions (name, description, module, action) VALUES
('tag.view', 'View all tags', 'tags', 'view'),
('tag.create', 'Create new tags', 'tags', 'create'),
('tag.edit', 'Edit tags', 'tags', 'edit'),
('tag.delete', 'Delete tags', 'tags', 'delete');

-- Authors Module
INSERT INTO permissions (name, description, module, action) VALUES
('author.view', 'View all authors', 'authors', 'view'),
('author.create', 'Create new authors', 'authors', 'create'),
('author.edit', 'Edit authors', 'authors', 'edit'),
('author.delete', 'Delete authors', 'authors', 'delete');

-- Lead Forms Module
INSERT INTO permissions (name, description, module, action) VALUES
('lead.view', 'View lead submissions', 'leads', 'view'),
('lead.export', 'Export lead data', 'leads', 'export'),
('lead.delete', 'Delete lead submissions', 'leads', 'delete');

-- Job Module
INSERT INTO permissions (name, description, module, action) VALUES
('job.view', 'View job openings and applications', 'jobs', 'view'),
('job.create', 'Create new job openings', 'jobs', 'create'),
('job.edit', 'Edit job openings', 'jobs', 'edit'),
('job.delete', 'Delete job openings', 'jobs', 'delete'),
('job_application.view', 'View job applications', 'jobs', 'view_applications'),
('job_application.manage', 'Manage job applications', 'jobs', 'manage_applications');

-- Pages Module
INSERT INTO permissions (name, description, module, action) VALUES
('page.view', 'View all pages', 'pages', 'view'),
('page.create', 'Create new pages', 'pages', 'create'),
('page.edit', 'Edit pages', 'pages', 'edit'),
('page.delete', 'Delete pages', 'pages', 'delete');

-- Site Settings Module
INSERT INTO permissions (name, description, module, action) VALUES
('settings.view', 'View site settings', 'settings', 'view'),
('settings.edit', 'Edit site settings', 'settings', 'edit'),
('hero.edit', 'Edit hero section', 'settings', 'edit_hero'),
('metrics.edit', 'Edit metrics', 'settings', 'edit_metrics'),
('brands.edit', 'Edit trusted brands', 'settings', 'edit_brands'),
('navigation.edit', 'Edit navigation', 'settings', 'edit_navigation');

-- Users & Roles Module
INSERT INTO permissions (name, description, module, action) VALUES
('user.view', 'View all users', 'users', 'view'),
('user.create', 'Create new users', 'users', 'create'),
('user.edit', 'Edit users', 'users', 'edit'),
('user.delete', 'Delete users', 'users', 'delete'),
('role.view', 'View all roles', 'roles', 'view'),
('role.create', 'Create new roles', 'roles', 'create'),
('role.edit', 'Edit roles and permissions', 'roles', 'edit'),
('role.delete', 'Delete roles', 'roles', 'delete'),
('permission.manage', 'Manage permissions', 'roles', 'manage_permissions');

-- ==================== DEFAULT ROLES ====================

-- Super Admin Role - Full Access
INSERT INTO roles (name, description, status) VALUES 
('Super Admin', 'Complete system access with all permissions', 'active');
SET @super_admin_role_id = LAST_INSERT_ID();

-- Admin Role - Full Admin Access
INSERT INTO roles (name, description, status) VALUES 
('Admin', 'Administrator with full access to content management', 'active');
SET @admin_role_id = LAST_INSERT_ID();

-- Editor Role - Can manage content but not settings/users
INSERT INTO roles (name, description, status) VALUES 
('Editor', 'Can create, edit, and delete content', 'active');
SET @editor_role_id = LAST_INSERT_ID();

-- Viewer Role - Read-only access
INSERT INTO roles (name, description, status) VALUES 
('Viewer', 'Can only view content, no editing', 'active');
SET @viewer_role_id = LAST_INSERT_ID();

-- ==================== ROLE PERMISSIONS ====================

-- Super Admin: All permissions
INSERT INTO role_permissions (role_id, permission_id)
SELECT @super_admin_role_id, id FROM permissions;

-- Admin: Everything except user management
INSERT INTO role_permissions (role_id, permission_id)
SELECT @admin_role_id, id FROM permissions 
WHERE name NOT IN ('user.delete', 'role.delete', 'permission.manage');

-- Editor: Only content management
INSERT INTO role_permissions (role_id, permission_id)
SELECT @editor_role_id, id FROM permissions 
WHERE module IN ('blogs', 'categories', 'tags', 'authors', 'pages') 
AND action IN ('view', 'create', 'edit', 'delete', 'publish');

-- Viewer: View-only access
INSERT INTO role_permissions (role_id, permission_id)
SELECT @viewer_role_id, id FROM permissions 
WHERE action = 'view' OR action LIKE '%view%';

-- ==================== DEFAULT SUPER ADMIN USER ====================
-- Username: admin
-- Password: admin123 (CHANGE THIS IMMEDIATELY!)
-- password_hash is bcrypt hash of "admin123"

INSERT INTO users (username, password_hash, email, full_name, role_id, status, last_login) 
VALUES ('admin', '$2y$10$u3mPuCL8vWpJKuKaDdLI4e6rEsF8vwDvOvDNUPtvNj7fXzFGLPEKS', 'admin@digifyce.com', 'System Administrator', @super_admin_role_id, 'active', NOW());

-- ==================== IMPORTANT SECURITY NOTES ====================
-- 1. Change the default admin password immediately after first login
-- 2. Create additional admin users with unique passwords
-- 3. Implement 2FA for admin accounts
-- 4. Regularly audit user access and permissions
-- 5. Archive inactive user accounts rather than deleting them
