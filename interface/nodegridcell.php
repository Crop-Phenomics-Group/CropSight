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