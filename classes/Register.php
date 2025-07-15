<?php

/**
 * handles the Experiment Registration
 * @author artoo
 * @link https://github.com/artoo-git
 * @license https://opensource.org/licenses/GPL-3.0 GPLv3 licence
 
 * Parts are very loosely adapted from 
 * @author Panique
 * @link http://www.php-login.net
 * @link https://github.com/panique/php-login-advanced/
 * @license http://opensource.org/licenses/MIT MIT License
 */
class Register
{

    /**
     * @var object $db_connection The database connection
     */
    private $db_connection            = null;
    /**
     * @var bool success state of registration
     */
    public  $registration_successful  = false;
    /**
     * @var bool success state of verification
     */
    public  $verification_successful  = false;
    /**
     * @var array collection of error messages
     */
    public  $errors                   = array();
    /**
     * @var array collection of success / neutral messages
     */
    public  $messages                 = array();

    public function __construct()
    {
        session_start();

        // if we have such a POST request, call the registerNewUser() method
        
        if (isset($_POST["register"])) {

            $this->registerNewExp($_POST['exp_name'], $_POST['password'],$_POST['pi']);
            
        }
    }
    
    /**
     * Checks if database connection is opened and open it if not
     */
    private function databaseConnection()
    {
        // connection already opened
        if ($this->db_connection != null) {
            return true;

        } else {
            // create a database connection, using the constants from config/config.php
            try {
                // Generate a database connection, using the PDO connector
               
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                #$this->db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
                return true;

            // If an error is catched, database connection failed
            } catch (PDOException $e) {
                $this->errors[] = MESSAGE_DATABASE_ERROR;
                return false;
            }
        }
    }


    private function registerNewExp($exp_name, $exp_password, $exp_princ_inv)
    {
        // we just remove extra space on expname 
        $exp_name = trim($exp_name);
        $exp_princ_inv = trim($exp_princ_inv);

        if ($this->databaseConnection()) {

            // check if expname or email already exists
            $query_check_exp_name = $this->db_connection->prepare('SELECT exp_name FROM experiments WHERE exp_name=:exp_name');
            $query_check_exp_name->bindValue(':exp_name', $exp_name, PDO::PARAM_STR);
            $query_check_exp_name->execute();

            $result = $query_check_exp_name->fetchAll();
            // if expname is found in the database
            // TODO: this is really awful!
            if (count($result) > 0) {
                for ($i = 0; $i < count($result); $i++) {
                    if ($result[$i]['exp_name'] == $exp_name){?>
                            <script type="text/javascript"> alert("This experiment name is taken");
                            window.location.href = "register.php";
                            </script>
                            <?php
                            //header("location:". $_SERVER['REQUEST_URI']);
                    }
                } 
            } else {
                // check if we have a constant HASH_COST_FACTOR defined (in config/hashing.php),
                // if so: put the value into $hash_cost_factor, if not, make $hash_cost_factor = null
                $hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);

                $exp_password_hash = password_hash($exp_password, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
                
                // RECOVERY CODE GENERATION 
                $recovery_code = strtoupper(substr(bin2hex(random_bytes(8)), 0, 16));
                
                // write new exp data into database - MODIFIED INSERT
                $q_ins_new_exp = $this->db_connection->prepare("INSERT INTO experiments (
                        exp_name, 
                        exp_password_hash, 
                        exp_princ_inv,
                        exp_recovery_code,
                        exp_registration_datetime) 
                    VALUES(
                        :exp_name, 
                        :exp_password_hash, 
                        :exp_princ_inv,
                        :recovery_code,
                        NOW())");

                $q_ins_new_exp->bindValue(':exp_name', $exp_name, PDO::PARAM_STR);
                $q_ins_new_exp->bindValue(':exp_password_hash', $exp_password_hash, PDO::PARAM_STR);
                $q_ins_new_exp->bindValue(':exp_princ_inv', $exp_princ_inv, PDO::PARAM_STR);
                $q_ins_new_exp->bindValue(':recovery_code', $recovery_code, PDO::PARAM_STR); // ADD THIS LINE
                $q_ins_new_exp->execute();
                
                // DISPLAY RECOVERY CODE TO USER 
                ?>
                <div style="background: #fffacd; padding: 20px; border: 3px solid #ffd700; margin: 20px; text-align: center;">
                    <h2 style="color: #d32f2f;">IMPORTANT: Save Your Recovery Code</h2>
                    <div style="font-size: 28px; font-weight: bold; color: #000; background: white; padding: 10px; border: 2px solid #333; letter-spacing: 2px;">
                        <?php echo htmlspecialchars($recovery_code); ?>
                    </div>
                    <p style="font-size: 16px; margin-top: 15px;">
                        <strong>Write this code down and store it safely!</strong><br>
                        You will need this code if you ever forget your password.
                    </p>
                    <p style="color: #d32f2f; font-weight: bold;">
                        This code will NOT be shown again!
                    </p>
                </div>
                <script>
                    // Auto-redirect after 15 seconds to ensure user sees the code
                    setTimeout(function(){ 
                        window.location.href = "index.php"; 
                    }, 15000);
                </script>
                <p style="text-align: center;">You will be redirected to the main page in 15 seconds...</p>
                <?php
                
                // Don't redirect immediately - let user see the recovery code
            }
        }
    }
}
