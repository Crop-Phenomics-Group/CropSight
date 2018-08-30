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
function node_grid_cell($gridnode, $gridwidth, $sql, $sqldatabase,  $columnindex)
 {


             $columnindex = $columnindex + 1;
            if ($columnindex > $gridwidth) {

                $columnindex = 0;
            } //$columnindex > $gridwidth


            $status = check_device_status($gridnode['macaddress']);

?>
<div class="gridcell">

<h3><?php
            echo $gridnode['hostname'];
?></h3>

<div style="clear:both;"></div>

<a href="index.php?node=<?php
            echo $gridnode['macaddress'];
?>" style="text-decoration: none;">
<img src="fetchimage.php?macaddress=<?php
            echo $gridnode['macaddress'];
?>" alt="<?php
            echo $gridnode['hostname'];
?> Sample Image" class="thumbnailgrid" />
</a>
<br />



<?php

            $timeQuery      = $sql->query("SELECT timestamp FROM " . $sqldatabase . ".sensorreading WHERE macaddress='" . $gridnode['macaddress'] . "' ORDER BY timestamp ASC;");
            $overallRuntime = 0;

            if ($timerow = $timeQuery->fetch_assoc()) {
                $overallRuntime = (int) (time() - strtotime($timerow['timestamp']));

            } //$timerow = $timeQuery->fetch_assoc()

?>



<?php

            if ($status == 'ok') {
?>
               <a class="statusokgrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>"><?php


                echo overall_runtime($sql, $sqldatabase, $gridnode['macaddress']);


?></a>
            <?php
            } //$status == 'ok'
            elseif ($status == 'warning') {
?>
               <a class="statuswarninggrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>"><?php

                echo overall_runtime($sql, $sqldatabase, $gridnode['macaddress']);
?></a>
            <?php
            } //$status == 'warning'
                elseif ($status == 'inactive') {
?>
               <a class="statuscompletegrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>">Exp. Completed<br/><?php
                echo $gridnode['lastupdate'];
?></a>

            <?php
            } //$status == 'inactive'
            else {
?>

        <a class="statusofflinegrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>">OFFLINE<br/><?php
                echo $gridnode['lastupdate'];
?></a>

<?php
            }
?>

</div>

<?php

return $columnindex;
} ?>