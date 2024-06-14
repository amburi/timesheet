# Working Hours Calculation in Different Slabs

This project facilitates the calculation of working hours based on configurable slabs using PHP. It involves configuring salary percentages for different time ranges, uploading timesheet data, and generating outputs in CSV format.

## Prerequisite

Ensure PHP is installed on your system. You can install PHP from [php.net](https://www.php.net/manual/en/install.php).

## Configuration

### 1. Slab Configuration

Configure salary percentages for different time ranges:
- **Regular**: Weekly breakdowns for the entire month.
- **Specific**: Date-specific breaks, such as holidays. These slabs are listed on the landing page.

<img src="screenshots/config.png" width="450" />

### 2. Upload Timesheet CSV

Upload a CSV file containing start and end times of employees' working hours.

<img src="screenshots/upload_timesheet.png" width="450" />

## Execution

1. Run the PHP project using the built-in web server:

   ```bash
   php -S 127.0.0.1:8000
   ```

2. Open [http://localhost:8000/](http://localhost:8000/) in any web browser.

<img src="screenshots/index-page.png" width="450" />

The landing page displays the configured rules. To update these rules, edit `slot-config.php`.

3. Upload Updated Timesheet

Upload the updated timesheet CSV file for processing.

<img src="screenshots/upload_timesheet.png" width="450" />

## Output

Upon processing, a CSV file will be downloaded automatically, containing the calculated working hours based on the configured slabs.

<img src="screenshots/output.png" width="450" />

