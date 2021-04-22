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

      function findAvailableDose($db)
      {
        $dosesql = "select tracking_no from dose where status = \"available\"";
        $result=$db->query($dosesql);
        if ($result) {
          if ($result->num_rows >0) 
          {
              $selectedDose = $row['tracking_no'];
          }
        }
        return $selectedDose;
      }

      function scheduleWaitlisted($db, $DesiredDate)
      {
        $findPatient = "select * from patient where 
        and waitlist = 1 order by age and group by priority";
        $result=$db->query($findPatient);
        if ($result) {
          if ($result->num_rows >0) 
          {
              $waitlistedPatient = $row['Ssn'];
              $dose = findAvailableDose($db);
              $sqlAppt = "Insert into appointment (P_Ssn, Tracking_no, Date) values ($waitlistedPatient, $dose, '$DesiredDate')";
		          $result=$db->query($sqlAppt);
		          if(!$result) 
		          {
			          echo $db->error;
		          }
          }
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
            scheduleWaitlisted($db, $DesiredDate);
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

  