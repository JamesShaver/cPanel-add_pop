<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Add POP Mail</title>
        <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.5.2/flatly/bootstrap.min.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <?php
        require 'config.php';

        // if form has been submitted
        if (isset($post['submit'])) {
            $newmail_str = "";  // $newmail_str is the string we'll append entries to
            if (!$post['fname'] || !$post['lname'] || !$post['password1'] || !$post['password2'] | !$post['altmail'] | !$post['newmail']) {
                die('You did not fill in a required field.');
            }

            // check passwords match
            if ($post['password1'] != $post['password2']) {
                die('Passwords did not match.');
            }
            $buildurl = http_build_query(
                    array(
                        'email' => $post['newmail'],
                        'password' => $post['password1'],
                        'quota' => $quota
                    )
            );
            $query = "https://" . $cphost . ":2083/execute/Email/add_pop?" . $buildurl;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $header[0] = "Authorization: cpanel $cpuser:$cptoken";
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt($curl, CURLOPT_URL, $query);

            $result = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($http_status != 200) {
                echo "[!] Error: " . $http_status . " returned<br>\n";
            } else {
                $json = json_decode($result);

                if (!$json->{'status'} == 1) {
                    echo print_r($json->{'errors'}[0], TRUE) . "<br><a href=javascript:history.go(-1)>Back</a><br>\n";
                } else {
                    echo $json->data . " has been created. <br><br>You may now use <b>mail.$domain</b> as your POP3 setting in your mail client or access <b>WebMail</b> by selecting <a href=http://$domain/webmail>Here</a>.<br>\n";

                    // Add info to database
                    // set parameters
                    $fname = $post['fname'];
                    $lname = $post['lname'];
                    $email = $post['newmail'];
                    $altmail = $post['altmail'];

                    $sql = "INSERT INTO " . $dbtable . " (firstname, lastname, username, alternatemail) VALUES (?, ?, ?, ?)";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("ssss", $fname, $lname, $email, $altmail);
                        $stmt->execute();
                        printf("rows inserted: %d\n", $conn->affected_rows);
                    } else {
                        $error = $conn->errno . ' ' . $conn->error;
                        echo $error;
                    }
                    $stmt->close();
                    $conn->close();

                    //
                    // Send Email to Admin
                    //
                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                    $headers .= 'From: ' . $sender . "\r\n";

                    $body = "<strong>Input from submitted form:</strong><br>\n";

                    // loop through form input
                    foreach ($post as $key => $value) {
                        $body .= $key . ' = ' . $value . "<br>\n";
                    }

                    // additional (client) information
                    $body .= "\n\nAdditional Client Information:\n\n"
                            . 'Quota = ' . $quota . "<br>\n"
                            . 'Date = ' . date('Y-m-d H:i') . "<br>\n"
                            . 'Browser = ' . $useragent . "<br>\n"
                            . 'IP Address = ' . $remoteAddr . "<br>\n"
                            . 'Hostname = ' . gethostbyaddr($remoteAddr);

                    // send email
                    mail($recipient, $subject, $body, $headers);
                }
            }
        }
        ?>

        <div class="container">
            <div class="col-10">
                <form name="emailform" method="post" action="index.php">
                    <div class="card text-center mt-5">
                        <div class="card-header">
                            <h3>Free Mail</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="firstname" class="col-sm-2 col-form-label">First Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="fname" placeholder="First Name" value="<?= $post['fname']; ?>" required />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="lastname" class="col-sm-2 col-form-label">Last Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="lname" placeholder="Last Name" value="<?= $post['lname']; ?>" required />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">New Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="newmail" placeholder="user@<?= $domain; ?>" value="<?= $post['newmail']; ?>" required />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="altemail" class="col-sm-2 col-form-label">Alternate Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" name="altmail" placeholder="yourmail@othersite.com" value="<?= $post['altmail']; ?>" required />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password1" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password1" required />
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password2" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" name="password2" required />
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <input name="submit" type="submit" class="btn btn-primary" value="Submit" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
</html>