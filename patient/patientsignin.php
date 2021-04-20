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
			<div class="form-grid">
				<label>Enter Your Full Name: </label><input type="text" name="name"/>
				<label>Age: </label><input type="number"  name="age"/>
				<label>Phone: </label><input type="text" name="phone"/>
				<label>SSN: </label><input type="number" name="ssn"/>
				<label> Priority Group: </label><input type="number" name= "priority"/>
				<label>Earliest Date to Take the Vaccine: </label><input type="date" name="pref_date"/>
			</div>
	  	<input type="submit">

		</form>
		<?php
		$con=mysqli_connect('localhost', 'bur', 'bur', 'BUR-webpage');
		if(!$con)
		{
			die('Cannot connect'.mysqli_connect_error());
		}
		$FullName = $_POST['name'];
		$PatientAge = $_POST['age'];
		$PatientPhone = $_POST['phone'];
		$PatientSSN = $_POST['ssn'];
		$PatientPriority = $_POST['priority'];
		$DesiredDate = $_POST['pref_date'];




	function findDose($Pref_Date, $db) {
		$availableDoseQuery = "select tracking_no from batch, dose where '$Pref_Date <= Exp_date and 
		status = \"available\"";
		$result=$db->query($availableDosesQuery);
		$firstAvailable;
		If ($result) {
			If ($result->num_rows >0) 
			{
					$firstAvailable = $row['tracking_no'];
			}
	}
}
		return $firstAvailable;

	function addPatient($db)
	{
		$PatientSQL = "Insert into patient (Ssn, Name, Age, Priority, Waitlist, Phone, Pref_Date)
		values ('$PatientSSN', '$FullName', '$PatientAge', 
		'$PatientPriority', '$waitList', '$PatientPhone' '$DesiredDate')";
		$result=$db->query($PatientSQL);
		if(!$result) #Not sure about this.  Would anything be returned
		{
			echo 'Not inserted';
		}
	}
	
	function makeAppt($db, $dose)
	{
		$ApptSQL = "Insert into appointment (P_Ssn, Tracking_no, Date)
		values ('$PatientSSN', '$dose', '$DesiredDate')";
		$result=$db->query($ApptSQL);
		if(!$result) #Not sure about this.  Would anything be returned
		{
			echo 'Not inserted';
		}
		else{
			echo 'You have an appointment on ', $DesiredDate, '.  See you soon!';
		} 
	}
		
	

function connectToDatabase() {
	$conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
	if($conn->connect_error) die($conn->connect_error);
	if($conn === false) die("Error: Could not connect".mysqli_connect_error());
	return $conn;
}
	$db = connectToDatabase();
	$doseVal = 0;
	$doseVal = findDose($DesiredDate);
	$waitList = 0;
	/*is_null($dose)
	{
		#$waitList = 1;
		echo 'Error here';
	}*/

	If($waitList == 1)
	{
		echo 'You have been added to the waitlist. We will contact you as appointments become available';
	}
	else{
			makeAppt($db, $doseVal);
	}


	
		?>
	</body>
</html>
