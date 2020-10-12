<?php
//error_reporting(E_ALL);
 //Display errors if anything goes wrong
//ini_set('display_errors', 1);
//Cpanel User Info
$cphost = "site.yoursite.com";  //Your cPanel Host or IP address
$cpuser = "cpaneluser"; //Your cPanel user
$cptoken = "ThisIsTheTokenGeneratedInCpanel"; //cPanel API token

// Parameters to parse for Curl
$domain = "yoursite.com";  //Form placeholder display domain name.  The user can't create an address to a name you don't own!
$quota = "10"; // Quota in Megabytes.  Settings to zero ( '0' ) will use the cPanel default value

// Database Info
$dbhost = "localhost";
$dbuser = "mail_db_user";  //Database User
$dbpass = "YOURPASSHERE"; //Database Password
$dbname = "email_users"; //Database Name
$dbtable = "users"; //Database Table Name


//Send Mail Variables
$sender = 'admin@yoursite.com';  // Who the mail will be coming FROM
$recipient = 'admin@yoursite.com'; // Who the mail will be sent TO
$subject = 'New Pop Mail Added';   // Subject of sent mail


/***********  Don't Edit Below Here  ***********/

//Sanitize POST
$post = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

//User IP Address
$remoteAddr = getenv('REMOTE_ADDR', true) ?: getenv('REMOTE_ADDR');

//User Browser Info
$useragent = getenv('HTTP_USER_AGENT', true) ?: getenv('HTTP_USER_AGENT');

// Preset the post for display
$post['fname'] = isset($post['fname']) ? $post['fname'] : '';
$post['lname'] = isset($post['lname']) ? $post['lname'] : '';
$post['newmail'] = isset($post['newmail']) ? $post['newmail'] : '';
$post['altmail'] = isset($post['altmail']) ? $post['altmail'] : '';


// Create connection
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}
