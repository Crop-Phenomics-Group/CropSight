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


<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
    <title>CropQuant Network Monitor</title>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="stylesheet" type="text/css" href="print.css" media="print">
<link rel="stylesheet" type="text/css" href="assets/fixedHeader.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="assets/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="assets/tipsy.css" />

<link rel="icon" type="image/png" href="images/favicon.png">

<script type="text/javascript" src="assets/jquery-1.12.0.min.js"></script>
<script type="text/javascript" src="assets/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/dataTables.fixedHeader.min.js"></script>
<script type="text/javascript" src="assets/d3.v3.min.js" charset="utf-8"></script>
<script type="text/javascript" src="assets/jquery.tipsy.js"></script>

</head>
<body>
<div id="container">



<div id="header" style="text-align: right">

<div style="float: left;">
<a href="index.php"><img align="top" src="images/john-innes-logo-dark.png" alt="JIC Logo" class="homelink" style="height: 100px"/></a>
<a href="index.php"><img align="top" src="images/eilogo.png" alt="Earlham Institute Logo" class="homelink" style="height: 100px"/></a>
</div>

<table align="right"><tr>
<td style="text-align: right"><img src="images/logo.png" style="height: 100px; display:inline-block;"  /></td>
<td><h1 style="display: inline;">CropMonitor</h1></td>
</tr>
</table>


<div style="clear: both;"></div>
</div>





    <?php
include("nav.php");
?>

<div id="content">
