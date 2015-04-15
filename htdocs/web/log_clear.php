<?
include "/htdocs/phplib/trace.php";
if ($AUTHORIZED_GROUP==0)
{
 if($_POST["act"]  == "clear")
 {
	$LOG_COUNT = cut_count($_POST["logtype"], ",");
	TRACE_debug("log_clear.php is to clear ".$LOG_COUNT." type(s) of logs: ".$_POST["logtype"]);
	$LOG_INDEX = 0;
	while ($LOG_INDEX < $LOG_COUNT)
	{
		$LOG = cut($_POST["logtype"], $LOG_INDEX, ",");
		TRACE_debug("log_clear.php  LOG[".$LOG_INDEX."] = ".$LOG);
		if ($LOG!="")
		{
			$count = query("/runtime/log/".$LOG."/entry#");
			while($count >0)
			{
				del("/runtime/log/".$LOG."/entry");
				$count--;
			}
			
			if( $LOG=="sysact" )
			{
				$time = query("/runtime/device/uptime");
				set("/runtime/log/".$LOG."/entry:1/time", $time);
				set("/runtime/log/".$LOG."/entry:1/message", "The System log is cleared by user.");
			}
			else if( $LOG=="attack" )
			{
				$time = query("/runtime/device/uptime");
				set("/runtime/log/".$LOG."/entry:1/time", $time);
				set("/runtime/log/".$LOG."/entry:1/message", "The Firewall & Security log is cleared by user.");
			}
			else if( $LOG=="drop" )
			{
				$time = query("/runtime/device/uptime");
				set("/runtime/log/".$LOG."/entry:1/time", $time);
				set("/runtime/log/".$LOG."/entry:1/message", "The Router Status log is cleared by user.");
			}
		}
		$LOG_INDEX++;
	}
 }
}
include "/htdocs/web/getcfg.php";
?>
