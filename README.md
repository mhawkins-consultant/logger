logger allows viewing and filtering of messages in text based log files of the kind that are created by rsyslog.
logger is composed of index.php and reset.php.
index.php allows a web browser to quickly and easily navigate a long list of text log files and the user can
enter an include regex and an exclude regex to filter the messages as the user desires.
index.php also refreshes the page periodically according to a user selectable time period
if the user selected a very large log file (where very large depends on the performnce of the host machine but say,
a file > 1Gig) then index.php might choke trying to consume the large file. In that case, the user can click on
'here' in the message "Please wait while the log file is parsed. Click HERE to cancel and try again."
The 'here' is a URL to reset.php. reset.php resets the page back to default settings so that the user can try again.
