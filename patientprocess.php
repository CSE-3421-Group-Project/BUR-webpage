<?php
$con=mysqli_connect('localhost', 'root', 'mysql', 'PATIENT');
if(!$con)
{
	die('Cannot connect'.mysqli_connect_error());
}
$FirstName = $_POST['Fname'];
$LastName = $_POST['Lname'];
$PatientAge = $_POST['Age'];
$PatientPhone = $_POST['Phone'];
$PatientSSN = $_POST['SSN']
$PatientPriority = $_POST['priority'];
$EarliestDay = $_POST['Date'];
/*
$sql = "Insert into PATIENT (SSN, Fname, Lname, Age, Priority, Phone, Earliest_Date)
values ('$PatientSSN', '$FirstName', '$LastName', '$PatientAge', '$EarliestDay)";

if(!mysqli_query($con, $sql))
{
	echo 'Not inserted';
}
else {
	$prioritySQL = "Select Age, SSN from PATIENT where Priority = $PatientPriority";
	$result = mysqli_query($con, $prioritySQL);
	$ageArray = [];
	while($row = mysqli_fetch_array($result)) 
	{
		array_push($ageArray, $row['Age'])

	}
	sort($ageArray);
	$waitListVal = 1;
	$waitListSQL = "Insert into Patient (WaitList) values($waitListVal)
	for($i = 0, $ $num = count($ageArray), i < $num; $i++)
	{
		*/
?>