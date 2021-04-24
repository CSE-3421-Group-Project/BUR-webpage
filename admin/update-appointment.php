<!DOCTYPE html>
<html>
  <head>
    <title>BUR Admin Page</title>
    <meta charset="UTF-8" />
    <link href="../main_styles.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <nav>
      <h1>BUR Admin Dashboard</h1>
    </nav>
    <div class="main-content">
      <a href="../admin.php">Go back</a>
      <h2 class="title">Update an appointment</h2>
      <hr class="form-divider" />
      <?php
      function connectToDatabase() {
        $conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
        if($conn->connect_error) die($conn->connect_error);
        if($conn === false) die("Error: Could not connect".mysqli_connect_error());
        return $conn;
      }

      function getAppointments($db) {
        try {
          $sql = <<<EOD
      SELECT p.Ssn as ssn, p.Name as name, d.Tracking_no as dose_no 
      FROM patient as p, appointment as a, dose as d
      WHERE p.Ssn=a.P_ssn
      AND a.Tracking_no=d.Tracking_no
EOD;
            $result = $db->query($sql);
            if($result->num_rows > 0) {
              return $result;
            }
        } catch (\Throwable $e) {
          throw $e;
        }
        return null;
      }
      echo "<form method=\"post\" action=\"", $_SERVER['PHP_SELF'], "\" class=\"form-wrapper\">";
      echo  "<select name=\"patient_select\" id=\"patient_select\">";
      echo "<option value=\"\" selected>None</option>";
      $db = connectToDatabase();
      $patients = getAppointments($db);
      if($patients){
        while ($p = $patients->fetch_array()){
          echo "<option value=\"".$p['dose_no']."\">";
          echo $p['name']." ".$p['ssn'];
          echo "</option>";
        }
      }
?>
<?php
      echo "</select>";
      echo "<input type=\"submit\" value=\"Submit\" />";
      echo "</form>";
      if(array_key_exists('patient_select', $_POST) && $_POST['patient_select'] ) {
        $dose = $_POST['patient_select'];
        $db = connectToDatabase();
        $update_dose = "UPDATE dose SET status='used' WHERE Tracking_no=".$dose;
        if ($db->query($update_dose)){
          echo "<div class=\"success\">The appointment was successfully updated and the dose has been marked used!</div>";
        } else {
          echo "<div class=\"error\">The appointment was not updated!</div>";
        }

      }

?>    
  </div>
  </body>
</html>
