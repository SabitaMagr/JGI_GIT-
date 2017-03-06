1. change the path to the index.php file of the project in the attendance.sh before adding the cron 
2. on the terminal run crontab -e 
3. add this line at the bottom "40 12 * * * /home/ukesh/attendance.sh"
4. make sure the path to the attendance.sh file is correct