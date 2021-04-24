<!DOCTYPE html>
<html>
	<head>
		<title> Patient Sign-In </title>
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
			<h1> BUR Patient Sign-Up </h1>
		</nav>
		<div class="main-content">
		<a href="../patient.php">Go back</a>
		<?php
			function findDose($Pref_Date, $db) {
				$availableDoseQuery = "select Tracking_no from batch, dose where batch.Batch_no = dose.Batch_no
				and \"$Pref_Date\" <= Exp_date and status = \"available\"";
				$result=$db->query($availableDoseQuery);
				$firstAvailableDose = null;
				if ($result && $result->num_rows > 0) {
				
							$firstAvailableDose = $result->fetch_assoc()['Tracking_no'];
				}
				return $firstAvailableDose;
			}
			
			function addPatient($db, $Ssn, $PName, $PatientAge, $priority, $waitlist, $Phone, $date)
			{
				$PatientSQL = "Insert into patient (Ssn, Name, Age, Priority, Waitlist, Phone, Pref_Date) 
				values ($Ssn, '$PName', $PatientAge, 
				$priority, $waitlist, '$Phone', '$date')";
				$result=$db->query($PatientSQL);
				if(!$result) 
				{
					echo $db->error;
				}
			}
			
			function makeAppt($db, $dose, $DesiredDate, $Ssn)
			{
				$ApptSQL = "Insert into appointment (P_Ssn, Tracking_no, Date)
				values ($Ssn, $dose, '$DesiredDate')";
				$result=$db->query($ApptSQL);
				if(!$result) 
				{
					echo $db->error;
				}
				else{
					echo '<div class="success">You have an appointment on ', $DesiredDate, '.  See you soon!</div>';
					$updateDose = "update dose set status = \"reserved\" where tracking_no = $dose";
					$result=$db->query($updateDose);
					if(!$result) 
					{
						echo $db->error;
					}
				} 
			}

			function connectToDatabase() {
				$conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
				if($conn->connect_error) die($conn->connect_error);
				if($conn === false) die("Error: Could not connect".mysqli_connect_error());
				return $conn;
			}

			if(array_key_exists('fullname', $_POST) && $_POST['fullname'] && 
				 array_key_exists('age', $_POST) && $_POST['age'] &&
				 array_key_exists('phone', $_POST) && $_POST['phone'] &&
				 array_key_exists('ssn', $_POST) && $_POST['ssn'] &&
				 array_key_exists('priority', $_POST) && $_POST['priority'] &&
				 array_key_exists('pref_date', $_POST) && $_POST['pref_date'])
			{
				$Pname = $_POST['fullname'];
				$PatientAge = $_POST['age'];
				$Phone = $_POST['phone'];
				$Ssn = $_POST['ssn'];
				$priority = $_POST['priority'];
				$date = $_POST['pref_date'];
				$db = connectToDatabase();
				$doseVal = 0;
				$doseVal = findDose($date, $db);	
				$waitList = is_null($doseVal) ? 1:0;
				addPatient($db, $Ssn, $Pname, $PatientAge, $priority, $waitList, $Phone, $date);
				if($waitList == 1)
				{
					echo '<div class="success">You have been added to the waitlist. We will contact you as appointments become available</div>';
				}
				else{
					makeAppt($db, $doseVal, $date, $Ssn);
				}
			}
			
			echo "<form method=\"post\" action=\"", $_SERVER['PHP_SELF'], "\" class=\"form-wrapper\">";
			?>
			<div class="form-grid">
				<label>Enter Your Full Name: </label><input type="text" name="fullname" value = ""/>
				<label>Age: </label><input type="number"  name="age"/>
				<label>Phone: </label><input type="text" name="phone"/>
				<label>SSN: </label><input type="number" name="ssn"/>
				<label>Priority Group: </label><input type="number" name= "priority"/>
				<label>Earliest Date to Take the Vaccine: </label><input type="date" name="pref_date"/>
			</div>
	  	<input type="submit" name = "submit" />
		</form>
	</div>

	</body>
</html>
