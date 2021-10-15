<?php
        error_reporting(0);
        ini_set('display_errors', 1);

        if ($_POST) {
            // get timeslots info
            $string = file_get_contents("slot-config.json");
            $rules = json_decode($string, true);

            // get upload csv data
            $csv = array();

            // check there are no errors
            if ($_FILES['csv']['error'] == 0) {
                $name = $_FILES['csv']['name'];
                $ext = strtolower(end(explode('.', $_FILES['csv']['name'])));
                $type = $_FILES['csv']['type'];
                $tmpName = $_FILES['csv']['tmp_name'];

                // check the file is a csv
                if ($ext === 'csv') {
                    if (($handle = fopen($tmpName, 'r')) !== FALSE) {
                        // necessary if a large csv file
                        set_time_limit(0);
                        $row = 0;
                        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                            // number of fields in the csv
                            $col_count = count($data);

                            // get the values from the csv
                            $csv[$row]['date'] = $data[0];
                            $csv[$row]['employeeId'] = $data[1];
                            $csv[$row]['startTime'] = $data[2];
                            $csv[$row]['endTime'] = $data[3];

                            // inc the row
                            $row++;
                        }
                        fclose($handle);
                    }
                }
            }

            $output = array();
            foreach ($csv as $d) {
                if ($d["date"] == "Date") continue; // ignore 1st line
                $dateparts = explode(".", $d['date']);
                $dayForDate = strtolower(date("l", mktime(0, 0, 0, $dateparts[1], $dateparts[0], $dateparts[2])));

                $applicable_rule = array();
                if ($rules["specific"][$d["date"]] != null) {
                    $applicable_rule = $rules["specific"][$d["date"]];
                } else if ($rules["regular"][$dayForDate] != null) {
                    $applicable_rule = $rules["regular"][$dayForDate];
                }

                if ($applicable_rule != null) {
                    foreach ($applicable_rule as $percent => $hourgroups) {

                        $employeeId = $d["employeeId"];
                        $from = strtotime($d["startTime"]);
                        $to = strtotime($d["endTime"]);

                        foreach ($hourgroups as $hg) {
                            $beginTime = strtotime($hg['start']);
                            $endTime = strtotime($hg['end']);
                            $e = 0;

                            // all hour in one 
                            if ($from >= $beginTime && $to <= $endTime) {
                                $e = (int) round(abs($to - $from) / 60, 2);
                            }

                            // partial 
                            elseif (($from >= $beginTime && $from < $endTime) && !($to <= $endTime)) {
                                $e = (int) round(abs($endTime - $from) / 60, 2);
                            }

                            // partial 
                            elseif (!($from >= $beginTime && $from < $endTime) && ($to <= $endTime && !($to <= $beginTime))) {
                                $e = (int)round(abs($to - $beginTime) / 60, 2);
                            }

                            // entire range is 
                            elseif ($from <= $beginTime && $endTime <= $to) {
                                $e = (int)round(abs($endTime - $beginTime) / 60, 2);
                            }

                            if ($e > 0) $output[$employeeId][$percent] += $e;
                        }
                    }
                }
            }
        }

        if ($output != null) {
            $data = array();
            $data[] = array("Name", "Salary Percentage (%)", "Hours (hrs)");

                foreach ($output as $name => $opt) {
                    foreach ($opt as $salaryPercent => $mins) {
                        $hours = floor($mins / 60) . ':' . ($mins -   floor($mins / 60) * 60);
                        $data[] = array($name, $salaryPercent, $hours);
                    }
                }

                $filename = "timesheet-" . date('Y_m_d-H-i-s') . ".csv";
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="'.$filename.'"');

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