<?php

include("includes/db_connect.php");

// CSV Headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="configuration_items.csv"');

// Open output stream
$output = fopen("php://output", "w");

// Column Headers
fputcsv($output, array(
    'CI Name',
    'CI Type',
    'Owner',
    'Environment',
    'Status',
    'Priority',
    'Criticality',
    'Hostname',
    'IP Address',
    'Operating System',
    'Version',
    'Location',
    'Support Group',
    'Health Score',
    'Last Updated'
));

// Fetch Data
$result = mysqli_query($conn, "SELECT * FROM configuration_items");

// Write Data
while($row = mysqli_fetch_assoc($result))
{
    fputcsv($output, array(
        $row['ci_name'],
        $row['ci_type'],
        $row['owner'],
        $row['environment'],
        $row['status'],
        $row['priority'],
        $row['criticality'],
        $row['hostname'],
        $row['ip_address'],
        $row['operating_system'],
        $row['version'],
        $row['location'],
        $row['support_group'],
        $row['health_score'],
        $row['last_updated']
    ));
}

fclose($output);
exit;

?>