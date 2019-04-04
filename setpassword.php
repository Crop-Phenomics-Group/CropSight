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
include("header.php");
?>


<?php

if (isset($passwordfailed)) {
?>
<div class="passworderror">Inputted passwords did not match</div>
<?php
} //isset($passwordfailed)
?>
<div id="loginform">

<p>This is your first login, set your password using the form below:</p>


<form action="index.php" method="post">

<table id="formtable">

<input type="hidden" name="setpassword" value="true" />

<tr><td>Password:</td><td><input type="password" name="password" /></td></tr>

    <tr><td>Repeat Password:</td><td><input type="password" name="password2" /></td></tr>

<tr><td colspan="2"><input type="submit" value="Set Password" /></td></tr>

    </table>
</form>
</div>
<?php
include('footer.php');
?>