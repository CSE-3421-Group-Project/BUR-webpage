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
      <h2 class="title">Add Batch to System</h2>
      <hr class="form-divider" />
      <?php
      function connectToDatabase() {
        $conn = new mysqli("localhost", "bur", "bur", "bur_webpage");
        if($conn->connect_error) die($conn->connect_error);
        if($conn === false) die("Error: Could not connect".mysqli_connect_error());
        return $conn;
      }

      function getDoses($batchId, $db) {
        try {
          $get_doses_query = $db->prepare("SELECT Tracking_no FROM dose WHERE dose.Batch_no = ? and dose.Status = \"available\"");
          if($get_doses_query) {
            $get_doses_query->bind_param("i", $batchId);
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

      function getWaitlistedPatientsByRow($num_of_patients, $expiration, $db) {
        try {
          $waitlisted_patients_query = $db->prepare("SELECT Ssn, Pref_date FROM patient WHERE Waitlist = TRUE and Pref_date < ? ORDER BY Priority, Age DESC LIMIT ?");
          if($waitlisted_patients_query) {
            $waitlisted_patients_query->bind_param("si", $expiration, $num_of_patients);
            $waitlisted_patients_query->execute();
            $result = $waitlisted_patients_query->get_result();
            if($result->num_rows > 0) {
              return $result;
            }
          }
        } catch (\Throwable $e) {
          throw $e;
        }
        return null;
      }

      function createAppointment($pid, $tracking_no, $date, $db) {
        try {
          $db->begin_transaction();
          $insert_appointment = $db->prepare("INSERT INTO appointment (P_Ssn, Tracking_no, Date) VALUES (?, ?, ?)");
          $insert_appointment->bind_param("iis", $pid, $tracking_no, $date);
          $insert_appointment->execute();

          $update_waitlist = $db->prepare("UPDATE patient SET Waitlist = FALSE WHERE Ssn = ?");
          $update_waitlist->bind_param("i", $pid);
          $update_waitlist->execute();

          $reserve_dose = $db->prepare("UPDATE dose SET Status = \"reserved\" WHERE Tracking_no = ?");
          $reserve_dose->bind_param("i", $tracking_no);
          $reserve_dose->execute();

          $db->commit();
        } catch (\Throwable $e) {
          $db->rollback();
          throw $e;
        }
      }

      function scheduleWaitlistedToBatch($batchId, $expiration, $db) {
        // Get num of doses in batch
        $doses = getDoses($batchId, $db);
        if(!$doses) return;

        // Get patients on waitlist sorted by priority
        $patients = getWaitlistedPatientsByRow($doses->num_rows, $expiration, $db);
        if(!$patients) return;

        // Assign first x patients to a batch at their earliest date.
        while($patient = $patients->fetch_assoc()) {
          $dose = $doses->fetch_assoc();
          createAppointment($patient["Ssn"], $dose["Tracking_no"], $patient["Pref_date"], $db);
        }
      }

      if(array_key_exists('manufacturer', $_POST) && $_POST['manufacturer']
         && array_key_exists('expiration', $_POST) && $_POST['expiration']
         && array_key_exists('numOfDoses', $_POST) && $_POST['numOfDoses']) {
        $manufacturer = $_POST['manufacturer'];
        $expiration = $_POST['expiration'];
        $num_of_doses = $_POST['numOfDoses'];

        // Check if the expiration is later than the current date.
        if(new DateTime($expiration) > new DateTime()) {
          $db = connectToDatabase();
          $db->begin_transaction();

          try {
            $insert_batch = $db->prepare("INSERT INTO batch (Manufacturer, Exp_date) VALUES (?, ?)");
            $insert_batch->bind_param("ss", $manufacturer, $expiration);
            $insert_batch->execute();
            $batch = $db->insert_id;

            $insert_dose = $db->prepare("INSERT INTO dose (Batch_no, Status) VALUES (?, \"available\")");
            $insert_dose->bind_param("i", $batch);
            for($i = 0; $i < $num_of_doses; $i++) {
              $insert_dose->execute();
            }
            
            $db->commit();

            scheduleWaitlistedToBatch($batch, $expiration, $db);
          } catch (\Throwable $e) {
            $db->rollback();
            throw $e;
          }

          $db->close();

          echo "<div class=\"success\">The batch was successfully added!</div>";
        } else {
          echo "<div class=\"error\">The batch you entered is already expired!</div>";
        }
      }
      
      echo "<form method=\"post\" action=\"", $_SERVER['PHP_SELF'], "\" class=\"form-wrapper\">";
      ?>
      <div class="form-grid">
        <label>Manufacturer: </label><input type="text" name="manufacturer"/>
        <label>Expiration Date: </label><input type="date" name="expiration"/>
        <label>Number of doses: </label><input type="number" min="1" value="10" step="1" name="numOfDoses" />
      </div>
      <input type="submit" value="Submit" />
      </form>
    </div>
  </body>
</html>
