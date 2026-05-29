<?php
/**
 * PermissionManager.php
 * Centralized permission and access control management
 */

class PermissionManager {
    private static $permissions_cache = [];
    private static $user_permissions_cache = [];
    private $pdo;
    private $user_id;

    public function __construct($pdo, $user_id = null) {
        $this->pdo = $pdo;
        $this->user_id = $user_id ?? ($_SESSION['user_id'] ?? null);
    }

    /**
     * Check if current user has a specific permission
     */
    public function hasPermission($permission_name) {
        if (!$this->user_id) {
            return false;
        }

        // Check cache first
        $cache_key = $this->user_id . '_' . $permission_name;
        if (isset(self::$user_permissions_cache[$cache_key])) {
            return self::$user_permissions_cache[$cache_key];
        }

        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) as count
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = ? AND p.name = ?
        ");
        $stmt->execute([$this->user_id, $permission_name]);
        $result = $stmt->fetch();
        $has_permission = $result['count'] > 0;

        self::$user_permissions_cache[$cache_key] = $has_permission;
        return $has_permission;
    }

    /**
     * Check if user has any of the listed permissions
     */
    public function hasAnyPermission($permissions) {
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all listed permissions
     */
    public function hasAllPermissions($permissions) {
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all permissions for current user
     */
    public function getUserPermissions() {
        if (!$this->user_id) {
            return [];
        }

        $stmt = $this->pdo->prepare("
            SELECT DISTINCT p.name, p.module, p.action
            FROM role_permissions rp
            JOIN permissions p ON rp.permission_id = p.id
            JOIN users u ON u.role_id = rp.role_id
            WHERE u.id = ?
        ");
        $stmt->execute([$this->user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all modules user has access to
     */
    public function getAccessibleModules() {
        $permissions = $this->getUserPermissions();
        $modules = [];

        foreach ($permissions as $perm) {
            if (!isset($modules[$perm['module']])) {
                $modules[$perm['module']] = [];
            }
            $modules[$perm['module']][] = $perm['action'];
        }

        return $modules;
    }

    /**
     * Enforce permission - redirect if user doesn't have access
     */
    public function requirePermission($permission_name) {
        if (!$this->hasPermission($permission_name)) {
            http_response_code(403);
            echo "Access Denied: You don't have permission to access this resource.";
            exit;
        }
    }

    /**
     * Enforce any permission - redirect if user doesn't have any
     */
    public function requireAnyPermission($permissions) {
        if (!$this->hasAnyPermission($permissions)) {
            http_response_code(403);
            echo "Access Denied: You don't have permission to access this resource.";
            exit;
        }
    }

    /**
     * Get user info with role
     */
    public function getCurrentUser() {
        if (!$this->user_id) {
            return null;
        }

        $stmt = $this->pdo->prepare("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$this->user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        $stmt = $this->pdo->query("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            ORDER BY u.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get user by ID
     */
    public function getUserById($user_id) {
        $stmt = $this->pdo->prepare("
            SELECT u.*, r.name as role_name
            FROM users u
            JOIN roles r ON u.role_id = r.id
            WHERE u.id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all roles
     */
    public function getAllRoles() {
        $stmt = $this->pdo->query("SELECT * FROM roles ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get role with its permissions
     */
    public function getRoleWithPermissions($role_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id = ?");
        $stmt->execute([$role_id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($role) {
            $stmt = $this->pdo->prepare("
                SELECT p.* FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                WHERE rp.role_id = ?
                ORDER BY p.module, p.action
            ");
            $stmt->execute([$role_id]);
            $role['permissions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $role;
    }

    /**
     * Create new user
     */
    public function createUser($username, $email, $full_name, $password, $role_id) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, email, full_name, password_hash, role_id, status)
            VALUES (?, ?, ?, ?, ?, 'active')
        ");

        try {
            return $stmt->execute([$username, $email, $full_name, $password_hash, $role_id]);
        } catch (Exception $e) {
            throw new Exception("Error creating user: " . $e->getMessage());
        }
    }

    /**
     * Update user
     */
    public function updateUser($user_id, $email, $full_name, $role_id, $status = null) {
        $sql = "UPDATE users SET email = ?, full_name = ?, role_id = ?";
        $params = [$email, $full_name, $role_id];

        if ($status !== null) {
            $sql .= ", status = ?";
            $params[] = $status;
        }

        $sql .= " WHERE id = ?";
        $params[] = $user_id;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * Update user password
     */
    public function updatePassword($user_id, $new_password) {
        $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$password_hash, $user_id]);
    }

    /**
     * Delete user
     */
    public function deleteUser($user_id) {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$user_id]);
    }

    /**
     * Create new role
     */
    public function createRole($name, $description = '') {
        $stmt = $this->pdo->prepare("
            INSERT INTO roles (name, description, status)
            VALUES (?, ?, 'active')
        ");

        try {
            $stmt->execute([$name, $description]);
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error creating role: " . $e->getMessage());
        }
    }

    /**
     * Update role
     */
    public function updateRole($role_id, $name, $description = '', $status = 'active') {
        $stmt = $this->pdo->prepare("
            UPDATE roles SET name = ?, description = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $description, $status, $role_id]);
    }

    /**
     * Delete role
     */
    public function deleteRole($role_id) {
        // Check if role is assigned to any users
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role_id = ?");
        $stmt->execute([$role_id]);
        $result = $stmt->fetch();

        if ($result['count'] > 0) {
            throw new Exception("Cannot delete role that is assigned to users.");
        }

        $stmt = $this->pdo->prepare("DELETE FROM roles WHERE id = ?");
        return $stmt->execute([$role_id]);
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissionsToRole($role_id, $permission_ids) {
        // Clear existing permissions
        $stmt = $this->pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
        $stmt->execute([$role_id]);

        // Add new permissions
        $stmt = $this->pdo->prepare("
            INSERT INTO role_permissions (role_id, permission_id)
            VALUES (?, ?)
        ");

        foreach ($permission_ids as $permission_id) {
            $stmt->execute([$role_id, $permission_id]);
        }

        return true;
    }

    /**
     * Get all available permissions
     */
    public function getAllPermissions() {
        if (empty(self::$permissions_cache)) {
            $stmt = $this->pdo->query("
                SELECT * FROM permissions
                ORDER BY module, action
            ");
            self::$permissions_cache = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return self::$permissions_cache;
    }

    /**
     * Get permissions by module
     */
    public function getPermissionsByModule($module) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM permissions
            WHERE module = ?
            ORDER BY action
        ");
        $stmt->execute([$module]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update last login
     */
    public function updateLastLogin($user_id) {
        $stmt = $this->pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$user_id]);
    }

    /**
     * Clear permission cache
     */
    public static function clearCache() {
        self::$permissions_cache = [];
        self::$user_permissions_cache = [];
    }
}
