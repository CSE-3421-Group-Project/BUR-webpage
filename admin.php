<?php
function listCurrentInventory($db){
    $sql = <<<EOD
        select COUNT(d.Tracking_no) as doseCount, d.Status as status, b.Manufacturer as manufacturer
        from DOSE as d, BATCH as b
        where d.Batch_no=b.Batch_no
        group by b.Manufacturer, d.Status
EOD;
    $result = $db->query($sql);
    while ($row = $result->fetch_array()){
        inventory[ $row['manufacturer'] ][ $row['status'] ] = $row['doseCount'];
        inventory[ $row['manufacturer'] ][ 'total' ] += $row['doseCount'];
        
    }
?>

    <div id="current_inventory">
    <h3>Current vaccine inventory</h3>
    <table>
    <tr>
    <th>Manufacturer</th>
    <th>Received</th>
    <th>Distributed</th>
    <th>Expired</th>
    <th>Available</th>
    </tr>

<?php
    foreach($inventory as $name => $inventory) {
        echo "<tr>";
        echo "<td>".$Name."</td>";
        echo "<td>"$inventory['total'].."</td>";
        echo "<td>".$inventory['used']."</td>";
        echo "<td>".$inventory['expired']."</td>";
        echo "<td>".$inventory['available']."</td>";
        echo "</tr>";
    }
?>
    </table>
    </div>

<?php
}
?>
