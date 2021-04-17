<!DOCTYPE html>
<html>
  <head>
    <title>List current vaccine inventory</title>
    <meta charset="UTF-8" />
    <link href="../main_styles.css" type="text/css" rel="stylesheet" />
    <style>
        table {
            cell-spacing: 20px;
            border-collapse: collapse; 
        }   
        td, th {
            text-align: center;
            padding: 5px;
            border: 1px solid black;
        } 
    </style>  
  </head>
  <body>
    <nav>
      <h1>BUR Admin Dashboard</h1>
    </nav>
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
        order by b.Manufacturer
EOD;
    $result = $db->query($sql);
    $inventory = array();
    while ($row = $result->fetch_array()){
        $manufacturer = $row['manufacturer'];
        $status = $row['status'];
        $count = isset($row['doseCount']) ? $row['doseCount'] : 0;

        if( !isset($inventory[$manufacturer]) ){
            $inventory[$manufacturer] = array();
        }
        $inventory[ $manufacturer ][ $status ] = $count;
        $inventory[ $manufacturer ][ 'total' ] = array_key_exists('total' , $inventory[ $manufacturer ]) ? $inventory[ $manufacturer ]['total'] + $count : $count;
    }
?>

    <div id="current_inventory">
    <h2>Current vaccine inventory</h3>
    <table>
    <tr>
    <th>Manufacturer</th>
    <th>Total Received</th>
    <th>Total Distributed</th>
    <th>Reserved</th>
    <th>Expired</th>
    <th>Available</th>
    </tr>

<?php
    foreach($inventory as $name => $count) {
        $total = isset($count['total']) ? $count['total'] : 0;
        $used = isset($count['used']) ? $count['used'] : 0;
        $reserved = isset($count['reserved']) ? $count['reserved'] : 0;
        $expired = isset($count['expired']) ? $count['expired'] : 0;
        $available = isset($count['available']) ? $count['available'] : 0;

        echo "<tr>";
        echo "<td>".$name."</td>";
        echo "<td>".$total."</td>";
        echo "<td>".$used."</td>";
        echo "<td>".$reserved."</td>";
        echo "<td>".$expired."</td>";
        echo "<td>".$available."</td>";
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
