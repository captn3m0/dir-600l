<html>
<head>
<script>
function logw_swh()
{
    if(document.getElementById("log_warming").checked){
        document.getElementById("logw").disabled = false;
    }else{
        document.getElementById("logw").disabled = true;
    }
    if(document.getElementById("warming").checked){
        document.getElementById("log").disabled = false;
    }else{
        document.getElementById("log").disabled = true;
    }
}
</script>
</head>
<body onload="logw_swh();" >
<form name="myform" action="/goform/form_mydlink_log_opt" method="post">
<table>
<tr><td><input type="text" name="settingsChanged" value="1"></td><td>settingsChanged</td></tr>
<tr><td><input type="text" name="config.log_enable" value="1"></td><td>config.log_enable switch of mydlink log</td></tr>
<tr><td><input type="text" name="config.log_userloginfo" value="1"></td><td>config.log_userloginfo switch of submit user login info to mydlink client</td></tr>
<tr><td><input type="text" name="config.log_fwupgrade" value="1"></td><td>config.log_fwupgrade switch of submit upgreade info to mydlink client</td></tr>
<tr><td><input type="checkbox" onclick="logw_swh()" id="log_warming"><input type="text" name="config.log_wirelesswarn" id="logw" value="1"></td><td>config.log_wirelesswarn switch of submit wireless warming info to mydlink client</td></tr>
<tr><td><input type="checkbox" onclick="logw_swh()" checked id="warming"><input type="text" name="config.wirelesswarn" id="log" value="1"></td><td>config.wirelesswarn switch of submit wireless warming info to mydlink client</td></tr>
<tr><td><input type="text" name="config.tc_meter_email_enable" value="1"></td><td>config.tc_meter_email_enable traffic meter email enable or disable</td></tr>

<input type="submit" value="Submit" />
</table>
</form>
</body>
</html>

