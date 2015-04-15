<?include "/htdocs/phplib/phyinf.php";?>
<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "WIFI.WLAN-1,RUNTIME.PHYINF.WLAN-1",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		if (!this.Initial()) return false;
		return true;
	},
	PreSubmit: function()
	{
		if (!this.SaveXML()) return null;
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	wifip: null,
	phyinf: null,
	runtime_phyinf: null,
	sec_type: null,
	wlanMode: null,
	bandWidth: null,
	shortGuard: null,
	wps: true,
	Initial: function()
	{
		this.phyinf = this.wifip = PXML.FindModule("WIFI.WLAN-1");
		this.runtime_phyinf = PXML.FindModule("RUNTIME.PHYINF.WLAN-1");
		if (!this.wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.wifip = GPBT(this.wifip+"/wifi", "entry", "uid", "WIFI-1", false);
		this.phyinf += "/phyinf";
		this.runtime_phyinf += "/runtime/phyinf";
		OBJ("en_wifi").checked = COMM_ToBOOL(XG(this.phyinf+"/active"));
		<? if($FEATURE_NOSCH!="1")echo 'COMM_SetSelectValue(OBJ("sch"), XG(this.phyinf+"/schedule"));\n'; ?>
		OBJ("ssid").value = XG(this.wifip+"/ssid");
		OBJ("auto_ch").checked = (XG(this.phyinf+"/media/channel")=="0")? true : false;
		if (OBJ("auto_ch").checked)
			COMM_SetSelectValue(OBJ("channel"), XG(this.runtime_phyinf+"/media/channel"));
		else
			COMM_SetSelectValue(OBJ("channel"), XG(this.phyinf+"/media/channel"));
		COMM_SetSelectValue(OBJ("wlan_mode"), XG(this.phyinf+"/media/wlmode"));
		COMM_SetSelectValue(OBJ("bw"), XG(this.phyinf+"/media/dot11n/bandwidth"));
		this.shortGuard	= XG(this.phyinf+"/media/dot11n/guardinterval");
		
		/*if (this.wlanMode=="n")
		{
			this.bandWidth	= XG(this.phyinf+"/media/dot11n/bandwidth");
			this.shortGuard	= XG(this.phyinf+"/media/dot11n/guardinterval");
			DrawTxRateList(this.bandWidth, this.shortGuard);
			var rate = XG(this.phyinf+"/media/dot11n/mcs/index");
			if (rate=="") rate = "-1";
			COMM_SetSelectValue(OBJ("txrate"), rate);
		}*/
		OBJ("en_wmm").checked = COMM_ToBOOL(XG(this.phyinf+"/media/wmm/enable"));
		//if (/n/.test(this.wlanMode))
		//	OBJ("en_wmm").disabled = true;
		OBJ("suppress").checked = COMM_ToBOOL(XG(this.wifip+"/ssidhidden"));
		this.OnChangeWLMode();
		
		if (!OBJ("en_wifi").checked)
			this.sec_type = "";
		else if (XG(this.wifip+"/encrtype")=="WEP")
			this.sec_type = "wep";
		else if (XG(this.wifip+"/authtype")=="WPAPSK" ||
				 XG(this.wifip+"/authtype")=="WPAEAP")
			this.sec_type = "WPA"
		else if (XG(this.wifip+"/authtype")=="WPA2PSK" ||
				 XG(this.wifip+"/authtype")=="WPA2EAP")
			this.sec_type = "WPA2"
		else if (XG(this.wifip+"/authtype")=="WPA+2PSK" ||
				 XG(this.wifip+"/authtype")=="WPA+2EAP")
			this.sec_type = "WPA+2"
		else
			this.sec_type = "";
		COMM_SetSelectValue(OBJ("security_type"), this.sec_type);
		///////////////// initial WEP /////////////////
		var auth = XG(this.wifip+"/authtype");
		var len = (XG(this.wifip+"/nwkey/wep/size")=="")? "64" : XG(this.wifip+"/nwkey/wep/size");
		var defkey = (XG(this.wifip+"/nwkey/wep/defkey")=="")? "1" : XG(this.wifip+"/nwkey/wep/defkey");
		this.wps = COMM_ToBOOL(XG(this.wifip+"/wps/enable"));
		OBJ("auth_type").disabled = this.wps;
		if (auth!="SHARED") auth = "OPEN";
		COMM_SetSelectValue(OBJ("auth_type"),	auth);
		COMM_SetSelectValue(OBJ("wep_key_len"),	len);
		COMM_SetSelectValue(OBJ("wep_def_key"),	defkey);
		for (var i=1; i<5; i++)
			OBJ("wep_"+len+"_"+i).value = XG(this.wifip+"/nwkey/wep/key:"+i);
		///////////////// initial WPA /////////////////
		var cipher = XG(this.wifip+"/encrtype");
		var type = null;
		switch (XG(this.wifip+"/authtype"))
		{
		case "WPA":
		case "WPA2":
		case "WPA+2":
			type = "eap";
			break;
		default:
			type = "psk";
		}
		switch (cipher)
		{
		case "TKIP":
		case "AES":
			break;
		default:
			cipher = "TKIP+AES";
		}
		COMM_SetSelectValue(OBJ("cipher_type"), cipher);
		COMM_SetSelectValue(OBJ("psk_eap"), type);

		OBJ("wpapsk").value		= XG(this.wifip+"/nwkey/psk/key");
		OBJ("srv_ip").value		= XG(this.wifip+"/nwkey/eap/radius");
		OBJ("srv_port").value	= (XG(this.wifip+"/nwkey/eap/port")==""?"1812":XG(this.wifip+"/nwkey/eap/port"));
		OBJ("srv_sec").value	= XG(this.wifip+"/nwkey/eap/secret");
		this.OnChangeSecurityType();
		this.OnChangeWEPKey();
		this.OnChangeWPAAuth();
		this.OnClickEnWLAN();
		this.OnClickEnAutoChannel();
		return true;
	},
	SaveXML: function()
	{
		if (OBJ("en_wifi").checked)
		{
			XS(this.phyinf+"/active", "1");
		}
		else
		{
			XS(this.phyinf+"/active", "0");
			return true;
		}
		<? if ($FEATURE_NOSCH!="1")echo 'XS(this.phyinf+"/schedule",	OBJ("sch").value);\n';?>
		XS(this.wifip+"/ssid",		OBJ("ssid").value);
		if (OBJ("auto_ch").checked)
			XS(this.phyinf+"/media/channel", "0");
		else
			XS(this.phyinf+"/media/channel", OBJ("channel").value);
		if (OBJ("txrate").value=="-1")
		{
			XS(this.phyinf+"/media/dot11n/mcs/auto", "1");
			XS(this.phyinf+"/media/dot11n/mcs/index", "");
		}
		else
		{
			XS(this.phyinf+"/media/dot11n/mcs/auto", "0");
			XS(this.phyinf+"/media/dot11n/mcs/index", OBJ("txrate").value);
		}
		XS(this.phyinf+"/media/wlmode",		OBJ("wlan_mode").value);
		XS(this.phyinf+"/media/dot11n/bandwidth",		OBJ("bw").value);
		XS(this.phyinf+"/media/wmm/enable",	SetBNode(OBJ("en_wmm").checked));
		XS(this.wifip+"/ssidhidden",		SetBNode(OBJ("suppress").checked));
		if (COMM_EqBOOL(OBJ("ssid").getAttribute("modified"),true)||COMM_EqBOOL(OBJ("security_type").getAttribute("modified"),true))
			XS(this.wifip+"/wps/configured", "1");
		if (OBJ("security_type").value=="wep")
		{
			if (OBJ("auth_type").value=="SHARED")
				XS(this.wifip+"/authtype", "SHARED");
			else
				XS(this.wifip+"/authtype", "OPEN");
			XS(this.wifip+"/encrtype",			"WEP");
			XS(this.wifip+"/nwkey/wep/size",	"");
			XS(this.wifip+"/nwkey/wep/ascii",	"");
			XS(this.wifip+"/nwkey/wep/defkey",	OBJ("wep_def_key").value);
			for (var i=1, len=OBJ("wep_key_len").value; i<5; i++)
			{
				if (i==OBJ("wep_def_key").value)
					XS(this.wifip+"/nwkey/wep/key:"+i, OBJ("wep_"+len+"_"+i).value);
				else
					XS(this.wifip+"/nwkey/wep/key:"+i, "");
			}
		}
		else if (OBJ("security_type").value=="WPA"  ||
				 OBJ("security_type").value=="WPA2" ||
				 OBJ("security_type").value=="WPA+2")
		{
			XS(this.wifip+"/encrtype", OBJ("cipher_type").value);
			if (OBJ("psk_eap").value=="psk")
			{
				XS(this.wifip+"/authtype",				OBJ("security_type").value+"PSK");
				XS(this.wifip+"/nwkey/psk/passphrase",	"");
				XS(this.wifip+"/nwkey/psk/key",			OBJ("wpapsk").value);
			}
			else
			{
				XS(this.wifip+"/authtype",			OBJ("security_type").value);
				XS(this.wifip+"/nwkey/eap/radius",	OBJ("srv_ip").value);
				XS(this.wifip+"/nwkey/eap/port",	OBJ("srv_port").value);
				XS(this.wifip+"/nwkey/eap/secret",	OBJ("srv_sec").value);
			}
		}
		else
		{
			XS(this.wifip+"/authtype", "OPEN");
			XS(this.wifip+"/encrtype", "NONE");
		}
		PXML.IgnoreModule("RUNTIME.PHYINF.WLAN-1");
		return true;
	},
	OnClickEnWLAN: function()
	{
		if (AUTH.AuthorizedGroup >= 100) return;
		if (OBJ("en_wifi").checked)
		{
			<?
			if ($FEATURE_NOSCH!="1")
			{
				echo 'OBJ("sch").disabled		= false;\n';
				echo 'OBJ("go2sch").disabled	= false;\n';
			}
			?>
			OBJ("ssid").disabled	= false;
			OBJ("auto_ch").disabled	= false;
			if (!OBJ("auto_ch").checked) OBJ("channel").disabled = false;
			OBJ("txrate").disabled	= false;
			if (/n/.test(OBJ("wlan_mode").value)) OBJ("en_wmm").disabled = true;
			OBJ("suppress").disabled= false;
			OBJ("security_type").disabled= false;
			COMM_SetSelectValue(OBJ("security_type"), this.sec_type);
			this.OnChangeSecurityType();
		}
		else
		{
			<?
			if ($FEATURE_NOSCH!="1")
			{
				echo 'OBJ("sch").disabled		= true;\n';
				echo 'OBJ("go2sch").disabled	= true;\n';
			}
			?>
			OBJ("ssid").disabled	= true;
			OBJ("auto_ch").disabled	= true;
			OBJ("channel").disabled	= true;
			OBJ("txrate").disabled	= true;
			OBJ("en_wmm").disabled = true;
			OBJ("suppress").disabled= true;
			OBJ("security_type").disabled= true;
			this.sec_type = OBJ("security_type").value;
			COMM_SetSelectValue(OBJ("security_type"), "");
			this.OnChangeSecurityType();
		}
	},
	OnClickEnAutoChannel: function()
	{
		if (OBJ("auto_ch").checked || !OBJ("en_wifi").checked)
			OBJ("channel").disabled = true;
		else
			OBJ("channel").disabled = false;
	},
	OnChangeSecurityType: function()
	{
		switch (OBJ("security_type").value)
		{
		case "":
			OBJ("wep").style.display = "none";
			OBJ("wpa").style.display = "none";
			OBJ("pad").style.display = "block";
			break;
		case "wep":
			OBJ("wep").style.display = "block";
			OBJ("wpa").style.display = "none";
			OBJ("pad").style.display = "none";
			break;
		case "WPA":
		case "WPA2":
		case "WPA+2":
			OBJ("wep").style.display = "none";
			OBJ("wpa").style.display = "block";
			OBJ("pad").style.display = "none";
		}
	},
	OnChangeWEPKey: function()
	{
		var no = S2I(OBJ("wep_def_key").value) - 1;
		switch (OBJ("wep_key_len").value)
		{
		case "64":
			OBJ("wep_64").style.display = "block";
			OBJ("wep_128").style.display = "none";
			SetDisplayStyle(null, "wepkey_64", "none");
			document.getElementsByName("wepkey_64")[no].style.display = "inline";
			break;
		case "128":
			OBJ("wep_64").style.display = "none";
			OBJ("wep_128").style.display = "block";
			SetDisplayStyle(null, "wepkey_128", "none");
			document.getElementsByName("wepkey_128")[no].style.display = "inline";
		}
	},
	OnChangeWPAAuth: function()
	{
		switch (OBJ("psk_eap").value)
		{
		case "psk":
			SetDisplayStyle("div", "psk", "block");
			SetDisplayStyle("div", "eap", "none");
			break;
		case "eap":
			SetDisplayStyle("div", "psk", "none");
			SetDisplayStyle("div", "eap", "block");
		}
	},
	OnChangeWLMode: function()
	{
		var phywlan = "";
		phywlan  = PXML.FindModule("WIFI.WLAN-1"); 
		phywlan = GPBT(phywlan, "phyinf", "uid","WLAN-1", false);
		if (/n/.test(OBJ("wlan_mode").value))
		{
			this.bandWidth	= XG(phywlan+"/media/dot11n/bandwidth");
			COMM_SetSelectValue(OBJ("bw"), this.bandWidth);
			OBJ("bw").disabled	= false;
			OBJ("en_wmm").checked = true;
			OBJ("en_wmm").disabled = true;
		}
		else
		{
			OBJ("bw").disabled	= true;
			OBJ("en_wmm").disabled = false;
		}
		DrawTxRateList(OBJ("bw").value, this.shortGuard, OBJ("wlan_mode").value);
		if (OBJ("wlan_mode").value === "n")
		{
			var rate = XG(phywlan+"/media/dot11n/mcs/index");
			if (rate=="") rate = "-1";
			COMM_SetSelectValue(OBJ("txrate"), rate);
		}
		DrawSecurityList(OBJ("wlan_mode").value);
		this.OnChangeSecurityType();
	},
	OnChangeBW: function()
	{
		if(OBJ("wlan_mode").value==="n")	DrawTxRateList(OBJ("bw").value, this.shortGuard, OBJ("wlan_mode").value);
	}
}

function SetBNode(value)
{
	if (COMM_ToBOOL(value))
		return "1";
	else
		return "0";
}

function SetDisplayStyle(tag, name, style)
{
	if (tag)	var obj = GetElementsByName_iefix(tag, name);
	else		var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		obj[i].style.display = style;
	}
}
function GetElementsByName_iefix(tag, name)
{
	var elem = document.getElementsByTagName(tag);
	var arr = new Array();
	for(i = 0,iarr = 0; i < elem.length; i++)
	{
		att = elem[i].getAttribute("name");
		if(att == name)
		{
			arr[iarr] = elem[i];
			iarr++;
		}
	}
	return arr;
}

function DrawTxRateList(bw, sgi, wlan_mode)
{
	var listOptions = null;
	var cond = bw+":"+sgi;
	switch(cond)
	{
	case "20:800":
		listOptions = new Array("0 - 6.5","1 - 13.0","2 - 19.5","3 - 26.0","4 - 39.0","5 - 52.0","6 - 58.5","7 - 65.0"<?
						$p = XNODE_getpathbytarget("/runtime", "phyinf", "uid", "WLAN-1", 0);
						$ms = query($p."/media/multistream");
						if ($ms != "1T1R")
							echo ',"8 - 13.0","9 - 26.0","10 - 39.0","11 - 52.0","12 - 78.0","13 - 104.0","14 - 117.0","15 - 130.0"';
						?>);
		break;
	case "20:400":
		listOptions = new Array("0 - 7.2","1 - 14.4","2 - 21.7","3 - 28.9","4 - 43.3","5 - 57.8","6 - 65.0","7 - 72.0"<?
						if ($ms != "1T1R")
							echo ',"8 - 14.444","9 - 28.889","10 - 43.333","11 - 57.778","12 - 86.667","13 - 115.556","14 - 130.000","15 - 144.444"';
						?>);
		break;
	case "20+40:800":
		listOptions = new Array("0 - 13.5","1 - 27.0","2 - 40.5","3 - 54.0","4 - 81.0","5 - 108.0","6 - 121.5","7 - 135.0"<?
						if ($ms != "1T1R")
							echo ',"8 - 27.0","9 - 54.0","10 - 81.0","11 - 108.0","12 - 162.0","13 - 216.0","14 - 243.0","15 - 270.0"';
						?>);
		break;
	case "20+40:400":
		listOptions = new Array("0 - 15.0","1 - 30.0","2 - 45.0","3 - 60.0","4 - 90.0","5 - 120.0","6 - 135.0","7 - 150.0"<?
						if ($ms != "1T1R")
							echo ',"8 - 30.0","9 - 60.0","10 - 90.0","11 - 120.0","12 - 180.0","13 - 240.0","14 - 270.0","15 - 300.0"';
						?>);
		break;
	}

	for(var idx=1; idx<OBJ("txrate").length;)
	{
		OBJ("txrate").remove(idx);
	}
	for(var idx=0; idx<listOptions.length && wlan_mode==="n"; idx++)
	{
		var item = document.createElement("option");
		item.value = idx;
		item.text = listOptions[idx];
		try		{ OBJ("txrate").add(item, null); }
		catch(e){ OBJ("txrate").add(item); }
	}
}
function DrawSecurityList(wlan_mode)
{
	var security_list = null;
	var cipher_list = null;
	if (wlan_mode === "n")
	{
		security_list = ['WPA', '<?echo i18n("Enable WPA Wireless Security (enhanced)");?>',
						 'WPA2', '<?echo i18n("Enable WPA2 Wireless Security (enhanced)");?>',
						 'WPA+2', '<?echo i18n("Enable WPA/WPA2 Wireless Security (enhanced)");?>'];
		cipher_list = ['AES'];
	}
	else
	{
		security_list = ['wep', '<?echo i18n("Enable WEP Wireless Security (basic)");?>',
						 'WPA', '<?echo i18n("Enable WPA Wireless Security (enhanced)");?>',
						 'WPA2', '<?echo i18n("Enable WPA2 Wireless Security (enhanced)");?>',
						 'WPA+2', '<?echo i18n("Enable WPA/WPA2 Wireless Security (enhanced)");?>'];
		cipher_list = ['TKIP+AES','TKIP','AES'];
	}
	//modify security_type
	var sec_length = OBJ("security_type").length;
	for(var idx=1; idx<sec_length; idx++)
	{
		OBJ("security_type").remove(1);
	}
	for(var idx=0; idx<security_list.length; idx++)
	{
		var item = document.createElement("option");
		item.value = security_list[idx++];
		item.text = security_list[idx];
		try		{ OBJ("security_type").add(item, null); }
		catch(e){ OBJ("security_type").add(item); }
	}
	// modify cipher_type
	var ci_length = OBJ("cipher_type").length;
	for(var idx=0; idx<ci_length; idx++)
	{
		OBJ("cipher_type").remove(0);
	}
	for(var idx=0; idx<cipher_list.length; idx++)
	{
		var item = document.createElement("option");
		item.value = cipher_list[idx];
		if (item.value=="TKIP+AES") item.text = "AUTO(TKIP/AES)";
		else						item.text = cipher_list[idx];
		try		{ OBJ("cipher_type").add(item, null); }
		catch(e){ OBJ("cipher_type").add(item); }
	}
}
</script>
