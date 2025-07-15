<?php
define('TESTING_MODE', False);

// password_change.php
session_start();
require_once 'config/config.php';

if (!isset($_SESSION['exp_name'])) {
    header('Location: login.php');
    exit;
}

if ($_POST['change_password']) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $exp_name = $_SESSION['exp_name'];
    
    if ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } else if (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters";
    } else {
        $db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
        
        // Get current password hash
        $query = $db_connection->prepare('SELECT exp_password_hash FROM experiments WHERE exp_name = :exp_name');
        $query->bindValue(':exp_name', $exp_name, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetch();
        
        if (password_verify($current_password, $result['exp_password_hash'])) {
            $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
    
            if (TESTING_MODE) {
                // Print the exact query that would be executed
                echo "<h3>TEST MODE - Query Preview:</h3>";
                echo "<pre>";
                echo "UPDATE experiments \n";
                echo "SET exp_password_hash = '" . $new_hash . "',\n";
                echo "    exp_rememberme_token = NULL\n";
                echo "WHERE exp_name = '" . $exp_name . "';\n";
                echo "</pre>";
                
                // Also show the parameter bindings
                echo "<h3>Parameter Bindings:</h3>";
                echo "<pre>";
                echo "exp_name: " . htmlspecialchars($exp_name) . "\n";
                echo "new_hash: " . htmlspecialchars($new_hash) . "\n";
                echo "hash_algorithm: " . PASSWORD_DEFAULT . "\n";
                echo "cost_factor: " . ($hash_cost_factor ?? 'default') . "\n       ";
                echo "</pre>";
                $success = "TEST MODE: Password would be changed successfully (not actually changed)";
                error_log("TEST: Password change simulated for experiment: " . $exp_name);
            } else {
            // Actual database update code here
                $update_query = $db_connection->prepare('
                UPDATE experiments 
                SET exp_password_hash = :hash, 
                    exp_rememberme_token = NULL 
                WHERE exp_name = :exp_name
                ');
                $update_query->bindValue(':hash', $new_hash, PDO::PARAM_STR);
                $update_query->bindValue(':exp_name', $exp_name, PDO::PARAM_STR);
                $update_query->execute();
        
                $success = "Password changed successfully. Please log in again.";
                session_destroy();
                header('Location: index.php');
                exit;
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Password - OpenPhoda-SeV</title>
    <style>
        .error { color: red; }
        .success { color: green; }
        .form-group { margin: 10px 0; }
        input[type="password"] { width: 200px; padding: 5px; }
        input[type="submit"] { padding: 8px 16px; background: #007cba; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Change Password</h2>
    
    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="form-group">
            <label>Current Password:</label><br>
            <input type="password" name="current_password" required>
        </div>
        
        <div class="form-group">
            <label>New Password:</label><br>
            <input type="password" name="new_password" required>
        </div>
        
        <div class="form-group">
            <label>Confirm New Password:</label><br>
            <input type="password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <input type="submit" name="change_password" value="Change Password">
        </div>
    </form>
    
    <p><a href="index.php">Back to Main</a></p>
</body>
</html>
