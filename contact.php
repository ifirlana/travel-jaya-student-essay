<?php include_once "header.php";?>
<?php include_once "sidebar.php";?>
<h1>CONTACT</h1>
<?php
$query =	mysql_query("select SenderNumber from inbox group by SenderNumber");
$check	=	mysql_num_rows($query);
if($check >= 0){
	echo "<table border='1' cellspacing = '0' width='100%'>";
	while($data = mysql_fetch_array($query)){
		echo "<tr><td>".$data['SenderNumber']."</td></tr>";
		}
	echo "</table>";
	}
?>

<?php include_once "footer.php";?>
