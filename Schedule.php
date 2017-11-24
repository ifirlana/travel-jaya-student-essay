<?php include_once "header.php";?>
<?php include_once "sidebar.php";?>
<h1>SCHEDULE</h1>
<?php
$query =	mysql_query("select *, (select sum(jumlah) from reservasi where reservasi.kode_jadwal = jadwal.kode_jadwal and reservasi.berangkat = 0) total from jadwal order by tujuan asc");
$check	=	mysql_num_rows($query);
if($check >= 0){

	echo "<table width='100%' border='1' cellspacing='0'>
	<tr><th>Jadwal</th><th>Keterangan</th><th>waktu</th><th>Jumlah</th><th>&nbsp;</th></tr>";
	while($data = mysql_fetch_array($query)){
		echo "<tr><td>". $data['kode_jadwal']."</td>
						<td>".$data['asal']."-".$data['tujuan']."</td>
						<td align='center'>".$data['jam_keberangkatan']."</td>
						<td align='center'>".$data['total']."</td>
						<td align='center'><a href='proses_keberangkatan.php/?kode_jadwal=".$data['kode_jadwal']."'>Berangkat</a> || <a href='proses_keberangkatan.php/?kode_cancel=".$data['kode_jadwal']."'>Batal</td>
						</tr>";
		}
	echo "</table>";
	}
	?>

<?php include_once "footer.php";?>