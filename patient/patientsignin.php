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
	<h1> BUR Patient Sign-Up </h1>
			<div class="form-grid" id = "patientInfo">
				<label>Enter Your Full Name: </label><input type="text" name="fullname" value = ""/>
				<label>Age: </label><input type="number"  name="age"/>
				<label>Phone: </label><input type="text" name="phone"/>
				<label>SSN: </label><input type="number" name="ssn"/>
				<label> Priority Group: </label><input type="number" name= "priority"/>
				<label>Earliest Date to Take the Vaccine: </label><input type="date" name="pref_date"/>
			</div>
	  	<input type="submit" name = "submit">

		</form>
		<?php
	

	function findDose($Pref_Date, $db) {
		$availableDoseQuery = "select tracking_no from batch, dose where '$Pref_Date <= Exp_date and 
		status = \"available\"";
		$result=$db->query($availableDosesQuery);
		$firstAvailableDose = "0";
		if ($result) {
		 if ($result->num_rows >0) 
		 {
						$firstAvailable = $row['tracking_no'];
		 }
		}
		return $firstAvailable;
	}
	
	function addPatient($db)
	{
		$PatientSQL = "Insert into patient (Ssn, Name, Age, Priority, Waitlist, Phone, Pref_Date) 
		 values ('$Ssn', '$PName', '$PatientAge', 
		'$priority', '$waitlist', '$Phone' '$date')";
		$result=$db->query($PatientSQL);
		if(!$result) 
		{
			echo 'Not inserted';
		}
	}
	
	function makeAppt($db, $dose, $DesiredDate)
	{
		$ApptSQL = "Insert into appointment (P_Ssn, Tracking_no, Date)
		values ('$Ssn]', '$dose', '$DesiredDate')";
		$result=$db->query($ApptSQL);
		if(!$result) 
		{
			echo 'Not inserted';
		}
		else{
			echo 'You have an appointment on ', $DesiredDate, '.  See you soon!';
			$updateDose = "update dose set status = \"reserved\" where tracking_no = '$dose'";
			$result=$db->query($ApptSQL);
			if(!$result) 
			{
				echo 'Not updated';
			}
		} 
	}
		
	

function connectToDatabase() {
	$conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
	if($conn->connect_error) die($conn->connect_error);
	if($conn === false) die("Error: Could not connect".mysqli_connect_error());
	return $conn;
}


	

	/*
	if((isset($_POST['submit'])))
	{
		$PName = $db->real_escape_string($_POST['name']);
		$PatientAge = $db->real_escape_string($_POST['age']);
		$Phone = $db->real_escape_string($_POST['phone']);
		$Ssn = $db->real_escape_string($_POST['ssn']);
		$priority = $db->real_escape_string($_POST['priority']);
		$date = $db->real_escape_string($_POST['pref_date']);
	}*/

	if(isset($_POST['submit']))
	{
		echo "hi";
		$Pname = $_POST['name'];
		$PatientAge = $_POST['age'];
		$Phone = $_POST['phone'];
		$Ssn = $_POST['Ssn'];
		$priority = $_POST['priority'];
		$date = $_POST['date'];
		$db = connectToDatabase();
		$doseVal = 0;
		$doseVal = findDose($date, $db);	
		$waitList = 0;
		if(is_null($doseVal))
		{
			$waitList = 1;
			echo 'You have been added to the waitlist. We will contact you as appointments become available';
			addPatient($db);
		}
		else{
		  addPatient($db);
			makeAppt($db, $doseVal, $date);
		}
	}
	?>

	</body>
</html>
