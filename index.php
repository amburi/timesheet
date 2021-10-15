<!DOCTYPE html>
<html>
<title>Timesheet</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">

<body>
    <div class="w3-container">
        <h2>Calculate Hours in Timesheet</h2>

        <div class="w3-card w3-padding w3-margin-bottom">
            <form class="w3-container" action="upload.php" method="post" enctype="multipart/form-data">
                <label class="w3-text-blue w3-margin"><b>Upload Timesheet</b></label>
                <input class="w3-input w3-border w3-margin" type="file" name="csv" value="">
                <input class="w3-btn w3-blue w3-margin" type="submit" name="submit" value="Save" />

            </form>
        </div>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // get timeslots info
        $string = file_get_contents("slot-config.json");
        $rules = json_decode($string, true);

        ?>
        <h2>Configured Timeslab Rules</h2>
        <p>To update rules please update slot-config.json</p>
        <table class="w3-margin-top w3-table-all w3-card-4">
            <tr>
                <th width="30%"> Day </th>
                <th width="30%"> Percent of Salary (%) </th>
                <th> Timeslots </th>
            </tr>
            <?php
            foreach ($rules as $day => $r) {
                foreach ($r as $day => $opt) {
                    foreach ($opt as $salaryPercent => $timeslots) {
                        foreach ($timeslots as $timeslot) {
                            echo "<tr><td>" . strtoupper($day) . "</td>" .
                                "<td>" . $salaryPercent . "</td>" .
                                "<td>" . $timeslot["start"] . "-" . $timeslot["end"] . "</td></tr>";
                        }
                    }
                }
            }
            ?>
        </table>


</body>

</html>

<?php
function download($data)
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="sample.csv"');

    $fp = fopen('php://output', 'wb');
    foreach ($data as $line) {
        // though CSV stands for "comma separated value"
        // in many countries (including France) separator is ";"
        fputcsv($fp, $line, ',');
    }
    fclose($fp);
    exit;
}
?>