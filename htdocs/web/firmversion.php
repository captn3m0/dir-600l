HTTP/1.1 200 OK
Content-Type: text/xml

<?
if ($AUTHORIZED_GROUP < 0)
{
	$result = "Authenication fail";
}
else
{
	if ($_POST["act"] == "checkreport")
	{
		$result = get("x", "/runtime/firmware/havenewfirmware");
	}
}
echo '<?xml version="1.0"?>\n';
?>
<firmware>
	<havenewfirmware><?=$result?></havenewfirmware>
</firmware>
