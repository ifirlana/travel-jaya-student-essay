<?php include_once "header.php";?>
<?php include_once "sidebar.php";?>
<h1>BROADCAST</h1>
<form method="POST">
	<table>
		<tr><td>SMS</td><td><textarea name="content" maxlength="144" rows="5"></textarea></td></tr>
		<tr><td><input type="submit" name="submit" value="send sms"></td></tr>
	</table>
</form>
<?php 
	if($_POST){
		
		$content = $_POST['content'];
		
		$query =	mysql_query("select SenderNumber from inbox group by SenderNumber");
		
		while($data = mysql_fetch_array($query)){
			
			$query_2 = mysql_query("insert into outbox (DestinationNumber, TextDecoded) values ('".$data['SenderNumber']."', '".$content."')");
			}
		
		echo "Broadcast has been published : ".$content."<br />";
		}?>
<?php include_once "footer.php";?>
