<?php
session_start();
require_once 'config/config.php';

if ($_POST['recover_password']) {
    $exp_name = trim($_POST['exp_name']);
    $recovery_code = strtoupper(trim($_POST['recovery_code']));
    $new_password = $_POST['new_password'];
    
    try {
        $db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
        
        // Simple verification
        $query = $db_connection->prepare("
            SELECT id, exp_recovery_code, exp_recovery_code_used 
            FROM experiments 
            WHERE exp_name = :exp_name AND exp_recovery_code = :code AND exp_recovery_code_used = FALSE
        ");
        $query->execute([
            ':exp_name' => $exp_name,
            ':code' => $recovery_code
        ]);
        $result = $query->fetch();
        
        if ($result) {
            // Generate new password hash
            $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
            
            // Update password and mark code as used
            $update_query = $db_connection->prepare("
                UPDATE experiments 
                SET exp_password_hash = :hash,
                    exp_recovery_code_used = TRUE,
                    exp_rememberme_token = NULL
                WHERE id = :id
            ");
            
            $update_query->execute([
                ':hash' => $new_hash,
                ':id' => $result['id']
            ]);
            
            echo "<div style='color: green; padding: 10px;'>Password reset successfully!</div>";
            echo "<a href='index.php'>Click here to log in with your new password</a>";
            exit;
        } else {
            $error = "Invalid experiment name or recovery code, or code already used.";
        }
    } catch (Exception $e) {
        $error = "An error occurred. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Recovery - OpenPhoda-SeV</title>
</head>
<body>
    <h2>Password Recovery</h2>
    <p> As of July 2025, new registrants are given  a recovery code that allows for self-service password recovery.</p>
    <p><b>How does it work:</b></p>
    <p>Enter your experiment name and the recovery code that was provided when you first registered your experiment.</p>
    <p>If you have saved your recovery code, you can reset your password immediately using the form below.</p>
    
    <?php if (isset($error)): ?>
        <div style="color: red; padding: 10px;"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div style="margin: 10px 0;">
            <label>Experiment Name:</label><br>
            <input type="text" name="exp_name" required autocomplete="off" style="width: 200px; padding: 5px;">
        </div>
        
        <div style="margin: 10px 0;">
            <label>Recovery Code:</label><br>
            <input type="text" name="recovery_code" required autocomplete="off" style="width: 200px; padding: 5px; text-transform: uppercase;">
        </div>
        
        <div style="margin: 10px 0;">
            <label>New Password:</label><br>
            <input type="password" name="new_password" autocomplete="off" required style="width: 200px; padding: 5px;">
        </div>
        
        <div style="margin: 10px 0;">
            <input type="submit" name="recover_password" value="Reset Password" style="padding: 8px 16px;">
        </div>
    </form>

    <p><strong>If you don't have a recovery code or have lost it:</strong></p>
    <p>Contact the system administrator for assistance:<a href="mailto:d.vitali@ucl.ac.uk" class="email-link">d.vitali@ucl.ac.uk</a></p>
    
    <p><a href="index.php">Back to Login</a></p>
</body>
</html>
