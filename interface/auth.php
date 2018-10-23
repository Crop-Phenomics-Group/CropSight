<?php
/*
Copyright 2018 The Zhou Laboratory, Earlham Institute, Norwich, UK

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this
   list of conditions and the following disclaimer in the documentation and/or
   other materials provided with the distribution.

3. Neither the name of the copyright holder nor the names of its contributors may
   be used to endorse or promote products derived from this software without
   specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
OF SUCH DAMAGE.
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