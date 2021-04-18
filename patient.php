<!DOCTYPE html>
<html>
  <head>
    <title>BUR Patient Site</title>
    <link href="main_styles.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <nav>
      <h1>BUR Patient Dashboard</h1>
    </nav>
    <div class="main-content">
      <a href="index.php">Go back</a>
      <div class ="grid">
        <div class = "action-card" onclick="redirect('patient/patientsignin.php')">
          <h2>Patient Sign-Up</h2>
          <p>Sign-Up to receive the COVID vaccine </p>
        </div>
        <div class = "action-card" onclick="redirect('patient/cancelAppt.php'">
          <h2>Appointment Cancelation</h2>
          <p> Cancel a previously scheduled appointment </p>
        </div>
    </div>
  </body>
</html>
