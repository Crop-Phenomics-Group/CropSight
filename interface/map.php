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
function renderMap($sql, $sqldatabase, $projectid)
{

    $GM_API_KEY = 'AIzaSyBflNrogYLabc49tqvFhd33r9DeQhhc7gU';

    $p_list = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $projectid . "';");

    $mapdata = '';
    $lat     = '';
    $long    = '';

    if ($proj = $p_list->fetch_assoc()) {
        $mapdata = $proj['map'];
        $lat     = $proj['latitude'];
        $long    = $proj['longitude'];
    } //$proj = $p_list->fetch_assoc()
    else {
        return;
    }

    if ($mapdata == '') {

?>

<table>
<tr>
<td valign="middle">
<iframe width="300" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=<?php
        echo $GM_API_KEY;
?>&q=<?php
        echo $long;
?>,<?php
        echo $lat;
?>" allowfullscreen>
    </iframe>
</td>
<td style="width: 99%;" valign="middle">
</td>
</tr>
</table>

<?php

        return;
    } //$mapdata == ''

?>

<table>
<tr>
<td valign="middle">
<iframe width="300" height="300" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=<?php
    echo $GM_API_KEY;
?>&q=<?php
    echo $long;
?>,<?php
    echo $lat;
?>" allowfullscreen>
    </iframe>
</td>
<td style="width: 99%;" valign="middle">
<?php
    echo str_replace('id="field"', 'id="field' . $projectid . '"', str_replace('svg id="map"', 'svg id="map' . $projectid . '"', $mapdata));
?>
</td>
</tr>
</table>

<script>

    var points = [];

    <?php

    $pointlist = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE projectid='" . $projectid . "';");

    while ($point = $pointlist->fetch_assoc()) {
        $pos = explode(",", $point['position']);

        $id  = $point['hostname'];
        $ip  = $point['ipaddress'];
        $mac = $point['macaddress'];

        $status = check_device_status($point['macaddress']);

        if ($status == 'ok') {
            $status = 0;
        } //$status == 'ok'
        elseif ($status == 'warning') {
            $status = 1;
        } //$status == 'warning'
            elseif ($status == 'inactive') {
            $status = 3;
        } //$status == 'inactive'
        else {
            $status = 2;
        }


        echo 'points.push([' . (6 * $pos[1]) . ', ' . (1.65 * $pos[0]) . ', ' . $status . ', "' . $id . '", "' . $ip . '", "' . $mac . '"]);';
        echo "\n";
    } //$point = $pointlist->fetch_assoc()

?>

    var map = d3.select("#map<?php
    echo $projectid;
?>");


    var point = map.selectAll("#field<?php
    echo $projectid;
?>")
        .data(points)
        .enter()
        .append("g")
        .attr("transform", function(d) {return "translate(" + (d[0]) + "," + (d[1]) + ")"});



    point.append("line")
    .attr("stroke-width",0.05)
    .attr("stroke","red")
        .attr("x1", 0)
        .attr("y1", 0)
        .attr("x2", 5)
        .attr("y2", 0);

    point.append("line")
    .attr("stroke-width",0.05)
    .attr("stroke","red")
        .attr("x1", 5)
        .attr("y1", 0)
        .attr("x2", 5)
        .attr("y2", 1.2);

        point.append("line")
    .attr("stroke-width",0.05)
    .attr("stroke","red")
        .attr("x1", 5)
        .attr("y1", 1.2)
        .attr("x2", 0)
        .attr("y2", 1.2);

        point.append("line")
    .attr("stroke-width",0.05)
    .attr("stroke","red")
        .attr("x1", 0)
        .attr("y1", 1.2)
        .attr("x2", 0)
        .attr("y2", 0);

    point.append("a")
        .attr("xlink:href", function(d) {return "index.php?node=" + d[5];})
        .append("svg:circle")
        .attr("cx", 0.3)
        .attr("cy", 0.3)
        .attr("r", 0.5)
        .attr("class", function(d){ if (d[2] == 0){return "mapstatusok";} else if (d[2] == 1){return "mapstatuswarning";} else if (d[2] == 3) {return "mapstatuscomplete";} else {return "mapstatusoffline";} });

       $('#<?php
    echo $projectid;
?> circle').tipsy({
            gravity: 'w',
            html: true,
            title: function() {
              var d = this.__data__;
              return d[3] + '<br />' + d[4];
            }
          });

</script>

<?php
}
?>