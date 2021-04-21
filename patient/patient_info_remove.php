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
    <h1> BUR Patient Information Removal Request </h1>
    <div class="form-grid">
      <label>Enter your SSN: </label><input type="number" name="ssn"/>
    </div>
    <input type="submit">
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
      

      function scheduleWaitlisted($db)
      {
        $findPatient = "select * from patient where 
        and waitlist = 1 order by age and group by priority";
        $result=$db->query($findPatient);
        if ($result) {
          if ($result->num_rows >0) 
          {
              $waitlistedPatient = $row['Ssn'];
              $dose = findAvailableDose($db);
              $sqlAppt = "Insert into appointment (P_Ssn, Tracking_no, Date) values ('$waitlistedPatient', '$dose', '$DesiredDate')";
		          $result=$db->query($sqlAppt);
		          if(!$result) 
		          {
			          echo 'Not inserted';
		          }
          }
        }
      }

      function deletePatient($db, $ssn)
      {
        $delPatient = "delete from patient where Ssn = '$ssn'";
        $result=$db->query($delPatient);
        if(!$result)
        {
          echo 'Not deleted';
        }
      }



      $db = connectToDatabase();
      $patient = array('patient' -> array());
	    $patient[$ssn] = array();
      $date = date('m-d-Y');
      $sql = "select Ssn from Patient, appointment where Ssn = '$patient[$ssn]' and '$date' > Date";
        $result=$db->query($sql);
        if($result->num_rows <= 0)
        {
          echo 'You were not found in our system or have already received the vaccine.';
        }
        else {
          #Trigger handles appointment cancelation and making the dose available
          scheduleWaitlisted($db);
          deletePatient($db, $ssn);
        }



    ?>
  </body>
</html>

  