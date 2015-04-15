<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "INET.WAN-1,INET.WAN-2,WAN,WIFI.WLAN-1",
	OnLoad: function()
	{
		if (!this.isFreset)	this.ShowStatus();
		else				this.ShowCurrentStage();
		OBJ("show_easy").checked = ("<?echo query("/device/features/easysetup/enable");?>"=="1")? true:false;
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		BODY.Logout();
		return true;
	},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.Initial()) return false;
		return true;
	},
	PreSubmit: function()
	{
		PXML.CheckModule("INET.WAN-1", null, null, "ignore");
		PXML.CheckModule("INET.WAN-2", null, null, "ignore");
		PXML.CheckModule("WAN", null, "ignore", null);
		PXML.ActiveModule("WIFI.WLAN-1");
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	inet1p: null,
	inet2p: null,
	inf1p: null,
	inf2p: null,
	wifip: null,
	phyinf: null,
	stages: new Array ("easy_main", "easy_wan", "easy_wlan", "easy_summary"),
	wanTypes: new Array ("DHCP", "PPPoE", "PPTP", "L2TP", "STATIC"),
	currentStage: 0,	// 0 ~ this.stages.length
	currentWanType: 0,	// 0 ~ this.wanTypes.length
	online: false,
	isFreset: <?if (query("/runtime/device/devconfsize")>0) echo 'false'; else echo 'true';?>,
	OnClickEasySetup: function()
	{
		this.SetupSummaryDiv("complete_setting");
		this.ShowCurrentStage();
	},
	SetupSummaryDiv: function(type)
	{
		if (type==="curr_setting")
		{
			OBJ("current_setting").style.display	= "block";
			OBJ("setup_button_list").style.display	= "block";
			OBJ("setup_complete").style.display		= "none";
			OBJ("wiz_button_list").style.display	= "none";
		}
		else
		{
			OBJ("current_setting").style.display	= "none";
			OBJ("setup_button_list").style.display	= "none";
			OBJ("setup_complete").style.display		= "block";
			OBJ("wiz_button_list").style.display	= "block";
		}
	},
	OnClickShowEasy: function()
	{
		var en_es = OBJ("show_easy").checked ? "1":"0";
		var ajaxObj = GetAjaxObj("SetEasySetup");
		ajaxObj.createRequest();
		ajaxObj.onCallback = function (xml) { ajaxObj.release(); /*alert(xml.Get("/device/features/easysetup/enable"));*/}
		ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
		ajaxObj.sendRequest("set_easysetup.php", "EnableEasySetup="+en_es);
	},
	Initial: function()
	{
		this.inet1p = PXML.FindModule("INET.WAN-1");
		this.inet2p = PXML.FindModule("INET.WAN-2");
		this.phyinf = this.wifip = PXML.FindModule("WIFI.WLAN-1");
		if (!this.inet1p||!this.inet2p||!this.wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		var inet1 = XG(this.inet1p+"/inf/inet");
		var inet2 = XG(this.inet2p+"/inf/inet");
		this.inf1p = this.inet1p+"/inf";
		this.inf2p = this.inet2p+"/inf";
		this.inet1p = GPBT(this.inet1p+"/inet", "entry", "uid", inet1, false);
		this.inet2p = GPBT(this.inet2p+"/inet", "entry", "uid", inet2, false);
		this.wifip = GPBT(this.wifip+"/wifi", "entry", "uid", "WIFI-1", false);
		this.phyinf += "/phyinf";
		this.GetWanType();
		COMM_SetSelectValue(OBJ("wan_mode"), this.wanTypes[this.currentWanType]);
		/////////////////////////// initial IPv4 hidden nodes ///////////////////////////
		OBJ("ipv4_mtu").value = XG(this.inet1p+"/ipv4/mtu");
		/////////////////////////// initial PPPv4 hidden nodes ///////////////////////////
		OBJ("ppp4_mtu").value		= XG(this.inet1p+"/ppp4/mtu");
		OBJ("ppp4_mru").value		= XG(this.inet1p+"/ppp4/mru");
		OBJ("ppp4_timeout").value	= XG(this.inet1p+"/ppp4/dialup/idletimeout");
		/////////////////////////// initial DHCP settings ///////////////////////////
		/////////////////////////// initial PPPoE settings ///////////////////////////
		OBJ("wiz_pppoe_usr").value		= XG(this.inet1p+"/ppp4/username");
		OBJ("wiz_pppoe_passwd").value	= XG(this.inet1p+"/ppp4/password");
		OBJ("wiz_pppoe_passwd2").value	= XG(this.inet1p+"/ppp4/password");
		/////////////////////////// initial PPTP settings ///////////////////////////
		OBJ("wiz_pptp_svr").value		= ResAddress(XG(this.inet1p+"/ppp4/pptp/server"));
		OBJ("wiz_pptp_usr").value		= XG(this.inet1p+"/ppp4/username");
		OBJ("wiz_pptp_passwd").value	= XG(this.inet1p+"/ppp4/password");
		OBJ("wiz_pptp_passwd2").value	= XG(this.inet1p+"/ppp4/password");
		/////////////////////////// initial L2TP settings ///////////////////////////
		OBJ("wiz_l2tp_svr").value		= ResAddress(XG(this.inet1p+"/ppp4/l2tp/server"));
		OBJ("wiz_l2tp_usr").value		= XG(this.inet1p+"/ppp4/username");
		OBJ("wiz_l2tp_passwd").value	= XG(this.inet1p+"/ppp4/password");
		OBJ("wiz_l2tp_passwd2").value	= XG(this.inet1p+"/ppp4/password");
		/////////////////////////// initial STATIC IP settings ///////////////////////////
		var mask1 = COMM_IPv4INT2MASK(XG(this.inet1p+"/ipv4/mask"));
		if (mask1=="0.0.0.0") mask1 = "255.255.255.0";
		OBJ("wiz_static_ipaddr").value	= ResAddress(XG(this.inet1p+"/ipv4/ipaddr"));
		OBJ("wiz_static_mask").value	= mask1;
		OBJ("wiz_static_gw").value		= ResAddress(XG(this.inet1p+"/ipv4/gateway"));
		OBJ("wiz_static_dns1").value	= ResAddress(XG(this.inet1p+"/ipv4/dns/entry:1"));
		OBJ("wiz_static_dns2").value	= ResAddress(XG(this.inet1p+"/ipv4/dns/entry:2"));
		/////////////////////////// initial WIFI settings ///////////////////////////
		var sec_type = "none";
		OBJ("ssid").value = XG(this.wifip+"/ssid");
		sec_type = "wpa";
		if (!this.isFreset && (XG(this.wifip+"/authtype")==="OPEN" && XG(this.wifip+"/encrtype")==="NONE")) sec_type = "none";
		COMM_SetSelectValue(OBJ("security_type"), sec_type)
		OBJ("wpapsk").value = XG(this.wifip+"/nwkey/psk/key");
		this.OnChangeSecurityType();

		/* init summary */
		/* wan */
		var wantype = "N/A";
		switch (XG(this.inet1p+"/addrtype"))
		{
		case "ipv4":
			if		(XG(this.inet1p+"/ipv4/static")=="0")	wantype = "<?echo i18n("Dynamic IP (DHCP)");?>";
			else											wantype = "<?echo i18n("Static IP");?>";
			break;
		case "ppp4":
			if		(XG(this.inet1p+"/ppp4/over")=="eth")	wantype = "<?echo i18n("PPPoE");?>";
			else if (XG(this.inet1p+"/ppp4/over")=="pptp")	wantype = "<?echo i18n("PPTP");?>";
			else if (XG(this.inet1p+"/ppp4/over")=="l2tp")	wantype = "<?echo i18n("L2TP");?>";
			break;
		}
		/* wlan */
		var sectype	= "<?echo i18n("N/A");?>";
		var nwkey	= "";
		switch (XG(this.wifip+"/authtype"))
		{
		case "OPEN":
			if (XG(this.wifip+"/encrtype")=="NONE")
			{
				sectype = "<?echo i18n("None");?>";
			}
			else
			{
				sectype = "<?echo i18n("OPEN");?>";
				var index = XG(this.wifip+"/nwkey/wep/defkey");
				nwkey = XG(this.wifip+"/nwkey/wep/key:"+index);
			}
			break;
		case "SHARED":
			sectype = "<?echo i18n("Shared Key");?>";
			var index = XG(this.wifip+"/nwkey/wep/defkey");
			nwkey = XG(this.wifip+"/nwkey/wep/key:"+index);
			break;
		case "WPA":		sectype = "<?echo i18n("WPA - Enterprise");?>";					break;
		case "WPA2":	sectype = "<?echo i18n("WPA2 - Enterprise");?>";				break;
		case "WPA2+":	sectype = "<?echo i18n("Auto (WPA or WPA2) - Enterprise");?>";	break;
		case "WPAPSK":
			sectype	= "<?echo i18n("WPA - Personal");?>";
			nwkey	= XG(this.wifip+"/nwkey/psk/key");
			break;
		case "WPA2PSK":
			sectype	= "<?echo i18n("WPA2 - Personal");?>";
			nwkey	= XG(this.wifip+"/nwkey/psk/key");
			break;
		case "WPA+2PSK":
			sectype	= "<?echo i18n("Auto (WPA or WPA2) - Personal");?>";
			nwkey	= XG(this.wifip+"/nwkey/psk/key");
			break;
		}
		OBJ("sum_wanmode").innerHTML = wantype;
		OBJ("sum_ssid").innerHTML = XG(this.wifip+"/ssid");
		OBJ("sum_sec").innerHTML = sectype;
		OBJ("s_key").style.display = (nwkey === "") ? "none" : "block";
		OBJ("sum_key").innerHTML = nwkey;
		OBJ("l_key").style.display = (nwkey.length > 50) ? "block" : "none";
		return true;
	},
	PreWANSettings: function()
	{
		var type = this.wanTypes[this.currentWanType];
		XD(this.inet1p+"/ipv4");
		XD(this.inet1p+"/ppp4");
		XS(this.inf1p+"/lowerlayer", "");
		XS(this.inf1p+"/upperlayer", "");
		XS(this.inf2p+"/lowerlayer", "");
		XS(this.inf2p+"/upperlayer", "");
		XS(this.inf2p+"/active", "0");
		XS(this.inf2p+"/nat", "");
		XS(this.inf2p+"/defaultroute", "0");
		switch (type)
		{
		case "DHCP":
			/////////////////////////// prepare DHCP settings ///////////////////////////
			XS(this.inet1p+"/addrtype", "ipv4");
			XS(this.inet1p+"/ipv4/static", "0");
			SetDNSAddress(this.inet1p+"/ipv4/dns", "", "");
			OBJ("sum_wanmode").innerHTML = "<?echo i18n("Dynamic IP (DHCP)");?>";
			break;
		case "PPPoE":
			/////////////////////////// prepare PPPoE settings ///////////////////////////
			XS(this.inet1p+"/addrtype", "ppp4");
			XS(this.inet1p+"/ppp4/over", "eth");
			XS(this.inet1p+"/ppp4/static", "0");
			XS(this.inet1p+"/ppp4/username", OBJ("wiz_pppoe_usr").value);
			XS(this.inet1p+"/ppp4/password", OBJ("wiz_pppoe_passwd").value);
			SetDNSAddress(this.inet1p+"/ppp4/dns", "", "");
			OBJ("sum_wanmode").innerHTML = "<?echo i18n("PPPoE");?>";
			break;
		case "PPTP":
			/////////////////////////// prepare PPTP settings ///////////////////////////
			XS(this.inf2p+"/active", "1");
			XS(this.inet1p+"/addrtype", "ppp4");
			XS(this.inet1p+"/ppp4/over", "pptp");
			XS(this.inet1p+"/ppp4/static", "0");
			XS(this.inet2p+"/addrtype", "ipv4");
			XS(this.inet2p+"/ipv4/static", "0");
			XS(this.inet1p+"/ppp4/pptp/server", OBJ("wiz_pptp_svr").value);
			XS(this.inet1p+"/ppp4/username", OBJ("wiz_pptp_usr").value);
			XS(this.inet1p+"/ppp4/password", OBJ("wiz_pptp_passwd").value);
			XS(this.inf1p+"/lowerlayer", "WAN-2");
			XS(this.inf2p+"/upperlayer", "WAN-1");
			SetDNSAddress(this.inet1p+"/ppp4/dns", "", "");
			OBJ("sum_wanmode").innerHTML = "<?echo i18n("PPTP");?>";
			break;
		case "L2TP":
			/////////////////////////// prepare L2TP settings ///////////////////////////
			XS(this.inf2p+"/active", "1");
			XS(this.inet1p+"/addrtype", "ppp4");
			XS(this.inet1p+"/ppp4/over", "l2tp");
			XS(this.inet1p+"/ppp4/static", "0");
			XS(this.inet2p+"/addrtype", "ipv4");
			XS(this.inet2p+"/ipv4/static", "0");
			XS(this.inet1p+"/ppp4/l2tp/server", OBJ("wiz_l2tp_svr").value);
			XS(this.inet1p+"/ppp4/username", OBJ("wiz_l2tp_usr").value);
			XS(this.inet1p+"/ppp4/password", OBJ("wiz_l2tp_passwd").value);
			XS(this.inf1p+"/lowerlayer", "WAN-2");
			XS(this.inf2p+"/upperlayer", "WAN-1");
			SetDNSAddress(this.inet1p+"/ppp4/dns", "", "");
			OBJ("sum_wanmode").innerHTML = "<?echo i18n("L2TP");?>";
			break;
		case "STATIC":
			/////////////////////////// prepare STATIC IP settings ///////////////////////////
			XS(this.inet1p+"/addrtype", "ipv4");
			XS(this.inet1p+"/ipv4/static", "1");
			XS(this.inet1p+"/ipv4/ipaddr", OBJ("wiz_static_ipaddr").value);
			XS(this.inet1p+"/ipv4/mask", COMM_IPv4MASK2INT(OBJ("wiz_static_mask").value));
			XS(this.inet1p+"/ipv4/gateway", OBJ("wiz_static_gw").value);
			SetDNSAddress(this.inet1p+"/ipv4/dns", OBJ("wiz_static_dns1").value, OBJ("wiz_static_dns2").value);
			OBJ("sum_wanmode").innerHTML = "<?echo i18n("Static IP");?>";
			break;
		}
		if (type=="DHCP"||type=="STATIC")
		{
			/////////////////////////// prepare IPv4 hidden nodes ///////////////////////////
			XS(this.inet1p+"/ipv4/mtu", OBJ("ipv4_mtu").value);
		}
		else if (type="PPPoE"||type=="PPTP"||type=="L2TP")
		{
			/////////////////////////// prepare PPPv4 hidden nodes ///////////////////////////
			if (type != "PPPoE" && ( OBJ("ppp4_mtu").value < 576 || OBJ("ppp4_mtu").value > 1400 ) ) XS(this.inet1p+"/ppp4/mtu", "1400");  
			else XS(this.inet1p+"/ppp4/mtu", OBJ("ppp4_mtu").value);
			XS(this.inet1p+"/ppp4/mru", OBJ("ppp4_mru").value);
			XS(this.inet1p+"/ppp4/dialup/idletimeout", OBJ("ppp4_mode").value);
			XS(this.inet1p+"/ppp4/dialup/mode", "auto");
		}
		
		return true;
	},
	PreWLANSettings: function()
	{
		var security = "NONE";
		var key = "";
		XS(this.phyinf+"/active", "1");
		XS(this.phyinf+"/schedule", "");
		XS(this.wifip+"/ssid", OBJ("ssid").value);
		if (OBJ("security_type").value=="wpa")
		{
			security = "<?echo i18n("Auto (WPA or WPA2) - Personal");?>";
			XS(this.wifip+"/authtype", "WPA+2PSK");
			XS(this.wifip+"/encrtype", "TKIP+AES");
			XS(this.wifip+"/nwkey/psk/passphrase", "");
			XS(this.wifip+"/nwkey/psk/key", OBJ("wpapsk").value);
			key = OBJ("wpapsk").value;
		}
		else
		{
			XS(this.wifip+"/authtype", "OPEN");
			XS(this.wifip+"/encrtype", "NONE");
		}
		XS(this.wifip+"/wps/configured", "1");
		OBJ("sum_ssid").innerHTML = OBJ("ssid").value;
		OBJ("sum_sec").innerHTML = security;
		OBJ("s_key").style.display = "block";
		if (security=="NONE")
		{
			OBJ("s_key").style.display = "none";
			OBJ("l_key").style.display = "none";
		}
		else if (key.length > 50)
		{
			OBJ("sum_key").innerHTML = "";
			OBJ("l_key").innerHTML = key;
			OBJ("l_key").style.display = "block";
		}
		else
		{
			OBJ("sum_key").innerHTML = key;
			OBJ("l_key").style.display = "none";
		}

		return true;
	},
	ShowStatus: function()
	{
		/* hide all */
		for (i=0; i<this.stages.length; i++) OBJ(this.stages[i]).style.display = "none";
		/* show status div */
		OBJ("easy_summary").style.display = "block";
		this.SetupSummaryDiv("curr_setting");
	},
	ShowCurrentStage: function()
	{
		var i = 0;
		for (i=0; i<this.wanTypes.length; i++)
		{
			OBJ(this.wanTypes[i]).style.display = "none";
		}
		for (i=0; i<this.stages.length; i++)
		{
			if (i==this.currentStage)
			{
				OBJ(this.stages[i]).style.display = "block";
				if (this.stages[this.currentStage]=="easy_wan")
				{
					OBJ(this.wanTypes[this.currentWanType]).style.display = "block";
				}
			}
			else
				OBJ(this.stages[i]).style.display = "none";
		}

		if (this.currentStage==0)
			SetButtonDisabled("b_pre", true);
		else
			SetButtonDisabled("b_pre", false);

		if (this.currentStage==this.stages.length-1)
		{
			SetButtonDisabled("b_next", true);
			SetButtonDisabled("b_send", false);
		}
		else
		{
			SetButtonDisabled("b_next", false);
			SetButtonDisabled("b_send", true);
		}
	},
	SetStage: function(offset)
	{
		var length = this.stages.length;
		switch (offset)
		{
		case 1:
			if (this.currentStage < length-1)
				this.currentStage += 1;
			break;
		case -1:
			if (this.currentStage > 0)
				this.currentStage -= 1;
			break;
		}
	},
	OnClickPre: function()
	{
		this.SetStage(-1);
		this.ShowCurrentStage();
	},
	OnClickNext: function()
	{
		var stage = this.stages[this.currentStage];

		switch (stage)
		{
		case "easy_wan":
			this.PreWANSettings();
			CheckWANSettings(this.wanTypes[this.currentWanType]);
			break;
		case "easy_wlan":
			this.PreWLANSettings();
			CheckWLANSettings();
			break;
		case "easy_main":
			if (OBJ("en_manual").checked)
			{
				self.location.href = "./bsc_internet.php";
				break;
			}
		default:
			this.SetStage(1);
			this.ShowCurrentStage();
		}
	},
	OnClickCancel: function()
	{
		if (confirm("<?echo i18n("Do you want to abandon all changes you made to this wizard and logout?");?>"))
		{
			BODY.Logout();
		}
	},
	OnChangeWanType: function(type)
	{
		for (var i=0; i<this.wanTypes.length; i++)
		{
			if (this.wanTypes[i]==type)
				this.currentWanType = i;
		}
		this.ShowCurrentStage();
	},
	OnChangeSecurityType: function()
	{
		switch (OBJ("security_type").value)
		{
		case "none":
			OBJ("wpa").style.display = "none";
			break;
		case "wpa":
			OBJ("wpa").style.display = "block";
		}
	},
	GetWanType: function()
	{
		var addrtype = XG(this.inet1p+"/addrtype");
		var type = null;
		if (this.isFreset) type = "<?echo query("/device/features/easysetup/defwantype");?>";
		else
		{
			switch (addrtype)
			{
			case "ipv4":
				if (XG(this.inet1p+"/ipv4/static")=="0")
					type = "DHCP";
				else
					type = "STATIC";
				break;
			case "ppp4":
				if (XG(this.inet1p+"/ppp4/over")=="eth")
					type = "PPPoE";
				else if (XG(this.inet1p+"/ppp4/over")=="pptp")
					type = "PPTP";
				else if (XG(this.inet1p+"/ppp4/over")=="l2tp")
					type = "L2TP";
				break;
			default:
				BODY.ShowAlert("Internal Error!!");
			}
		}
		for (var i=0; i<this.wanTypes.length; i++)
		{
			if (this.wanTypes[i]==type)	this.currentWanType = i;
		}
	}
}

function SetButtonDisabled(name, disable)
{
	var button = document.getElementsByName(name);
	for (i=0; i<button.length; i++)
	{
		button[i].disabled = disable;
	}
}
function ResAddress(address)
{
	if (address=="")
		return "0.0.0.0";
	else if (address=="0.0.0.0")
		return "";
	else
		return address;
}
function SetDNSAddress(path, dns1, dns2)
{
	var cnt = 0;
	var dns = new Array (false, false);
	if (dns1!="0.0.0.0"&&dns1!="") {dns[0] = true; cnt++;}
	if (dns2!="0.0.0.0"&&dns2!="") {dns[1] = true; cnt++;}
	XA(path+"/count", cnt);
	if (dns[0]) XA(path+"/entry", dns1);
	if (dns[1]) XA(path+"/entry", dns2);
}

function CheckWANSettings(type)
{
	switch (type)
	{
	case "STATIC":
		if ((OBJ("wiz_static_dns1").value==="") || (OBJ("wiz_static_dns1").value==="0.0.0.0"))
		{
			BODY.ShowAlert("<?echo i18n("Invalid Primary DNS address .");?>");
			return false;
		}
		break;
	case "PPPoE":
	case "PPTP":
	case "L2TP":
		if (OBJ("wiz_"+type.toLowerCase()+"_passwd").value!=
			OBJ("wiz_"+type.toLowerCase()+"_passwd2").value)
		{
			BODY.ShowAlert("<?echo i18n("Please make the two passwords the same and try again.");?>");
			return false;
		}
		break;
	default:
	}

	PXML.CheckModule("INET.WAN-1", null, "ignore", "ignore");
	PXML.CheckModule("INET.WAN-2", null, "ignore", "ignore");
	PXML.IgnoreModule("WIFI.WLAN-1");
	CallHedwig(PXML.doc);
}
function CheckWLANSettings()
{
	PXML.CheckModule("WIFI.WLAN-1", null, "ignore", "ignore");
	PXML.IgnoreModule("INET.WAN-1");
	PXML.IgnoreModule("INET.WAN-2");
	CallHedwig(PXML.doc);
}
function CallHedwig(doc)
{
	COMM_CallHedwig(doc, 
		function (xml)
		{
			switch (xml.Get("/hedwig/result"))
			{
			case "OK":
				PAGE.SetStage(1);
				PAGE.ShowCurrentStage();
				break;
			case "FAILED":
				BODY.ShowAlert(xml.Get("/hedwig/message"));
				break;
			}
		}
	);
}
</script>
