<?php //include("../admin/dbinfo.inc.php"); ?>
<!DOCTYPE html>
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<title>NZ Quake Maps</title>
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8="  crossorigin="anonymous"></script>
 
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<!-- tablesorter plugin http://mottie.github.io/tablesorter/docs/example-widget-filter.html -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/jquery.tablesorter.min.js"></script>
<!-- tablesorter widget file - loaded after the plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/jquery.tablesorter.widgets.min.js"></script>
<link rel="stylesheet" href="tablesorter.boottheme.css">
<!-- pager plugin -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/addons/pager/jquery.tablesorter.pager.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/addons/pager/jquery.tablesorter.pager.min.js"></script>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>




<script>
$(function () {

  $.tablesorter.themes.bootstrap = {
    table        : 'table table-bordered table-hover ',
    caption      : 'caption',
    header       : 'active', // give the header a gradient background (theme.bootstrap_2.css)
    iconSortNone : 'bootstrap-icon-unsorted', // class name added to icon when column is not sorted
    iconSortAsc  : 'icon-chevron-up glyphicon glyphicon-chevron-up', // class name added to icon when column has ascending sort
    iconSortDesc : 'icon-chevron-down glyphicon glyphicon-chevron-down', // class name added to icon when column has descending sort
  };


  
  // t2 = table with sort + filter
  
   $( "table.t2" ).before( '<button type="button" class="reset2 btn btn-primary" data-column="0" data-filter=""><i class="icon-white icon-refresh glyphicon glyphicon-refresh"></i> Reset filters</button><small> hover on the gray bar under the header to show column filters</small>' );
  
  // call the tablesorter plugin and apply the uitheme widget
  $("table.t2").tablesorter({
    theme : "bootstrap",
	dateFormat : "ddmmyyyy",
    widthFixed: false,
    headerTemplate : '{content} {icon}', 
		widgets : [ "uitheme", "filter"],    
    widgetOptions : {
      filter_columnFilters: true,
      filter_placeholder: { search : 'Search...' },
      filter_saveFilters : true,
      filter_reset: '.reset2',
      filter_hideFilters : true,
    }
  });
  
  
 
 // t3 = table with sort only 
  $("table.t3").tablesorter({
    theme : "bootstrap",
    widthFixed: true,
    headerTemplate : '{content} {icon}',     
		widgets : [ "uitheme"],
  });
  
	
	$("#setbounds").change(function () {
			var setb = $(this).val();
			if ($(this).is(':checked')) {
				$("#boundsv").val($("#bounds").text());
			} else {
				$("#boundsv").val('');
			}
	});
  
});
</script> 
 
      

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDgC4NjamllB6pL-YvIhrdei5UaJLQ4HuM"></script>      
 <?php
 
 function url_exists($url) {
    $file_headers = @get_headers($url);
    if($file_headers[0] == 'HTTP/1.1 404 Not Found') {
        return FALSE;
    }
    else {
        return TRUE;
    }

}

 
 
 
 

if (isset($_POST['submit'])) {
	//$days = $_POST['days'];
	$start=gmdate("Y-m-d\TH:i:s",strtotime($_POST['start']));
	$start2=$_POST['start'];
	$end=gmdate("Y-m-d\TH:i:s",strtotime($_POST['end']));
	$end2=$_POST['end'];
	$magmin=$_POST['magmin'];
	$magmax=$_POST['magmax'];
	$depthLower=$_POST['depthLower'];
	$depthUpper=$_POST['depthUpper'];
	$boundsv = $_POST['boundsv'];
	$setbounds = $_POST['setbounds'];
}  else {
	//$days = 0;
	$start=gmdate("Y-m-d\TH:i:s",strtotime("-24 hours"));
	$start2=date("d-m-Y H:i:s",strtotime("-24 hours"));
	$end=gmdate("Y-m-d\TH:i:s");
	$end2=date("d-m-Y H:i:s");
	$magmin=1;
	$magmax=9;
	$depthLower=0;
	$depthUpper=600;
	$boundsv = "";
	$setbounds = 0;
}
	  
      


/*

CSV format

http://quakesearch.geonet.org.nz/csv?bbox=163.60840,-49.18170,182.98828,-32.28713&minmag=3&maxmag=8&mindepth=2&maxdepth=4&startdate=2016-11-13T9:00:00&enddate=2016-11-20T10:00:00
"http://quakesearch.geonet.org.nz/csv?startdate=".$start."&enddate=".$end."&minmag=".3."&maxmag=".8."&mindepth=".2."&maxdepth=".4 ;
*/

$url="http://quakesearch.geonet.org.nz/csv?startdate=".$start."&enddate=".$end."&minmag=".$magmin."&maxmag=".$magmax."&mindepth=".$depthLower."&maxdepth=".$depthUpper;

if (($setbounds==1)&&($boundsv!="")) {
	$bounds = str_replace(array('(',')',' '),'',$boundsv);
	$llb = explode(',',$bounds);	
	$newbounds = $llb[1].','.$llb[0].','.$llb[3].','.$llb[2];
	echo $newbounds;
	
	$url.="&bbox=".$newbounds;
}


          $row = 1;
    if (($handle = fopen($url, "r")) !== FALSE) {
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $lines[]=$data;
      }
      fclose($handle);
    }
//    echo"<pre>";
//    print_r($lines);
//	echo"</pre>";
//  die();

$date1 = new DateTime($start);
$date2 = new DateTime($end);
$interval = $date1->diff($date2);
//echo "difference " . $interval->y . " years, " . $interval->m." months, ".$interval->d." days "; 
// shows the total amount of days (not divided into years, months and days like above)
//echo "difference " . $interval->days . " days ";


$stats['start']=$start;
$stats['end']=$end;
$stats['hours']=$interval->y." years, ".$interval->m." months, ".$interval->d." days, ".$interval->h." hours";
$stats['count']=count($lines)-1;
$stats['maxmag']=0;
$stats['minmag']=10;
$stats['avgmag']=0;
$stats['r1']=0;
$stats['r2']=0;
$stats['r3']=0;
$stats['r4']=0;
$stats['r5']=0;
$stats['r6']=0;
$stats['r7']=0;
$stats['r8']=0;
$stats['maxd']=0;
$stats['mind']=500;
$stats['avgd']=0;
$stats['energyt']=0;

    
foreach($lines as $field)  {
		
		if ($field[0]!="publicid") { // header check
				$ref = $field[0];
				$depth =$field[7];
        $mag =$field[6];
        $utc = $field[2];
        $status = $field[12].' '.$field[11];

        $lat = $field[5];
        $lng = $field[4];
            

            
            if ($depth<15) {
            	$d2 = "#ff66ff";
               $im = "pink";
            } else if ($depth<40) {
            	$d2 = "#ff0000";
               $im = "red";
            } else if ($depth<70) {
            	$d2 = "#ff9900";
               $im = "orange";
            } else if ($depth<100) {
            	$d2 = "#ffff00";
               $im = "yellow";
            } else if ($depth<150) {
            	$d2 = "#00cc00";
               $im = "green";
            } else if ($depth<200) {
            	$d2 = "#00ffff";
               $im = "cyan";
            } else if ($depth<300) {
            	$d2 = "#0066ff";
               $im = "blue";
            } else {
            	$d2 = "#800080";
               $im = "purple";
            }
						
						if ($mag<2) {
            	$m2 = "gray";
            } else if ($mag<3) {
            	$m2 = "purple";
            } else if ($mag<4) {
            	$m2 = "blue";
            } else if ($mag<5) {
            	$m2 = "green";
            } else if ($mag<6) {
            	$m2 = "gold";
            } else if ($mag<7) {
            	$m2 = "orange";
            } else {
            	$m2 = "red";
            }
						$quakes[$ref]['m3']=$m2;
            
            
            $link = "http://www.geonet.org.nz/quakes/region/newzealand/".$ref;


            $quakes[$ref]['name']=$ref;
            $quakes[$ref]['m']=$mag;
            $quakes[$ref]['deep']=$depth;
            $quakes[$ref]['dt']=$utc;
            //$quakes[$ref]['add']=$add;
            
            $energy = pow(10,(1.5*$mag));
            $radius = 5*sqrt($energy);
            //$quakes[$ref]['m2']=number_format((($mag*100)*($mag*3)),0,'','');
            $quakes[$ref]['en']=number_format($energy,0,'.','');
            $quakes[$ref]['m2']=number_format($radius,0,'.','');
            $quakes[$ref]['d2']=$d2; 
            $quakes[$ref]['lat']=$lat;
            $quakes[$ref]['lng']=$lng;
				
						$quakes[$ref]['desc']="<h2>M ".number_format($mag,2)." - ".date("d-m-Y g:i a",strtotime($utc." GMT")).'</h2>';  
						$quakes[$ref]['desc'].="<b>Ref:</b> $ref<br />";
            $quakes[$ref]['desc'].="<b>Mag:</b> $mag<br />";
            $quakes[$ref]['desc'].="<b>Depth:</b> $depth<br />";
            $quakes[$ref]['desc'].="<b>Lat/Long:</b> $lat,$lng<br />"; 
            //$quakes[$ref]['desc'].="<b>Location:</b> $add<br />";
           // $quakes[$ref]['desc'].="<b>Stations:</b> $stations<br />";
           	$quakes[$ref]['desc'].="<b>Status:</b> $status<br />";
            
						$quakes[$ref]['desc'].='<b>Geonet Link:</b> <a href="'.$link.'" title="Ref:'.$ref.'" target="_blank">Link</a><br />';
            

            $quakes[$ref]['status']=$status;
            $quakes[$ref]['link']=$link;
            $quakes[$ref]['im']=$im;
            $quakes[$ref]['title']="M $mag | ".$quakes[$ref]['dt']." | $depth km";
						
						
						
						
						if ($mag>$stats['maxmag']) { $stats['maxmag']=$mag; }
						if ($mag<$stats['minmag']) { $stats['minmag']=$mag; }
            $stats['avgmag']+=$mag;
            if ($mag<2) { $stats['r1']++; }
            else if ($mag<3) { $stats['r2']++; }
            else if ($mag<4) { $stats['r3']++; }
            else if ($mag<5) { $stats['r4']++; }
            else if ($mag<6) { $stats['r5']++; }
            else if ($mag<7) { $stats['r6']++; }
            else if ($mag<8) { $stats['r7']++; }
            else  { $stats['r8']++; }
            if ($depth>$stats['maxd']) { $stats['maxd']=$depth; }
						if ($depth<$stats['mind']) { $stats['mind']=$depth; }
            $stats['avgd']+=$depth;
						$stats['energyt']+=$energy;
            
			
		}	// end header check
		 

  }  // end each

	$stats['avgmag']=$stats['avgmag']/$stats['count'];
	$stats['avgd']=$stats['avgd']/$stats['count'];
  
  krsort($quakes); 



  
?>      
      
 <script type="text/javascript">
 
 // Create an object containing LatLng, population.
var citymap = {};
 
 <?php  foreach ($quakes as $k=>$q) {   ?>
  	citymap['<?=$k?>'] = {
     	center: new google.maps.LatLng(<?=$q['lat']?>,<?=$q['lng']?>),
     	mag: <?=$q['m2']?>,
      deep: '<?=$q['d2']?>',
      contentStr: '<?=$q['desc']?>',
      title: '<?=$q['title']?>',
      im: '<?=$q['im']?>',
      ref: '<?=$k?>',
      llat: '<?=$q['lat']?>',
      llng: '<?=$q['lng']?>',
			mmm: <?=$q['m']?>,
   };
  
  <?php } ?>
 

function initialize() {
  var nz = new google.maps.LatLng(-40.497092,173.254396);
  var myOptions = {
    zoom: 6,
    center: nz,
    mapTypeId: google.maps.MapTypeId.HYBRID
  }

  var map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

//  var ctaLayer = new google.maps.KmlLayer('<?php echo "$url"; ?>');
//  ctaLayer.setMap(map);

 
var marker; 
var mmm = {};

   for (var city in citymap) {
    // Construct the circle for each value in citymap. 
    var populationOptions = {
      strokeColor: citymap[city].deep,
      strokeOpacity: 0.8,
      strokeWeight: 1,
      fillColor: citymap[city].deep,
      fillOpacity: 0.05,
      map: map,
      center: citymap[city].center,
      radius: citymap[city].mag,
      title: citymap[city].title,
      
    };
    marker = new google.maps.Circle(populationOptions);
    
 

var marker2 = new google.maps.Marker({
    position: citymap[city].center,
    map: map,
    title: citymap[city].title,
    icon: {
      path: google.maps.SymbolPath.CIRCLE,
      scale: (citymap[city].mmm*1.5),
			strokeColor: citymap[city].deep,
    },
    html: citymap[city].contentStr
});

mmm[citymap[city].ref]={mark:marker2};

var infowindow = new google.maps.InfoWindow({ });

google.maps.event.addListener(marker2, 'click', function() {
infowindow.setContent(this.html);
infowindow.open(map,this);
});


 
  }
  
  <?php  foreach ($quakes as $k=>$q) {   ?>
  google.maps.event.addDomListener(document.getElementById("<?=$k?>"), "click", function(ev) { 

	google.maps.event.trigger(mmm['<?=$k?>'].mark, "click");
    var center= new google.maps.LatLng(<?=$q['lat']?>,<?=$q['lng']?>);
    map.setCenter(center);
    
  });
 <?php } ?>
 
 
 map.addListener('bounds_changed', function() {
    var bounds = map.getBounds();
		//$('#bounds').html(bounds);
		document.getElementById("bounds").innerHTML = bounds;
 });
 
 

 
}


</script>     
 
<script type="text/javascript">

      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);
			
			google.charts.setOnLoadCallback(drawChart2);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Topping');
        data.addColumn('number', 'Slices');
        data.addRows([
          ['1s = <?=$stats['r1']?>', <?=$stats['r1']?>],
          ['2s = <?=$stats['r2']?>', <?=$stats['r2']?>],
          ['3s = <?=$stats['r3']?>', <?=$stats['r3']?>],
          ['4s = <?=$stats['r4']?>', <?=$stats['r4']?>],
          ['5s = <?=$stats['r5']?>', <?=$stats['r5']?>],
					['6s = <?=$stats['r6']?>', <?=$stats['r6']?>],
					['7s = <?=$stats['r7']?>', <?=$stats['r7']?>],
					['8s = <?=$stats['r8']?>', <?=$stats['r8']?>]
        ]);

        // Set chart options
        var options = {'title':'How Much Pizza I Ate Last Night',
                       'width':'100%',
                       'height':300,
											 'is3D':true
											 };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
			
			// chart 2
			
			function drawChart2() {

      var data = google.visualization.arrayToDataTable([
         ['Date', 'Mag', { role: 'style' }, {type: 'string', role: 'tooltip', 'p': {'html': true}}],
				 
				 <?php  foreach ($quakes as $k=>$q) {   ?>				 
         [
				  new Date(<?=date("Y",strtotime($q['dt']." GMT"))?>,<?=date("n",strtotime($q['dt']." GMT"))-1?>,<?=date("j,H,i",strtotime($q['dt']." GMT"))?>    ), 
				 	<?=$q['m']?>, 
				 	'<?=$q['d2']?>',
					'<?=date("D d-m-Y g:ia",strtotime($q['dt']." GMT"))?><br />M <?=number_format($q['m'],3)?><br />depth: <?=number_format($q['deep'],3)?><br />ID: <?=$k?> '
				 ],            
					<?php } ?>
					
     	 ]);
				
        // Set chart options
        var options = {'title':'<?=$stats['hours']?> - mag coloured by depth',
											 'width':'100%',
                       'height':400,
											 'legend': 'none',
											 'tooltip': {isHtml: true},
											 'vAxis': { ticks: [1,2,3,4,5,6,7,8,9] }

											 };

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
        chart.draw(data, options);
      }
			
			
    </script>
 
 
 
 
      

<style type="text/css">
<!-- 
body {
	font-family:tahoma;
    font-size:13px;
}
 

#map_canvas {
	width:100%;
    height:600px;
}

#qtable tbody tr:hover {
	background-color:#ffffcc;
}

.rr {
text-align:right;
}
.lr {
text-align:left;
}
.sm {
font-size:8px;
}

.tablesorter-bootstrap .tablesorter-header-inner {
    padding: 0 18px 0 4px;
		font: bold 12px/16px Arial, Sans-serif;
}
#geonet {
    background: url('http://static.geonet.org.nz/geonet-2.0.2/images/logos.png') 0 -249px;
    width: 137px;
    height: 53px;
    display: block;
}
-->
</style>

</head>
<body onload="initialize()">
<?php include('menu.inc.php'); ?>
<div class="container-fluid">
<h1>Madness</h1>
<div class="thumbnail">
<form action="" method="post" id="sform" >

	Start Date <input type="text" name="start" value="<?php echo "$start2"; ?>" class="datepicker" size="20"/>
	End Date <input type="text" name="end" value="<?php echo "$end2"; ?>" class="datepicker" size="20"/>
	<!-- <u>OR</u> Number of days before now <input type="text" name="days" value="<?php echo "$days"; ?>" size="4" /><br /> -->
	Magnitude Between Min  <input type="text" name="magmin" value="<?php echo "$magmin"; ?>" size="4" />
	and Max  <input type="text" name="magmax" value="<?php echo "$magmax"; ?>" size="4" /><span class="sm" style="color:#aaa"> (value between 1 and 9)</span>
	<input  type="submit" name="submit" value="Search" class="btn btn-success pull-right"/>
	<br />Depth Between <input type="text" name="depthLower" value="<?php echo "$depthLower"; ?>" size="4" />
	and <input type="text" name="depthUpper" value="<?php echo "$depthUpper"; ?>" size="4" /> km
	&nbsp;&nbsp;&nbsp;<span id="bounds"><?=$boundsv?></span>&nbsp;&nbsp;
	<label for="setbounds"><input type="checkbox" name="setbounds" id="setbounds" value="1" <?=$setbounds==1 ? 'checked' : '' ?> /> Set results to map bounds?</label>
	<input type="hidden" name="boundsv" id="boundsv" value="<?=$boundsv?>" />
    
</form>
</div>

<div class="thumbnail">
<table summary="key" class="">
<tr><td>Depth guide: &nbsp;</td>
<td style="color:#ff66ff"><span class="glyphicon glyphicon-record"></span></td><td><15 &nbsp;&nbsp;</td>
<td style="color:#ff0000"><span class="glyphicon glyphicon-record"></span></td><td>15-40 &nbsp;</td>
<td style="color:#ff9900"><span class="glyphicon glyphicon-record"></span></td><td>40-70 &nbsp;</td>
<td style="color:#ffff00"><span class="glyphicon glyphicon-record"></span></td><td>70-100 &nbsp;</td>
<td style="color:#00cc00"><span class="glyphicon glyphicon-record"></span></td><td>100-150 &nbsp;</td>
<td style="color:#00ffff"><span class="glyphicon glyphicon-record"></span></td><td>150-200 &nbsp;</td>
<td style="color:#0066ff"><span class="glyphicon glyphicon-record"></span></td><td>200-300 &nbsp;</td>
<td style="color:#800080"><span class="glyphicon glyphicon-record"></span></td><td>300+</td>
<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo count($quakes); ?> quakes listed </td>
</tr>
</table>
</div>


<div class="thumbnail"><div id="map_canvas"></div></div>

<div class="thumbnail">
<div style="height:300px;overflow:auto;'">
<table summary="quakes" id="qtable" class="table table-condensed t2">
 <thead> 
 <tr>
   <th class="lr">Ref</th>
   <th class="lr">Date</th>
   <th class="rr">Mag</th>
   <th class="rr">Depth</th>
   <th class="rr">Energy</th>
   <th class="rr">Location</th>
	 <th class="rr">Status</th>
   <th class="rr">Link&nbsp;&nbsp;&nbsp;</th>
 </tr>
 </thead> 
 <tbody>
<?php foreach ($quakes as $k => $q) { ?>
 <tr id="<?=$k?>">
  <td><div><?=$k?></div></td>
  <td><?=date("Y-m-d H:i:s",strtotime($q['dt']." GMT"))?></td>
  <td class="rr" style="color:<?=$q['m3']?>"><?=number_format($q['m'],3)?></td>
  <td class="rr" style="color:<?=$q['d2']?>"><?=number_format($q['deep'],2)?></td>
  <td class="rr"><?=number_format(($q['en']/73500),2,'.',',');?></td>
  <td class="rr"><?=$q['lat']?>,<?=$q['lng']?></td>
	<td class="rr"><?=$q['status']?></td>
  <td class="rr">
  <?php if ($q['link'] != "") { ?>
  	<a href="<?=$q['link']?>" title="Geonet Link" target="_blank">Link</a>
  <?php } ?>
  </td>
 </tr>
 <?php } ?>
 </tbody>
</table>
</div>
</div>

<p>&nbsp;&nbsp;&nbsp; Note: energy is shown as tonnes of TNT</p>

<h3>Summary Stats</h3>
<!-- 
<div class="progress">
        <?php $pc = ($stats['r1']/$stats['count'])*100; ?>
				<div class="progress-bar" style="width: <?=$pc?>%">
          <?=$stats['r1']?>
        </div>
				<?php $pc = ($stats['r2']/$stats['count'])*100; ?>
        <div class="progress-bar progress-bar-striped" style="width: <?=$pc?>%">
           <?=$stats['r2']?>
        </div>
				<?php $pc = ($stats['r3']/$stats['count'])*100; ?>
        <div class="progress-bar progress-bar-success" style="width: <?=$pc?>%">
           <?=$stats['r3']?>
        </div>
				<?php if ($stats['r4']>0) { $pc = ($stats['r4']/$stats['count'])*100; ?>
        <div class="progress-bar  progress-bar-warning" style="width: <?=$pc?>%">
           <?=$stats['r4']?>
        </div>
				<?php } if ($stats['r5']>0) { $pc = ($stats['r5']/$stats['count'])*100; ?>
        <div class="progress-bar progress-bar-danger" style="width: <?=$pc?>%">
           <?=$stats['r5']?>
        </div>
				<?php } if ($stats['r6']>0) { $pc = ($stats['r6']/$stats['count'])*100; ?>
        <div class="progress-bar progress-bar-danger progress-bar-striped" style="width: <?=$pc?>%">
           <?=$stats['r6']?>
        </div>
				<?php } ?>
      </div>
	 -->		
<div class="row">
	<div class="col-sm-6 col-md-3">
		<div class="thumbnail">
			<h4>Range</h4>
			<p>
				Start - <?=$stats['start']?> GMT<br />
				End - <?=$stats['end']?> GMT<br />
				Period - <?=$stats['hours']?><br />
				Count - <?=$stats['count']?><br />
			</p>
		</div>
	</div>
	<div class="col-sm-6 col-md-3">
		<div class="thumbnail">
			<h4>Magnitude</h4>
			<p>
				Max - <?=$stats['maxmag']?><br />
				Min - <?=$stats['minmag']?><br />
				Avg - <?=$stats['avgmag']?><br />
			</p>
			<div id="chart_div"></div>
			
		</div>
	</div>
	<div class="col-sm-6 col-md-3">
		<div class="thumbnail">
			<h4>Depth</h4>
			<p>
				Max - <?=$stats['maxd']?><br />
				Min - <?=$stats['mind']?><br />
				Avg - <?=$stats['avgd']?><br />
			</p>
		</div>
	</div>
	<div class="col-sm-6 col-md-3">
		<div class="thumbnail">
			<h4>Total Energy</h4>
			<p><?=number_format(($stats['energyt']/73500),2,'.',',');?> tonnes of TNT</p>			
		</div>
	</div>
</div>

<div class="thumbnail"><div id="chart_div2"></div></div>

<div class="thumbnail">
<h3>Last 24 hours of Madness</h3>
<img src="http://www.mossgreengarden.co.nz/geonet/depth-magnitude-multiplot_day_0001.png" class="img-responsive" />
	<div class="caption">
	<p>This image is in 2 parts:<br />
    The upper part displays magnitude, coloured by depth.<br />
    The lower part displays depth, coloured by magnitude.</p> 
	<p><i>image by Mark</i></p>
	</div>	
</div>





<?php 
//echo "<pre>";
// print_r($stats);
//print_r($quakes);
//echo"</pre>";

//die();
 ?>

<a id="geonet" href="http://info.geonet.org.nz/display/appdata" target="_blank" title="thanks geonet!"></a>
</div>
</body>
</html>
