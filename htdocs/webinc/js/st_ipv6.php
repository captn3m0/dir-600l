<script type="text/javascript">
var EventName=null;
function SendEvent(str)
{
	var ajaxObj = GetAjaxObj(str);
	if (EventName != null) return;

	EventName = str;
	ajaxObj.createRequest();
	ajaxObj.onCallback = function (xml)
	{
		ajaxObj.release();
		//setTimeout("OnLoadBody()", 3*1000);
		EventName = null;
	}
	ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
	ajaxObj.sendRequest("service.cgi", "EVENT="+EventName);
}
/*PPPoE or 3G*/
function WAN3PPPDIALUP()	{ SendEvent("WAN-3.PPP.DIALUP"); }
function WAN3PPPHANGUP()	{ SendEvent("WAN-3.PPP.HANGUP"); }

function Page() {}
Page.prototype =
{
	services: "<?
		$layout = query("/runtime/device/layout");
	
		echo "RUNTIME.PHYINF,";
		if ($layout=="router")
			echo "INET.WAN-1,INET.WAN-3,INET.WAN-4,INET.LAN-4,RUNTIME.INF.LAN-1,RUNTIME.INF.LAN-4,RUNTIME.INF.WAN-1,RUNTIME.INF.WAN-3,RUNTIME.INF.WAN-4,DHCPS6.LAN-4";
		else
			echo "RUNTIME.INF.BRIDGE-1";
		?>",
	OnLoad: function() { BODY.CleanTable("client6_list"); },
	OnUnload: function() {},
	OnSubmitCallback: function ()
	{
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var rlan1 = PXML.FindModule("RUNTIME.INF.LAN-1");
		var rlan4 = PXML.FindModule("RUNTIME.INF.LAN-4");
		//PXML.doc.dbgdump();
<?
		echo "\t\tif (\"".$layout."\"==\"router\")";
?>
		{
			if (!this.InitWAN()) return false;
			if (!this.InitLAN()) return false;
		}
		else
		{
			OBJ("ipv6_bridge").style.display = "block";
			OBJ("ipv6").style.display 		 = "none";
			OBJ("ipv6_client").style.display = "none";
		}

		var pfxlen = XG(rlan4+"/runtime/inf/dhcps6/prefix");
		var cntlan1 = XG(rlan1+"/runtime/inf/dhcps4/leases/entry#");
		var cntlan4 = XG(rlan4+"/runtime/inf/dhcps6/leases/entry#");
		if(cntlan1=="") cntlan1 = 0;
		if(cntlan4=="") cntlan4 = 0;
		for (var i=1; i<=cntlan4; i++)
		{
			var uid = "DUMMY-"+i;
			var ipaddr = XG(rlan4+"/runtime/inf/dhcps6/leases/entry:"+i+"/ipaddr");
			var ipstr = ipaddr+"/"+pfxlen;
			var mac = XG(rlan4+"/runtime/inf/dhcps6/leases/entry:"+i+"/macaddr");
			for (var j=1; j<=cntlan1; j++)
			{
				var mactmp = XG(rlan1+"/runtime/inf/dhcps4/leases/entry:"+j+"/macaddr");
				if(mactmp.toUpperCase() == mac)
				{
					var hostname = XG(rlan1+"/runtime/inf/dhcps4/leases/entry:"+j+"/hostname");
					break;
				}
			}
			var data = [ipstr, hostname];
			var type = ["text", "text"];
			BODY.InjectTable("client6_list", uid, data, type);
		}
		return true;
	},
	PreSubmit: function()
	{
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	waninetp: null,
	rwanphyp: null,
	rlanphyp: null,
	wanip: null,
	lanip: null,
	inetp: null,
	prefix: null,
	is_ll: null,
	
	InitWAN: function ()
	{
		var wan	= PXML.FindModule("INET.WAN-4");
		var wan1 = PXML.FindModule("INET.WAN-1");
		var wan3 = PXML.FindModule("INET.WAN-3");
		var rphy = PXML.FindModule("RUNTIME.PHYINF");
		var rwan = PXML.FindModule("RUNTIME.INF.WAN-4");
		var is_ppp6 = 0;
		var is_ppp10 = 0;
		//var is_ll = 0;
		var wan3inetuid = XG(wan3+"/inf/inet");
		var wan3inetp = GPBT(wan3+"/inet", "entry", "uid", wan3inetuid, false);
		var wan1inetuid = XG(wan1+"/inf/inet");
		var wan1inetp = GPBT(wan1+"/inet", "entry", "uid", wan1inetuid, false);
		if(XG(wan3inetp+"/addrtype") == "ppp6")
		{
			wan = PXML.FindModule("INET.WAN-3");
			rwan = PXML.FindModule("RUNTIME.INF.WAN-3");
			is_ppp6 = 1;
		}
		if(XG(wan1inetp+"/addrtype") == "ppp10")
		{
			wan = PXML.FindModule("INET.WAN-1");
			rwan = PXML.FindModule("RUNTIME.INF.WAN-1");
			is_ppp10 = 1;
		}
		var waninetuid = XG  (wan+"/inf/inet");
		var wanphyuid = XG  (wan+"/inf/phyinf");
		this.waninetp = GPBT(wan+"/inet", "entry", "uid", waninetuid, false);
		this.rwanphyp = GPBT(rphy+"/runtime", "phyinf", "uid", wanphyuid, false);
		//if(XG(this.waninetp+"/ipv6/mode") == "" && is_ppp6 != 1 && is_ppp10 != 1)
		if(XG(wan+"/inf/active") == 0 && is_ppp6 != 1 && is_ppp10 != 1)
		{
			wan = PXML.FindModule("INET.WAN-3");
			rwan = PXML.FindModule("RUNTIME.INF.WAN-3");
			this.is_ll = 1;
		}

		if(this.is_ll==1) 
		{
			OBJ("ll_ipv6").style.display 		 = "";
		}
		else
		{
			OBJ("ipv6").style.display 		 = "";
		}

		var str_networkstatus = str_Disconnected = "<?echo i18n("Disconnected");?>";
		var str_Connected = "<?echo i18n("Connected");?>";
		var wancable_status=0;
		if ((!this.waninetp))
		{
			BODY.ShowAlert("InitWAN() ERROR!!!");
			return false;
		}

		if((XG  (this.rwanphyp+"/linkstatus")!="0")&&(XG  (this.rwanphyp+"/linkstatus")!=""))
		{
		   wancable_status=1;
		}

		if(is_ppp6==1 || is_ppp10==1 || XG(this.waninetp+"/ipv6/mode")=="AUTO" || XG(this.waninetp+"/ipv6/mode")=="6IN4")
			OBJ("ipv6_pd").style.display = "";
			
		var rstlwan = rwan+"/runtime/inf/stateless";
		rwan = rwan+"/runtime/inf/inet";
		if (this.is_ll == 1)
		{
			var str_wantype = "Link-Local";
			var str_wanipaddr = "";
			var str_wanprefix = "";
			var str_wangateway = "None";
			var str_wanDNSserver = "";
			var str_wanDNSserver2 = "";
		}
		else if ((XG(this.waninetp+"/addrtype") == "ipv6")&& wancable_status==1)
		{
			var str_wantype = XG(this.waninetp+"/ipv6/mode");
			if (XG(rwan+"/ipv6/ipaddr")!="")
			{
				var str_wanipaddr = XG(rwan+"/ipv6/ipaddr");
				var str_wanprefix = "/"+XG(rwan+"/ipv6/prefix");
				var str_wangateway = XG(rwan+"/ipv6/gateway");
			}
			else
			{
				var str_wanipaddr = XG(this.rwanphyp+"/ipv6/global/ipaddr");
				var str_wanprefix = "/"+XG(this.rwanphyp+"/ipv6/global/prefix");
				var str_wangateway = XG(rstlwan+"/gateway");
			}
			var str_wanDNSserver  = XG(rwan+"/ipv6/dns:1")?XG(rwan+"/ipv6/dns:1"):"";
			var str_wanDNSserver2 = XG(rwan+"/ipv6/dns:2")?XG(rwan+"/ipv6/dns:2"):"";
			
			if(str_wantype == "AUTO")
			{
				//if wan has more than one ipaddr, we need to get it by runtime phyinf and get the last one
				this.rwanphyp = GPBT(rphy+"/runtime", "phyinf", "uid", wanphyuid, false);
				var temp_wanipaddr = XG(this.rwanphyp+"/ipv6/global/ipaddr");
				var temp_wanprefix = XG(this.rwanphyp+"/ipv6/global/prefix");

				var arr_wanipaddr = temp_wanipaddr.split(" ");
				var arr_wanprefix = temp_wanprefix.split(" ");
				var str_wanipaddr = "";
				for(i=0;i< arr_wanipaddr.length;i++)
				{
					str_wanipaddr = str_wanipaddr+arr_wanipaddr[i]+" /"+arr_wanprefix[i]+" ";
				}
				str_wanprefix = "";
			}
		}
		else if (is_ppp6 == 1 && wancable_status==1)
		{
			rwan = PXML.FindModule("RUNTIME.INF.WAN-3");
			rwan = rwan+"/runtime/inf/inet";
			var rwan4 = PXML.FindModule("RUNTIME.INF.WAN-4");
			var str_wantype = "PPPoE";
			var str_wanipaddr = XG(rwan+"/ppp6/local");
			var str_wanprefix = "/64";
			var str_wangateway = XG(rwan+"/ppp6/peer");
			if(XG(rwan+"/ppp6/dns:1")!="")
			{
				var str_wanDNSserver = XG(rwan+"/ppp6/dns:1");
				if(XG(rwan+"/ppp6/dns:2")!="")	var str_wanDNSserver2 = XG(rwan+"/ppp6/dns:2");
				else							var str_wanDNSserver2 = XG(rwan4+"/runtime/inf/inet/ipv6/dns:1");
			}
			else
			{
				var str_wanDNSserver = XG(rwan4+"/runtime/inf/inet/ipv6/dns:1");
				var str_wanDNSserver2 = XG(rwan4+"/runtime/inf/inet/ipv6/dns:2");
			}
		}
		else if (is_ppp10 == 1 && wancable_status==1)
		{
			rwan = PXML.FindModule("RUNTIME.INF.WAN-1");
			rwanc = rwan+"/runtime/inf/child";
			var rwan4 = PXML.FindModule("RUNTIME.INF.WAN-4");
			var str_wantype = "PPPoE";
			var str_wanipaddr = XG(rwanc+"/ipaddr");
			var str_wanprefix = "/64";
			var str_wangateway = XG(rwanc+"/ppp6/peer");
			if(XG(wan1inetp+"/ppp6/dns/count") > 0)
			{
				var str_wanDNSserver = XG(rwan+"/runtime/inf/inet/ppp6/dns:1");
				if(XG(rwan+"/runtime/inf/inet/ppp6/dns:2")!="")
					var str_wanDNSserver2 = XG(rwan+"/runtime/inf/inet/ppp6/dns:2");
				else
					var str_wanDNSserver2 = XG(rwan4+"/runtime/inf/inet/ipv6/dns:1");
			}
			else
			{
				var str_wanDNSserver = XG(rwan4+"/runtime/inf/inet/ipv6/dns:1");
				var str_wanDNSserver2 = XG(rwan4+"/runtime/inf/inet/ipv6/dns:2");
			}
		}
		else if(wancable_status==0)
		{
			if(XG(this.waninetp+"/addrtype")=="ipv6")
				var str_wantype = XG(this.waninetp+"/ipv6/mode");
			else if(is_ppp6==1 || is_ppp10==1)	
				var str_wantype = "PPPoE";

			var str_wanipaddr = "";
			var str_wanprefix = "";
			var str_wangateway = "";
			var str_wanDNSserver = "";
			var str_wanDNSserver2 = "";
		}
		
		if(is_ppp6==1)	
		{
			var str_status = this.PPPBtnSetup(wancable_status, str_Connected, str_Disconnected);
			OBJ("status").innerHTML  = str_status;//((wancable_status==1 && str_wanipaddr!="") ? str_status:str_Disconnected);
		}
		else if(!this.is_ll)
			OBJ("status").innerHTML  = ((wancable_status==1 && str_wanipaddr!="") ? str_Connected:str_Disconnected);
		else
			OBJ("status").innerHTML  = str_Disconnected;
			
		if(this.is_ll==1)
			OBJ("ll_type").innerHTML = str_wantype;
		else
		{
			if(str_wantype=="STATIC") str_wantype = "Static";
			else if(str_wantype=="AUTO")
			{
				//var rwan4 = PXML.FindModule("RUNTIME.INF.WAN-4");
				//var rwanmode = XG(rwan4+"/runtime/inf/inet/ipv6/mode");
				//if(rwanmode=="STATEFUL") 		str_wantype = "DHCPv6";
				//else if(rwanmode=="STATELESS") 	str_wantype = "SLAAC";
				//else 							str_wantype = "Autoconfiguration";
				str_wantype = "Autoconfiguration (SLAAC/DHCPV6)";
			}
			OBJ("type").innerHTML = str_wantype;
		}
		
		if(str_wanipaddr=="")
		{
			OBJ("wan_address").innerHTML  = "None";
			OBJ("wan_address_pl").innerHTML  = "";
		}
		else
		{
			OBJ("wan_address").innerHTML  = str_wanipaddr;
			OBJ("wan_address_pl").innerHTML  = str_wanprefix;
		}
		if(this.is_ll==1)
			OBJ("ll_gateway").innerHTML  =  str_wangateway;
		else
			OBJ("gateway").innerHTML  =  str_wangateway!="" ? str_wangateway:"None";
		if(wancable_status==1)
		{
			if(str_wanDNSserver == "" && this.is_ll==0) str_wanDNSserver = XG(this.waninetp+"/ipv6/dns/entry:1");
			if(str_wanDNSserver2 == "" && this.is_ll==0 ) str_wanDNSserver2 = XG(this.waninetp+"/ipv6/dns/entry:2");
		}
		OBJ("br_dns1").innerHTML  = str_wanDNSserver!="" ? str_wanDNSserver:"None";
		OBJ("br_dns2").innerHTML  = str_wanDNSserver2!="" ? str_wanDNSserver2:"None";

		return true;
	},
	InitLAN: function()
	{
		var lan	= PXML.FindModule("INET.LAN-4");
		var rlan = PXML.FindModule("RUNTIME.INF.LAN-4");
		var dhcps6p = PXML.FindModule("DHCPS6.LAN-4");
		var inetuid = XG  (lan+"/inf/inet");
		var phyuid = XG  (lan+"/inf/phyinf");
		var phy = PXML.FindModule("RUNTIME.PHYINF");
		/*this.inetp = GPBT(lan+"/inet", "entry", "uid", inetuid, false);*/
		this.rlanphyp = GPBT(phy+"/runtime", "phyinf", "uid", phyuid, false);
		/*
		if (!this.inetp)
		{
			BODY.ShowAlert("InitLAN() ERROR!!!");
			return false;
		}
		*/

		if(this.is_ll)
		{
			OBJ("ll_lan_ll_address").innerHTML = XG(this.rlanphyp+"/ipv6/link/ipaddr");
			OBJ("ll_lan_ll_pl").innerHTML = "/64";
		}
		else
		{
			OBJ("lan_ll_address").innerHTML = XG(this.rlanphyp+"/ipv6/link/ipaddr");
			OBJ("lan_ll_pl").innerHTML = "/64";
		}

		//var dhcpp = GPBT(dhcps6p+"/dhcps6", "entry", "uid", XG(dhcps6p+"/inf/dhcps6"), false);
		//var enpd = XG(dhcpp+"/pd/enable");
			
		//var b = rlan+"/runtime/inf/dhcps6/pd";
		//var rpd = XG(b+"/enable");
		//var pdnetwork = XG(b+"/network");
		//var pdpfx = XG(b+"/prefix");
		var rwan = PXML.FindModule("RUNTIME.INF.WAN-4");
		var child = XG(rwan+"/runtime/inf/child/uid");
		var pdnetwork = XG(rwan+"/runtime/inf/child/pdnetwork");
		var pdprefix = XG(rwan+"/runtime/inf/child/pdprefix");
		var enpd="0";
		if(child!="")	enpd = "1";
		else			enpd = "0";
		if(pdnetwork!="")
		{
			OBJ("pd_prefix").innerHTML = pdnetwork;
			OBJ("pd_pl").innerHTML = "/"+pdprefix;
		}
		else
		{
			OBJ("pd_prefix").innerHTML = "None";
			OBJ("pd_pl").innerHTML = "";
		}

		if(!this.is_ll)
			OBJ("enable_pd").innerHTML = (enpd=="1")? "Enabled":"Disabled"; 
			
		//if(pdnetwork!="" && rpd=="1")
		//{
		//	OBJ("pd_prefix").innerHTML = pdnetwork;
		//	OBJ("pd_pl").innerHTML = "/"+pdpfx;
		//}
		//else
		//{
		//	OBJ("pd_prefix").innerHTML = "None";
		//	OBJ("pd_pl").innerHTML = "";
		//}

		b = rlan+"/runtime/inf/inet/ipv6";
		this.lanip = XG(b+"/ipaddr");
		this.prefix = XG(b+"/prefix");
		if(this.lanip!="")
		{
			OBJ("lan_address").innerHTML = this.lanip;
			OBJ("lan_pl").innerHTML = "/"+this.prefix;
		}
		else
		{
			OBJ("lan_address").innerHTML = "None";
			OBJ("lan_pl").innerHTML = "";
		}
			
		return true;
	},
	PPPBtnSetup: function(wancable_status, str_Connected, str_Disconnected)
	{
		var rwan = PXML.FindModule("RUNTIME.INF.WAN-3");
		var connStat = XG(rwan+"/runtime/inf/pppd/status");    
			
		OBJ("st_wan_ppp_action").style.display = "";
		switch (connStat)
		{
	        case "connected":
				if (wancable_status == 1)
		        {
		            str_networkstatus=str_Connected;
		            OBJ("st_wan_ppp_connect").disabled = true;
		            OBJ("st_wan_ppp_disconnect").disabled = false;
		        }
		        else
		        {
		            str_networkstatus=str_Disconnected;
		            OBJ("st_wan_ppp_connect").disabled = false;
		            OBJ("st_wan_ppp_disconnect").disabled = true;
		        }
		        break;
	        case "":
	        case "disconnected":
	            str_networkstatus=str_Disconnected;
	            OBJ("st_wan_ppp_connect").disabled = false;
	            OBJ("st_wan_ppp_disconnect").disabled = true;
	            wancable_status=0;
	        	break;
	        /*
	        case "on demand":
	            str_networkstatus="<?echo i18n("Idle");?>";
	            OBJ("st_wan_ppp_connect").disabled = false;
	            OBJ("st_wan_ppp_disconnect").disabled = true;
	            wancable_status=0;
	       		break;
	       	*/
	        default:
	            str_networkstatus = "<?echo i18n("Busy ...");?>";
	            OBJ("st_wan_ppp_connect").disabled = false;
	            OBJ("st_wan_ppp_disconnect").disabled = false;
	            setTimeout("BODY.OnLoad()", 6*1000);
	            break;
		}
        return str_networkstatus;
	},
	PPP_Connect: function()
	{
	    WAN3PPPDIALUP();
	    setTimeout("BODY.OnLoad()", 5*1000);
	},
	PPP_Disconnect: function()
	{
	    WAN3PPPHANGUP();
	    setTimeout("BODY.OnLoad()", 5*1000);
	}
}
</script>