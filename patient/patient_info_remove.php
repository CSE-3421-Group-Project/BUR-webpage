<html>
  <head>
	  <title> Patient Information Removal </title>
    <style>
		 	/*Functions added since we should not be able to increment an SSN despite it being an integer*/
			input[type=number] {
  		-moz-appearance: textfield;
			}
 			
			input::-webkit-outer-spin-button,
			input::-webkit-inner-spin-button {
  		-webkit-appearance: none;
  			margin: 0;
			}
			</style>
    	<meta charset="UTF-8" />
    	<link href="../main_styles.css" type="text/css" rel="stylesheet" />
    	<script type="text/javascript">
    	function redirect(newPath) {
      window.location.href = `${window.location.protocol}//${window.location.hostname}:${window.location.port}/${newPath}`;
    }
    </script>
  </head>
  <body>
    <nav>
      <h1> BUR Patient Information Removal Request </h1>
    </nav>
    <div class="main-content">
      <a href="../patient.php">Go back</a>
      <?php

      function connectToDatabase() {
        $conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
        if($conn->connect_error) die($conn->connect_error);
        if($conn === false) die("Error: Could not connect".mysqli_connect_error());
        return $conn;
      }

      function findAvailableDose($date, $db)
      {
        try {
          $dosesql = $db->prepare("SELECT tracking_no FROM dose, batch WHERE status = \"available\" AND dose.Batch_no = batch.Batch_no AND ? < Exp_date LIMIT 1");
          $dosesql->bind_param("s", $date);
          $dosesql->execute();
          $result = $dosesql->get_result();
          if ($result->num_rows >0) 
          {
            $row = $result->fetch_assoc();
            return $row['tracking_no'];
          }
        } catch (\Throwable $e) {
          throw $e;
        }

        return null;
      }

      function scheduleWaitlisted($db)
      {
        try {
          $findPatient = "SELECT Ssn, Pref_date from patient where Waitlist = 1 order by Priority, Age DESC";
          $result=$db->query($findPatient);
          if ($result) {
            if ($result->num_rows >0) 
            {
              while($row = $result->fetch_assoc()) {
                $waitlistedPatient = $row['Ssn'];
                $dose = findAvailableDose($row['Pref_date'], $db);
                if($dose != null) {
                  $sqlAppt = $db->prepare("INSERT INTO appointment (P_Ssn, Tracking_no, Date) VALUES (?, ?, ?)");
                  $sqlAppt->bind_param("iis", $waitlistedPatient, $dose, $row['Pref_date']);
                  $sqlAppt->execute();

                  $reserveDose = $db->prepare("UPDATE dose SET Status = 'reserved' WHERE Tracking_no = ?");
                  $reserveDose->bind_param("i", $dose);
                  $reserveDose->execute();

                  $takeOffWaitlist = $db->prepare("UPDATE patient SET Waitlist = FALSE WHERE Ssn = ?");
                  $takeOffWaitlist->bind_param("i", $waitlistedPatient);
                  $takeOffWaitlist->execute();
                }
              }
            }
          }
        } catch (\Throwable $e) {
          throw $e;
        }
      }

      function deletePatient($db, $ssn)
      {
        $delPatient = "delete from patient where Ssn = $ssn";
        $result=$db->query($delPatient);
        if(!$result)
        {
          echo $db->error;
        }
      }

      if(array_key_exists('ssn', $_POST) && $_POST['ssn'])
      {
        $db = connectToDatabase();
        $Ssn = $_POST['ssn'];

        $sql = "select waitlist from Patient where Ssn = '$Ssn'";
        $result=$db->query($sql);
        $waitlistVal = null;
        if($result && $result->num_rows > 0)
        {
          $waitlistVal = $result->fetch_assoc()['waitlist'];
        }

        if($waitlistVal == 1)
        {
          deletePatient($db, $Ssn);
        }
        elseif($waitlistVal == 0)
        {
          $sql = "select p.Ssn, p.Pref_date from Patient as p, appointment as a, dose as d
            where a.P_Ssn = $Ssn and p.Ssn = $Ssn and a.Tracking_no = d.Tracking_no and d.status = \"reserved\"";
          $result=$db->query($sql);
          if($result && $result->num_rows == 0)
          {
            echo '<div class="error">We couldn\'t find your appointment.</div>';
          }
          else {
            #Trigger handles appointment cancelation and making the dose available
            $patient = $result->fetch_assoc();
            $Ssn = $patient['Ssn'];
            $DesiredDate = $patient['Pref_date'];
        
            deletePatient($db, $Ssn);
            scheduleWaitlisted($db);
            echo '<div class="success">We removed your appointment on ', $DesiredDate, '</div>';
          }
        }
        else{
          echo '<div class="error">You were not found in our system.</div>'; 
        }
      }
      
      echo "<form method=\"post\" action=\"", $_SERVER['PHP_SELF'], "\" class=\"form-wrapper\">"; ?>
      <div class="form-grid">
        <label>Enter your SSN: </label><input type="number" name="ssn"/>
      </div>
      <input type="submit">
    </div>
  </body>
</html>

  