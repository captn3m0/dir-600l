<?
include "/htdocs/phplib/xnode.php";

$path = XNODE_getpathbytarget("/wifi", "entry", "uid", "WIFI-1", 0);
$tmp = query($path."/ssid");
$cnt = cut_count($tmp, "\"");
if ($cnt > 1)
{
	$i = 0;
	$cnt--;
	while ($i < $cnt)
	{
		$ssid = $ssid.cut($tmp, $i, "\"")."\\\"";
		$i++;
	}
	$ssid = $ssid.cut($tmp, $i, "\"");
}
else
{
	$ssid = $tmp;
}
?>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "RUNTIME.INF.LAN-1,RUNTIME.PHYINF.WLAN-1",
	OnLoad: function() { BODY.CleanTable("client_list"); },
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		this.inf = PXML.FindModule("RUNTIME.INF.LAN-1");
		this.phyinf = PXML.FindModule("RUNTIME.PHYINF.WLAN-1");
		if (!this.inf||!this.phyinf)
		{
			BODY.ShowAlert("<?echo i18n("Initial() ERROR!!!");?>");
			return false;
		}
		this.inf += "/runtime/inf/dhcps4/leases";
		this.phyinf += "/runtime/phyinf/media/clients";
		var cnt = XG(this.phyinf+"/entry#");
		var ssid = "<? echo $ssid; ?>";
		if (cnt=="") cnt = 0;
		OBJ("client_cnt").innerHTML = cnt;
		for (var i=1; i<=cnt; i++)
		{
			var uid		= "DUMMY-"+i;
			var mac		= XG(this.phyinf+"/entry:"+i+"/macaddr");
			var ipaddr	= this.GetIP(mac);
			var mode	= XG(this.phyinf+"/entry:"+i+"/band");
			var rate	= XG(this.phyinf+"/entry:"+i+"/rate");
//			var signal	= XG();
//			var data	= [ssid, mac, ipaddr, mode, rate, signal];
//			var type	= ["text", "text", "text", "text", "text", "text"];
			var data	= [ssid, mac, ipaddr, mode, rate];
			var type	= ["text", "text", "text", "text", "text"];
			BODY.InjectTable("client_list", uid, data, type);
		}
		return true;
	},
	PreSubmit: function() { return null; },
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	inf: null,
	phyinf: null,
	GetIP: function(mac)
	{
		var path = GPBT(this.inf, "entry", "macaddr", mac.toLowerCase(), false);
		return XG(path+"/ipaddr");
	}
}
</script>
