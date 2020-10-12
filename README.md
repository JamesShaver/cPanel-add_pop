# cPanel-add_pop
Allows visitors to add a free POP3/IMAP email account via the cPanel API, adds their information to a MySql database, and emails the site admin.

# Requirements:
  - cPanel Access
  - At least one (1) MySql database available
  - PHP with Curl

# Installation:
  - Create an API token in cPanel
  - Open config.php with your favorite PHP editor (IE: Notepad)
  - Edit the config variables to suit your needs
  - Create a database based on the variables in config.php
  - Run the included 'database.sql' in phpMyAdmin
  - Upload contents of the "FreeMail" directory to your server.
