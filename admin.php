<!DOCTYPE html>
<html>
  <head>
    <title>BUR Admin Page</title>
    <meta charset="UTF-8" />
  </head>
  <body>
    <h1>BUR Admin Dashboard</h1>
    <a href="index.php">Go back</a>
    <?php
    function connectToDatabase() {
      // TODO: Is this database name correct?
      $conn = new mysqli("localhost", "root", "mysql", "bur");
      if($conn->connect_error) die($conn->connect_error);
      if($conn === false) die("Error: Could not connect".mysqli_connect_error());
      return $conn;
    }

    function closeDatabaseConnection($db) {
      $db->close();
    }

    function getNumOfDoses($batchId, $db) {
      $num_of_doses_query = "SELECT COUNT(Tracking_no) FROM Dose WHERE Dose.BatchId = {$batchId}";
      $result = $db->query($num_of_doses_query);
      if(!$result) die($db->error);
      if($result && $result->num_rows == 1) {
        $row = $result->fetch_array();
        return $row[0];
      } else {
        return 0;
      }
    }

    function scheduleWaitlistedToBatch($batchId) {
      // Open database connection
      $db = connectToDatabase();

      // Get num of doses in batch
      $doses = getNumOfDoses($batchId, $db);
      echo $doses;

      // Get patients on waitlist sorted by priority

      // Assign first x patients to a batch at their earliest date.

      // Close database connection
      closeDatabaseConnection($db);
    }
    ?>
  </body>
</html>
