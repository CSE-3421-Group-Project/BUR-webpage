<!DOCTYPE html>
<html>
  <head>
    <title>BUR Admin Page</title>
    <meta charset="UTF-8" />
    <link href="main_styles.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <nav>
      <h1>BUR Admin Dashboard</h1>
    </nav>
    <div class="main-content">
      <a href="index.php">Go back</a>
      <?php
      function connectToDatabase() {
        // TODO: Is this database name correct?
        $conn = new mysqli("localhost", "root", "mysql", "bur");
        if($conn->connect_error) die($conn->connect_error);
        if($conn === false) die("Error: Could not connect".mysqli_connect_error());
        return $conn;
      }

      function getDoses($batchId, $db) {
        $get_doses_query = "SELECT Tracking_no FROM Dose WHERE Dose.Batch_no = {$batchId}";
        $result = $db->query($get_doses_query);
        if(!$result) die($db->error);
        if($result && $result->num_rows > 0) {
          return $result;
        }
        return null;
      }

      function getWaitlistedPatientsByRow($num_of_patients, $db) {
        $waitlisted_patients_query = "SELECT P_id, Earliest_date FROM patient WHERE Waitlisted = TRUE ORDER BY Priority LIMIT {$num_of_patients}";
        $result = $db->query($waitlisted_patients_query);
        if($result && $result->num_rows > 0) {
          return $result;
        }
        return null;
      }

      function createAppointment($pid, $tracking_no, $date, $db) {
        try {
          $db->begin_transaction();
          $db->query("INSERT INTO appointment (P_id, Dose_no, Date) VALUES ({$pid}, {$tracking_no}, \"{$date}\")");
          $db->query("UPDATE patient SET Waitlisted = FALSE WHERE P_id = {$pid}");
          // TODO: Might need to reserve dose
          $db->commit();
          // TODO: This is for testing. Please remove in final release.
          echo "<div>Made appointment for {$pid} on {$date}</div>";
        } catch (\Throwable $e) {
          $db->rollback();
          throw $e;
        }
      }

      function scheduleWaitlistedToBatch($batchId) {
        // Open database connection
        $db = connectToDatabase();

        // Get num of doses in batch
        $doses = getDoses($batchId, $db);
        if(!$doses) return;

        // Get patients on waitlist sorted by priority
        $patients = getWaitlistedPatientsByRow($doses->num_rows, $db);
        if(!$patients) return;

        // Assign first x patients to a batch at their earliest date.
        while($patient = $patients->fetch_assoc()) {
          $dose = $doses->fetch_assoc();
          // TODO: How should the date be chosen?
          createAppointment($patient["P_id"], $dose["Tracking_no"], $patient["Earliest_date"], $db);
        }

        // Close database connection
        $db->close();
      }

      // TODO: For testing purposes. Feel free to delete.
      scheduleWaitlistedToBatch(1);
      ?>
    </div>
  </body>
</html>
