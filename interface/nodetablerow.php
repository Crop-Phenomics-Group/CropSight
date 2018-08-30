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
function node_table_row($node, $active, $sql, $sqldatabase)
 {?>

 <tr>

            <td class="nameblock">

<?php
        echo $node['hostname'];
?>

            </td>




<?php
        $imageurl = 'fetchimage.php?macaddress=' . $node['macaddress'];
?>

            <td class="imagecell"><a href="<?php
        echo $imageurl;
?>" target="_blank"><img src="<?php
        echo $imageurl;
?>" class="thumbnail" /></a></td>

            <?php

        $status = check_device_status($node['macaddress']);
?>

            <?php
        if ($status == 'ok') {
?>
               <td class="statuscell"><a class="statusok" href="index.php?node=<?php
            echo $node['macaddress'];
?>">

                <div class="statusboxtext">
                <?php
            echo overall_runtime($sql, $sqldatabase, $node['macaddress']);
?>
               <hr />
                OK
                </div>
                </a>
                </td>
                <?php
        } //$status == 'ok'
        elseif ($status == 'warning') {
?>
               <td class="statuscell"><a class="statuswarning" href="index.php?node=<?php
            echo $node['macaddress'];
?>">
                <div class="statusboxtext">
                <?php
            echo overall_runtime($sql, $sqldatabase, $node['macaddress']);
?><hr class="warninghr" />Warning</div></a>
                <?php
        } //$status == 'warning'
            elseif ($status == 'inactive') {
?>
               <td class="statuscell"><a class="statuscomplete" href="index.php?node=<?php
            echo $node['macaddress'];
?>"><div class="statusboxtext">Experiment Completed</div></a></td>
                <?php
        } //$status == 'inactive'
        else {
?>

                <td class="statuscell"><a class="statusoffline" href="index.php?node=<?php
            echo $node['macaddress'];
?>"><div class="statusboxtext">OFFLINE</div></a></td>
                <?php
        }
?>

<?php
        if ($active == TRUE) {
?>

            <td style="font-size: 0.8em;">
            <table class="internaltable">

            <tr><td>Uptime</td> <td>
            <?php
            if ($status == 'warning' or $status == 'ok') {
                echo get_uptime($node['uptime'], $node['lastupdate']);

            } //$status == 'warning' or $status == 'ok'
            else {
                echo '<span class="error">Offline</span>';
            }
?>

            </td> </tr>

            <tr><td>Last Data Capture</td><td><?php

            if ($node['lastupdate'] == 999999999999) {
                echo date("d.m.Y H:i:s", ((int) (time()) - rand(0, 3600)));
            } //$node['lastupdate'] == 999999999999
            else {
                echo $node['lastimage'];
            }


?></td></tr>

            </table>
            </td>

            <td class="borderedcell">

            <table class="internaltable">


                <tr style="font-size: 0.8em;"><td class="internaltablecell">IP Address:</td><td class="internaltablecell"><?php

            echo $node['ipaddress'];
?>

</td></tr>

<?php

            if ($node['ipaddress'] != $node['internalipaddress']) {
?>
               <tr style="font-size: 0.8em;"><td class="internaltablecell">(Internal):</td><td class="internaltablecell"><?php
                echo $node['internalipaddress'];
?></td></tr>
<?php
            } //$node['ipaddress'] != $node['internalipaddress']
?>
               <!--<tr><td class="internaltablecell">MAC Address:</td><td class="internaltablecell"><?php
            echo $node['macaddress'];
?></td></tr>-->


<tr><td colspan="2"><a href="http://<?php
            echo $node['ipaddress'];
?>" target="blank" class="linkButton">Device Interaction</a></td></tr>


                </table>

            </td>

<?php
        } //$active == TRUE
?>

            <?php


        $storage = $sql->query("SELECT * FROM " . $sqldatabase . ".pistorage WHERE macaddress='" . $node['macaddress'] . "';");

?>

            <td>
                    <table class="internaltable">
            <?php

        if ($active == TRUE) {

            while ($store = $storage->fetch_assoc()) {

?>

                <tr>


                <td class="internaltablecell">

                <?php
                if ($store['online'] == 1) {
                    echo "<span class=\"online\">&#10004;</span>";
                } //$store['online'] == 1
                else {
                    echo "<span class=\"offline\">&#10008;</span>";
                }
?>

                </td>

                    <?php
                $mp = $store['mountpoint'];

                if ($mp == '/') {
                    $mp = '<span class="it">[onboard]</span>';
                } //$mp == '/'
?>

                                        <td class="internaltablecell"><?php
                echo $mp;
?></td>

                                        <td class="internaltablecell"><div class="diskspacebar"><div class="floatingtext"><?php
                echo number_format((float) (((int) $store['free'] / (int) $store['total']) * 100), 2, '.', '');
?>% free - (<?php
                echo number_format((float) ((int) $store['free'] / 1024), 2, '.', '');
?>GB/<?php
                echo number_format((float) ((int) $store['total'] / 1024), 2, '.', '');
?>GB)</div> <div class="usedspace" style="width: <?php
                echo (300 - (((int) $store['free'] / (int) $store['total']) * 300));
?>px;"></div> </div></td>


                    </tr>

<?php
            } //$store = $storage->fetch_assoc()
?>
                   <?php
        } //$active == TRUE
        else {
?>
    <tr><td class="shrink">Last Data Capture</td><td class="expand" ><?php
            echo $node['lastimage'];
?></td></tr>
    <?php
        }
?>
<tr>
<td colspan="3"><a href="index.php?page=datastore&macaddress=<?php
        echo $node['macaddress'];
?>" class="linkButton">Crop Growth Image Series</a></td>
</tr>
        <tr><td colspan="3"><a href="downloadsensordata.php?macaddress=<?php
        echo $node['macaddress'];
?>" target="blank" class="linkButton">Download Sensor Data</a></td></tr>
                        </table>

                </td>






</tr>

<?php } ?>