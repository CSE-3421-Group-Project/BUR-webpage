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
      <h2 class="title">Dose Statuses</h2>
      <hr class="form-divider" />
      <table>
      <thead>
        <tr><th>Dose No.</th><th>Patient</th><th>Status</th></tr>
      </thead>
      <tbody>
      <?php
      function connectToDatabase() {
        $conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
        if($conn->connect_error) die($conn->connect_error);
        if($conn === false) die("Error: Could not connect".mysqli_connect_error());
        return $conn;
      }

      function getDosesWithAppointment($db) {
        try {
          $get_doses_query = $db->prepare(
            "select dose.Tracking_no, Name, Status from appointment, dose, patient
              where appointment.Tracking_no = dose.Tracking_no and Ssn = P_Ssn");
          if($get_doses_query) {
            $get_doses_query->execute();
            $result = $get_doses_query->get_result();
            if($result->num_rows > 0) {
              return $result;
            }
          }
        } catch (\Throwable $e) {
          throw $e;
        }
        return null;
      }

      function getDosesWithoutAppointment($db) {
        try {
          $get_doses_query = $db->prepare(
            "select Tracking_no, Status from dose where Status = \"available\"");
          if($get_doses_query) {
            $get_doses_query->execute();
            $result = $get_doses_query->get_result();
            if($result->num_rows > 0) {
              return $result;
            }
          }
        } catch (\Throwable $e) {
          throw $e;
        }
        return null;
      }

      $db = connectToDatabase();

      $doses_with_appt = getDosesWithAppointment($db);

      if($doses_with_appt != null) {
        while($row = $doses_with_appt->fetch_assoc()) {
          echo '<tr><td>', $row["Tracking_no"], '</td><td>', $row["Name"], '</td><td>', $row["Status"], '</td></tr>';
        }
      }

      $doses_without_appt = getDosesWithoutAppointment($db);

      if($doses_without_appt != null) {
        while($row = $doses_without_appt->fetch_assoc()) {
          echo '<tr><td>', $row["Tracking_no"], '</td><td>---</td><td>', $row["Status"], '</td></tr>';
        }
      }

      $db->close();
    
    ?>
    </tbody>
    </table>
    </div>
  </body>
</html>
