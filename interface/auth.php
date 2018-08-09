<?php
/*
Copyright 2018 The Zhou Laboratory, Earlham Institute, Norwich, UK

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
?>


<?php

$authorised = false;

if (isset($_GET['logout'])) {

    if ($_GET['logout'] == "true") {

        $_SESSION['email']    = '';
        $_SESSION['password'] = '';
        session_unset();
        session_destroy();
    } //$_GET['logout'] == "true"
} //isset($_GET['logout'])




if (isset($_POST['email']) && isset($_POST['password'])) {
    $_SESSION['email']    = strtolower($_POST['email']);
    $_SESSION['password'] = md5($_POST['password']);
} //isset($_POST['email']) && isset($_POST['password'])

if (isset($_SESSION['email']) && isset($_SESSION['password'])) {
    $res = $sql->query("SELECT * FROM users WHERE email='" . $sql->real_escape_string($_SESSION['email']) . "'");
    while ($row = $res->fetch_assoc()) {

        if ($row['password'] == '') {

            if (isset($_POST['setpassword']) && isset($_POST['password']) && isset($_POST['password2'])) {
                if ($_POST['password'] != '' && $_POST['password'] == $_POST['password2']) {
                    $sql->query("UPDATE " . $sqldatabase . ".users SET password='" . md5($_POST['password']) . "' WHERE email='" . $_SESSION['email'] . "';");
                    $authorised           = true;
                    $_SESSION['password'] = md5($_POST['password']);
                } //$_POST['password'] != '' && $_POST['password'] == $_POST['password2']
                else {
                    $passwordfailed = true;
                    include("setpassword.php");
                    exit(0);
                }
            } //isset($_POST['setpassword']) && isset($_POST['password']) && isset($_POST['password2'])
            else {
                include("setpassword.php");
                exit(0);
            }
        } //$row['password'] == ''
        else {


            if ($row['password'] == $_SESSION['password']) {

                $authorised = true;
            } //$row['password'] == $_SESSION['password']
        }
    } //$row = $res->fetch_assoc()

} //isset($_SESSION['email']) && isset($_SESSION['password'])

$admin = false;

if ($authorised == false) {
    include("login.php");
    exit(0);
} //$authorised == false
else {
    $adminquery = $sql->query("SELECT admin FROM " . $sqldatabase . ".users WHERE email='" . $_SESSION['email'] . "';");
    $adminres   = $adminquery->fetch_assoc();
    $admin      = $adminres['admin'];
}

?>