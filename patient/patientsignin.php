<!DOCTYPE html>
<html>
	<head>
		<title> Patient Sign-In </title>
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
			<form action = PatientProcess.php method="post">
			Enter Your Full Name: <input type="text" name="Name" placeholder = "Full Name"><br><br><br>
			Age: <input type="text" name = "Age" placeholder = "Age"><br><br><br>
			Phone: <input type="text" name="Phone" placeholder = "Phone"><br><br><br>
			SSN: <input type="text" name ="SSN" placeholder = "SSN"><br><br><br>
			<br>
	
			<p> Select a priority group
				<select name="priority">
				<option value= '1'>Group 1</option>
				<option value= '2'>Group 2</option>
				<option value= '3'>Group 3</option>
				</select>
			</p>
			<br><br><br>
	    <input type="text" name="Date" placeholder = "Earliest Date"><br><br>
			<input type="submit">

		</form>
	</body>
</html>
