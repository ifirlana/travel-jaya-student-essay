<?php
	if($_GET){
	include_once "header.php";
		if(isset($_GET['kode_jadwal'])){ // proses keberangkatan
			
			$kode_jadwal	=	$_GET['kode_jadwal'];
			
			echo " Proses Jadwal ".$_GET['kode_jadwal']."...<br/>";
			
			$db = mysql_query("update  reservasi set berangkat = 1 where kode_jadwal = '".$kode_jadwal."' and berangkat = 0");// echo "update  reservasi set berangkat = 1 where kode_jadwal = '".$kode_jadwal."' and berangkat = 0 <br />";
			$query = mysql_query("select count(*) from  reservasi where berangkat = 1 and date(waktu_keberangkatan) = '".date("Y-m-d")."'"); //echo "select * from  reservasi where berangkat = 1 and date(waktu_keberangkatan) = '".date("Y-m-d")."'<br />";
			
			$check	=	mysql_num_rows($query);

			if($check > 0){//kalai ada, di update
				echo "Proses Keberangkatan berhasil <a href='../schedule.php'>klik disini</a><br />";			
			}
			else{
				echo "Proses Keberangkatan gagal.  <a href='../schedule.php'>klik disini</a><br />";
			}
		}
		elseif(isset($_GET['kode_cancel'])){ // proses cancel data
			
			$kode_jadwal	=	$_GET['kode_cancel'];
			
			echo " Proses Pembatalan ".$_GET['kode_cancel']."...<br/>";
			
			$db = mysql_query("update  reservasi set berangkat = 2 where kode_jadwal = '".$kode_jadwal."' and berangkat = 0");// echo "update  reservasi set berangkat = 1 where kode_jadwal = '".$kode_jadwal."' and berangkat = 0 <br />";
			$query = mysql_query("select count(*) from  reservasi where berangkat = 2 and date(waktu_keberangkatan) = '".date("Y-m-d")."'"); //echo "select * from  reservasi where berangkat = 1 and date(waktu_keberangkatan) = '".date("Y-m-d")."'<br />";
			
			$check	=	mysql_num_rows($query);

			if($check > 0){//kalai ada, di update
				echo "Proses Pembatalan berhasil <a href='../schedule.php'>klik disini</a><br />";			
			}
			else{
				echo "Proses Pembatalan gagal.  <a href='../schedule.php'>klik disini</a><br />";
			}
			}
	}
	else{
		echo "Error.";
		echo "<a href='home.php'>Click here</a>";
	}
?>