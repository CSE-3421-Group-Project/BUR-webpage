<?php
function listVaccinated($db){
?>

<div id="vaccinated_patients">
    <h3>Vaccinated patients</h3>
    <table>
        <tr>
            <th>Name</th>
            <th>SSN</th>
            <th>Date vaccinated</th>
            <th>Dose manufacturer</th>
        </tr>

<?php
    $sql = <<<EOD
        select p.name as name, p.Ssn as ssn, a.Date as date, b.Manufacturer as manufacturer
        from PATIENT as p, APPOINTMENT as a, DOSE as d, BATCH as b
        where p.Ssn=a.P_id
        and a.Dose_no=d.Tracking_no
        and d.Batch_no=b.Batch_no
        and d.Status=used
EOD;
    $result = $db->query($sql);
    while ($patient = $result->fetch_array()){
        $name = $patient['name'];
        $ssn = $patient['ssn'];
        $date = $patient['date'];
        $manufacturer = $patient['manufacturer'];
        echo "<tr>";
        echo "<td>".$name."</td>";
        echo "<td>".$ssn."</td>";
        echo "<td>".$date."</td>";
        echo "<td>".$manufacturer."</td>";
        echo "</tr>";
}
?>
    </table>
    </div>

<?php
}
?>
