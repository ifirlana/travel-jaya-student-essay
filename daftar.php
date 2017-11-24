<?php include_once "header.php";?>
<?php include_once "sidebar.php";?>
<h1>DAFTAR</h1>
<form method="POST">
	<table>
		<tr><td>Nama</td><td></td><td><input type="text" name="nama" value="" /></td></tr>
		<tr><td>Nomor Hanphone</td><td>+62</td><td><input type="text" name="nomor_handphone" value="" /></td></tr>
		<tr><td>Deposit</td><td></td><td><input type="text" name="deposit" value="0" /></td></tr>
		<tr><td colspan='3'><input type="submit" value="submit"></td></tr>
	</table>
</form>
<?php

	if($_POST){
		$nama				=	$_POST['nama'];
		$nomor_handphone	=	"+62".$_POST['nomor_handphone'];
		$deposit			=	$_POST['deposit'];

		$query =	mysql_query("select * from pelanggan where pelanggan.handphone = ".$nomor_handphone);
		$check	=	mysql_num_rows($query);

		if($check > 0){//kalai ada, di update

			$query =	mysql_query("update pelanggan set nama = '".$nama."', deposit = '".$deposit."' where handphone = '".$nomor_handphone."'");
			echo "<b>Data pelanggan sudah terupdate</b>";
			}
			else{//kalau tidak ada, di insert

				$query =	mysql_query("insert into pelanggan(handphone, nama, deposit) values('".$nomor_handphone."','".$nama."','".$deposit."')");
				echo "<b>Data pelanggan sudah dimasukan</b>";
				}
		}
?>
<?php
$query =	mysql_query("select * from pelanggan");
$check	=	mysql_num_rows($query);
if($check >= 0){

	echo "<table width='100%' border='1' cellspacing='0'>
	<tr><th>handphone</th><th>nama</th><th>deposit</th></tr>";
	while($data = mysql_fetch_array($query)){
		echo "<tr><td>". $data['handphone']."</td>
						<td align='center'>".$data['nama']."</td>
						<td align='center'>".$data['deposit']."</td>
						</tr>";
		}
	echo "</table>";
	}
	?>
<?php include_once "footer.php";?>