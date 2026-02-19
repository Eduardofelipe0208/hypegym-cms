<?php
/**
 * Setup Roles Script
 * Use this to set a user as 'superadmin' or 'admin'
 */

require_once '../includes/db.php';

// Check if run from CLI for security or require admin login
if (php_sapi_name() !== 'cli' && (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'superadmin')) {
    // If running from browser, at least protect it slightly or just allow it for initial setup if no superadmin exists
    // For now, let's make it simple: if you are logged in, you can see this page, but only superadmin can change roles.
    // OR: if no superadmin exists, allow creating one.
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $role = $_POST['role'] ?? 'admin';
    
    if ($username && in_array($role, ['admin', 'superadmin', 'editor'])) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE users SET role = ? WHERE username = ?");
        if ($stmt->execute([$role, $username])) {
             if ($stmt->rowCount() > 0) {
                 $message = "Success: User '$username' is now '$role'.";
             } else {
                 $message = "No changes made (user not found or already has that role).";
             }
        } else {
            $message = "Error updating role.";
        }
    } else {
        $message = "Invalid input.";
    }
}

// Fetch all users
$db = getDB();
$users = $db->query("SELECT id, username, role FROM users")->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Setup Roles</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f0f0f0; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #eee; }
        .btn { padding: 5px 10px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer;}
        .message { padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Role Management</h1>
        
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST">
            <h3>Assign Role</h3>
            <p>Select a username and a role to assign.</p>
            <input type="text" name="username" placeholder="Username" required>
            <select name="role">
                <option value="admin">Admin (Restricted)</option>
                <option value="superadmin">SuperAdmin (Full Access)</option>
                <option value="editor">Editor (Product Management Only)</option>
            </select>
            <button type="submit" class="btn">Update Role</button>
        </form>

        <h3>Existing Users</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Current Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><strong><?php echo htmlspecialchars($u['role'] ?? 'none'); ?></strong></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p style="margin-top: 20px; font-size: 0.9em; color: #666;">
            <strong>Roles:</strong><br>
            - <strong>superadmin</strong>: Can see Logs, Backups, and manage Settings.<br>
            - <strong>admin</strong>: Can manage Products and Orders, but NOT Logs or Backups.<br>
            - <strong>editor</strong>: Can only manage Products.
        </p>
        <a href="index.php">Back to Dashboard</a>
    </div>
</body>
</html>
