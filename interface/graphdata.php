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

$graphdata = 'cputemp';
if (isset($_GET['graphdata'])) {
    $graphdata = $sql->real_escape_string($_GET['graphdata']);
} //isset($_GET['graphdata'])
else {
    $graphdata = 'temp';
}

$axislabel = $graphdata;
$graphunit = '';

$labelq = $sql->query("SELECT * FROM " . $sqldatabase . ".pisensor WHERE sensorid='" . $graphdata . "'");
if ($label = $labelq->fetch_assoc()) {
    $axislabel = $label['description'] . ' (' . $label['unit'] . ')';
    $graphunit = $label['unit'];
} //$label = $labelq->fetch_assoc()

?>
<h2>CQ Sensor Data</h2>
<div id="graphbox">
<div id="graphnavbar">

<?php

$sensorlist = $sql->query("SELECT * FROM pisensor");

while ($sensor = $sensorlist->fetch_assoc()) {

    if ($sensor['sensorid'] != 'cputemp') {
        $buttonclass = 'navButtonGrey';

        if ($sensor['sensorid'] == $graphdata) {
            $buttonclass = 'navButton';
        } //$sensor['sensorid'] == $graphdata

?>
   <a href="index.php?node=<?php
        echo $_GET['node'];
?>&graphdata=<?php
        echo $sensor['sensorid'];
?>" class="<?php
        echo $buttonclass;
?>"><?php
        echo $sensor['name'];
?></a>
    <?php
    } //$sensor['sensorid'] != 'cputemp'
} //$sensor = $sensorlist->fetch_assoc()

?>




</div>




</div>

<div id="varprint">
</div>

<script>

Date.createFromMysql = function(mysql_string)
{
   var t, result = null;
   if( typeof mysql_string === 'string' )
   {
      t = mysql_string.split(/[- :]/);
      result = new Date(t[0], t[1] - 1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
   }
   return result;
}

var data = [];
var missingdata = [];

<?php
$datapoints = $sql->query("SELECT * FROM " . $sqldatabase . ".sensorreading WHERE macaddress='" . $nodeselection . "' AND sensor='" . $graphdata . "'");

$last = '';

while ($datapoint = $datapoints->fetch_assoc()) {

    if ($last != '') {
        $curdate = strtotime($datapoint['timestamp']);
        $diff    = $curdate - $last;
        if ($diff > 7200) {

?>

                missingdata.push([data[data.length-1][0], "NaN"]);
                missingdata.push([data[data.length-1][0], data[data.length-1][1]]);
                missingdata.push([Date.createFromMysql("<?php
            echo $datapoint['timestamp'];
?>"), <?php
            echo $datapoint['reading'];
?>]);


                <?php



            echo 'data.push([Date.createFromMysql("' . $datapoint['timestamp'] . '"),"NaN"]);';
            echo "\n";


        } //$diff > 7200
    } //$last != ''

    echo 'data.push([Date.createFromMysql("' . $datapoint['timestamp'] . '"),' . $datapoint['reading'] . ']);';
    echo "\n";

    $last = strtotime($datapoint['timestamp']);
    ;

} //$datapoint = $datapoints->fetch_assoc()
?>


var margin = {top: 20, right: 20, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

width = d3.select("#graphbox").node().getBoundingClientRect().width - margin.left - margin.right -20;

var formatDate = d3.time.format("%d-%b-%y");

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);




var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

xAxis = xAxis.innerTickSize(-height)
    .outerTickSize(0)
    .tickPadding(10);

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left")
    .innerTickSize(-width)
    .outerTickSize(0)
    .tickPadding(10);



var line = d3.svg.line()
    .interpolate("cardinal")
    .x(function(d) { return x(d[0]); })
    .y(function(d) { return y(d[1]); });

line.defined(function(d) { return !isNaN(d[1]); });


var linetwo = d3.svg.line()
    .interpolate("cardinal")
    .x(function(d) { return x(d[0]); })
    .y(function(d) { return y(d[1]); });

linetwo.defined(function(d) { return !isNaN(d[1]); });

var bisectDate = d3.bisector(function(d) { return d[0]; }).left

var graphsvg = d3.select("#graphbox").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

  x.domain(d3.extent(data, function(d) { return d[0]; }));
  y.domain([0,d3.max(data, function(d) { return d[1]+1; })]);

  graphsvg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis)
    .append("text")
      .style("text-anchor", "end")
      .attr("x", width-20)
      .attr("y", -6)
      .text("Date/Time");


  graphsvg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("<?php
echo $axislabel;
?>");


      graphsvg.append("path")
      .datum(missingdata)
      .attr("class", "missingline")
      .style("stroke-dasharray", ("3, 3"))
      .attr("d", linetwo);

  graphsvg.append("path")
      .datum(data)
      .attr("class", "line")
      .attr("d", line);



    var focus = graphsvg.append("g")
      .attr("class", "focus")
      .style("display", "none");

  focus.append("circle")
      .attr("r", 4.5);

  focus.append("rect")
      .attr("width", 60)
      .attr("height", 20)
      .attr("x", 7)
      .attr("y", -10);

  focus.append("text")
      .attr("x", 9)
      .attr("dy", ".35em");


  graphsvg.append("rect")
      .attr("class", "overlay")
      .attr("width", width)
      .attr("height", height)
      .on("mouseover", function() { focus.style("display", null); })
      .on("mouseout", function() { focus.style("display", "none"); })
      .on("mousemove", mousemove);

  function mousemove() {

  var format = d3.time.format("%d/%m/%y - %H:%M");

    var x0 = x.invert(d3.mouse(this)[0]),
        i = bisectDate(data, x0, 1),
        d0 = data[i - 1],
        d1 = data[i],
        d = x0 - d0[0] > d1[0] - x0 ? d1 : d0;
    focus.attr("transform", "translate(" + x(d[0]) + "," + y(d[1]) + ")");
    focus.select("text").text(d[1] + " <?php
echo $graphunit;
?>");
  }

function type(d) {
  d[0] = formatDate.parse(d[0]);
  d[1] = +d[1];
  return d;
}

</script>