<!DOCTYPE html>
<html>
  <head>
    <title>List current vaccine inventory</title>
    <meta charset="UTF-8" />
    <link href="main_styles.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <div class="main-content">
      <a href="../admin.php">Go back</a>

<?php
function connectToDatabase() {
    // TODO: Is this database name correct?
    $conn = new mysqli("localhost", "bur", "bur", "BUR_webpage");
    if($conn->connect_error) die($conn->connect_error);
    if($conn === false) die("Error: Could not connect".mysqli_connect_error());
    return $conn;

}

function listCurrentInventory($db){
    $sql = <<<EOD
        select COUNT(d.Tracking_no) as doseCount, d.Status as status, b.Manufacturer as manufacturer
        from DOSE as d, BATCH as b
        where d.Batch_no=b.Batch_no
        group by b.Manufacturer, d.Status
EOD;
    $result = $db->query($sql);
    $inventory = array();
    while ($row = $result->fetch_array()){
        $inventory[ $row['manufacturer'] ][ $row['status'] ] = $row['doseCount'];
        $inventory[ $row['manufacturer'] ][ 'total' ] += $row['doseCount'];

    }
?>

    <div id="current_inventory">
    <h2>Current vaccine inventory</h3>
    <table>
    <tr>
    <th>Manufacturer</th>
    <th>Received</th>
    <th>Distributed</th>
    <th>Expired</th>
    <th>Available</th>
    </tr>

<?php
    foreach($inventory as $name => $count) {
        echo "<tr>";
        echo "<td>".$Name."</td>";
        echo "<td>".$count['total']."</td>";
        echo "<td>".$count['used']."</td>";
        echo "<td>".$count['expired']."</td>";
        echo "<td>".$count['available']."</td>";
        echo "</tr>";
    }
}

$db = connectToDatabase();
listCurrentInventory($db);
?>
    </table>
    </div>
</body>
</html>
