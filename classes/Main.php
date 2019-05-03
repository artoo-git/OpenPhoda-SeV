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
            $summaryA = $this->getexpInfo($exp_id);?>
            <table class="summary">
            <tr><th colspan="2">Summary</th>
            <tr><td>Experiment name: </td>
                <td><?php echo $summaryA->exp_name;?></td>
            </tr>
            <tr><td>Created on: </td>
                <td><?php echo $summaryA->exp_registration_datetime;?></td>
            </tr>
            <tr><td>P.I. :</td>
                <td><?php echo $summaryA->exp_princ_inv;?></td>
            </tr>
            <tr><td>Experimental conditions :</td>
                <td><?php echo $summaryA->exp_n_cond;?></td></tr>
            </table>            
            <?php
        }
        if ($this->getexpData($exp_id) != NULL){
            
            $summaryB = $this->getexpData($exp_id);
            $phodaTot = array();
            foreach($summaryB as $data){
                $mean = ($data->PHODA_1 + $data->PHODA_2 + $data->PHODA_3 + $data->PHODA_4 + $data->PHODA_5 +
                            $data->PHODA_6 + $data->PHODA_7 + $data->PHODA_8 + $data->PHODA_9 + $data->PHODA_10 +
                            $data->PHODA_11 + $data->PHODA_12 + $data->PHODA_13 + $data->PHODA_14 + $data->PHODA_15 +
                            $data->PHODA_16 + $data->PHODA_17 + $data->PHODA_18 + $data->PHODA_19 + $data->PHODA_20 +
                            $data->PHODA_21 + $data->PHODA_22 + $data->PHODA_23 + $data->PHODA_24 + $data->PHODA_25 +
                            $data->PHODA_26 + $data->PHODA_27 + $data->PHODA_28 + $data->PHODA_29 + $data->PHODA_30 +
                            $data->PHODA_31 + $data->PHODA_32 + $data->PHODA_33 + $data->PHODA_34 + $data->PHODA_35 +
                            $data->PHODA_36 + $data->PHODA_37 + $data->PHODA_38 + $data->PHODA_39 + $data->PHODA_40)/40;
            array_push($phodaTot, round($mean));
            }
            

            ?>

            <table class="summary">
            
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