<?php

/**
 * handles the the main page experiment listing and data export
 * @author artoo
 * @link https://github.com/artoo-git/---
 * @license https://opensource.org/licenses/GPL-3.0 GPLv3 License
 **/
class Main
{
    
    private $db_connection = null;
    
    private $exp_id = null;
    
    private $exp_name = "";
    
    private $exp_is_logged_in = false;
    
    public $errors = array();
    
    public $messages = array();


    public function __construct()
    {
        if (isset($_SESSION)){
            $this->drawSummary($_SESSION['exp_id']);
            $this->form($_SESSION['exp_id']);
        }else{echo "smt wrong";}

    }


    private function databaseConnection()
    {
        // if connection already exists
        if ($this->db_connection != null) {
            return true;
        } else {
            try {
                $this->db_connection = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
                return true;
            } catch (PDOException $e) {
                $this->errors[] = MESSAGE_DATABASE_ERROR . $e->getMessage();
            }
        }
        // default return
        return false;
    }


    
    private function getexpInfo($exp_id)
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // database query, getting all the info of the selected exp
            $query_exp = $this->db_connection->prepare('SELECT * FROM experiments WHERE id = :exp_id');
            $query_exp->bindValue(':exp_id', $exp_id, PDO::PARAM_STR);
            $query_exp->execute();
            #$query_exp->debugDumpParams();
            #print_r($query_exp->fetchObject());
            // get result row (as an object)
            $results = $query_exp->fetchObject();
            return $results;
        } else {
            return false;
        }
    }

    private function getexpData($exp_id)
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // database query, getting all the info of the selected exp
            $query_data = $this->db_connection->prepare('SELECT * FROM data WHERE exp_id = :exp_id');
            $query_data->bindValue(':exp_id', $exp_id, PDO::PARAM_STR);
            $query_data->execute();
            #$query_exp->debugDumpParams();
            #print_r($query_exp->fetchObject());
            // get result rows (as an object)
            return $query_data->fetchAll(PDO::FETCH_OBJ);
            //return $query_data->fetchall(PDO::FETCH_GROUP);
            
        } else {
            return false;
        }
    }

    private function getphoda($measureID)
    {
        // if database connection opened
        if ($this->databaseConnection()) {
            // database query, getting all the info of the selected exp
            $query_data = $this->db_connection->prepare('SELECT * FROM data WHERE id = :id');
            $query_data->bindValue(':id', $measureID, PDO::PARAM_STR);
            $query_data->execute();
            #$query_exp->debugDumpParams();
            #print_r($query_exp->fetchObject());
            // get result rows (as an object)
            return $query_data->fetchAll(PDO::FETCH_OBJ);
            //return $query_data->fetchall(PDO::FETCH_GROUP);
            
        } else {
            return false;
        }
    }

    private function drawSummary($exp_id)
    {
        if ($this->getexpInfo($exp_id) != NULL){
            $summary = $this->getexpInfo($exp_id);?>
            <table class="summary">
            <tr><th colspan="2">Summary</th>
            <tr><td>Experiment name: </td>
                <td><?php echo $summary->exp_name;?></td>
            </tr>
            <tr><td>Created on: </td>
                <td><?php echo $summary->exp_registration_datetime;?></td>
            </tr>
            <tr><td>P.I. :</td>
                <td><?php echo $summary->exp_princ_inv;?></td>
            </tr>
            <tr><td>Experimental conditions :</td>
                <td><?php echo $summary->exp_n_cond;?></td></tr>
            </table>            
            <?php
        }  
    }

     private function form($exp_id)
    {
        if ($this->getexpData($exp_id) != NULL){
            // create empty arrays for the ajax autocomplete
            $subjects = array();
            $therapists = array();
            $conditions = array();
            $data = $this->getexpData($exp_id);
            foreach ($data as $measure) {
                // get all patientIDs for this experiments that have been tested
                if (!in_array($measure->patientID, $subjects)){
                    // get all therapists that have worked in this experiment
                    if(isset($measure->patientID)){
                    $subjects[] = $measure->patientID;
                    }
                }
                if (!in_array($measure->thrpstID, $therapists)){
                    // get all therapists that have worked in this experiment
                    if(isset($measure->thrpstID)){
                    $therapists[] = $measure->thrpstID;
                    }
                }
                //
                if (!in_array($measure->exp_cond, $conditions)){
                    // get all condition descriptions that were used in this exp
                    if(isset($measure->exp_cond)){
                        $conditions[] = $measure->exp_cond;
                    }
                }
            }
        }
        //var_dump($conditions);
        ?>
            <p>
            <form autocomplete="off" method="POST" action="exp.php">
            <table class="experiment">
            
            <tr><td>Participant ID</td>
                <td><div class="autocomplete" style="max-width:300px;"><input id="patientID" type="text" name="patientID" placeholder="Pseudonym" required></td>
                </div></tr>
            <tr><td>Therapist ID</td>
                <td><div class="autocomplete" style="max-width:300px;"><input id="thrpstID" type="text" name="thrpstID" placeholder="Therapist" required> </td>
                </div></tr>
            <tr><td>Session</td>
                <td><input id="session" type="text" name="session" placeholder="Session number" required></td>
                </tr>
            <tr><td>Condition code</td>
                <td><div class="autocomplete" style="max-width:300px;"><input id="condition" type="text" name="condition" placeholder="Condition" required></td>
                </div></tr>
           
            <tr><td></td><td>
                <input type="hidden" name="NewTest" value="1">
                <button class="button" type="submit" value="submit">Begin new measure</</button></td></tr>
            
            </table>
            </form>
            </p>
            <script src="js/ajax.js"></script>
            <script>
            var subjects = <?php echo json_encode($subjects); ?>;
            var therapists = <?php echo json_encode($therapists); ?>;
            var conditions = <?php echo json_encode($conditions); ?>;
            autocomplete(document.getElementById("patientID"), subjects);
            autocomplete(document.getElementById("thrpstID"), therapists);
            autocomplete(document.getElementById("condition"), conditions);
            </script>
            <?php
    }

    
}