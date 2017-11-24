<?php include_once "header.php";?>
<?php include_once "sidebar.php";?>
<h1>Home</h1>
<hr />
<h2>Grafik Keuntungan Travel Jaya</h2>
<div id="chart2">
</div>
<script class="code" type="text/javascript" language="javascript">
$(document).ready(function(){   
    var line2 = [<?php 
		//echo "['1/1/2014', 42], ['2/14/2014', 56], ['3/7/2014', 39], ['4/22/2014', 81]"
		$temp	=	"";
		$db 		=	mysql_query("select date(reservasi.waktu_keberangkatan) tanggal, sum(jadwal.harga * reservasi.jumlah) nominal  from reservasi, jadwal where reservasi.kode_jadwal = jadwal.kode_jadwal and berangkat = 1 group by date(waktu_keberangkatan)");
		
		while($row = mysql_fetch_array($db)){
			
			$temp .= "['".date("m/d/Y",strtotime($row['tanggal']))."', ".$row['nominal']."], ";
			
			}
			$temp = substr($temp,0,-2);
			echo $temp;
			//
		?>];

    var plot2 = $.jqplot('chart2', [line2], {
      axes: {
        xaxis: {
          renderer: $.jqplot.DateAxisRenderer,
          label: 'Tanggal Keberangkatan',
          labelRenderer: $.jqplot.CanvasAxisLabelRenderer,
          tickRenderer: $.jqplot.CanvasAxisTickRenderer,
          tickOptions: {
              // labelPosition: 'middle',
              angle: 15
          },

          
        },
        yaxis: {
          label: 'Nominal',
          labelRenderer: $.jqplot.CanvasAxisLabelRenderer
        }
      }
    });

});
</script>
<table class="table-form" border="1" cellspacing="0">
	<tr><th>Bulan</th><th>Year</th><th>Nominal</th></tr>
	<?php
		$db = mysql_query("select month(waktu_keberangkatan) bulan, year(waktu_keberangkatan) tahun, sum(jadwal.harga * reservasi.jumlah)  nominal from reservasi, jadwal where reservasi.kode_jadwal = jadwal.kode_jadwal and (berangkat = 1 or berangkat = 3) group by month(waktu_keberangkatan), year(waktu_keberangkatan) order by month(waktu_keberangkatan) desc, year(waktu_keberangkatan)");
		while($row = mysql_fetch_array($db)){
			echo "<tr><td align='center'>".$row['bulan']."</td><td align='center'>".$row['tahun']."</td><td align='right'>".$row['nominal']."</td></tr>";
			}
	?>
</table>
<?php include_once "footer.php";?>
