<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "WIFI.WLAN-1",
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
	phyinf: null,
	wlanmode: null,
	Initial: function()
	{
		this.phyinf = PXML.FindModule("WIFI.WLAN-1");
		if (!this.phyinf)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.phyinf += "/phyinf/media";
		COMM_SetSelectValue(OBJ("tx_power"), XG(this.phyinf+"/txpower"));
		this.wlanmode = XG(this.phyinf+"/wlmode");
		SetRadioValue("preamble", XG(this.phyinf+"/preamble"));
//		SetRadioValue("cts", XG(this.phyinf+"/ctsmode"));
		OBJ("beacon").value	= XG(this.phyinf+"/beacon");
		OBJ("rts").value	= XG(this.phyinf+"/rtsthresh");
		OBJ("frag").value	= XG(this.phyinf+"/fragthresh");
		OBJ("dtim").value	= XG(this.phyinf+"/dtim");
		OBJ("sgi").checked	= COMM_EqNUMBER(XG(this.phyinf+"/dot11n/guardinterval"), 400);

		SetRadioValue("en_bwcoex", XG(this.phyinf+"/dot11n/bw2040coexist"));
		if (XG(this.phyinf+"/dot11n/bandwidth") !="20") RadioStatus("en_bwcoex", "enable");
		else	RadioStatus("en_bwcoex", "disabled");

		if (/n/.test(this.wlanmode))
		{
			OBJ("en_wmm").disabled = true;
			OBJ("en_wmm").checked = true;
		}
		else
		{
			OBJ("en_wmm").disabled = false;
			OBJ("en_wmm").checked = COMM_ToBOOL(XG(this.phyinf+"/wmm/enable"));
			RadioStatus("en_bwcoex", "disabled");
		}
	},
	SaveXML: function()
	{
		if (!TEMP_IsDigit(OBJ("beacon").value))
		{
			BODY.ShowAlert("<?echo i18n("The input beacon interval is invalid.");?>");
			OBJ("beacon").focus();
			return null;
		}
		else if (!TEMP_IsDigit(OBJ("rts").value))
		{
			BODY.ShowAlert("<?echo i18n("The input RTS threshold is invalid.");?>");
			OBJ("rts").focus();
			return null;
		}
		else if (!TEMP_IsDigit(OBJ("frag").value))
		{
			BODY.ShowAlert("<?echo i18n("The input fragmentation is invalid.");?>");
			OBJ("frag").focus();
			return null;
		}
		else if (!TEMP_IsDigit(OBJ("dtim").value))
		{
			BODY.ShowAlert("<?echo i18n("The input DTIM interval is invalid.");?>");
			OBJ("dtim").focus();
			return null;
		}
		XS(this.phyinf+"/txpower",		OBJ("tx_power").value);
		XS(this.phyinf+"/beacon",		OBJ("beacon").value);
		XS(this.phyinf+"/rtsthresh",	OBJ("rts").value);
		XS(this.phyinf+"/fragthresh",	OBJ("frag").value);
		XS(this.phyinf+"/dtim",			OBJ("dtim").value);
		XS(this.phyinf+"/preamble",		GetRadioValue("preamble"));
//		XS(this.phyinf+"/ctsmode",		GetRadioValue("cts"));
		XS(this.phyinf+"/dot11n/guardinterval",	(OBJ("sgi").checked)? "400":"800");
		XS(this.phyinf+"/wmm/enable",     SetBNode(OBJ("en_wmm").checked));
		XS(this.phyinf+"/dot11n/bw2040coexist",       GetRadioValue("en_bwcoex"));

		return true;
	},
	GetIP: function(mac)
	{
		var path = PXML.doc.GetPathByTarget(this.inf, "entry", "mac", mac.toLowerCase(), false);
		return XG(path+"/ipaddr");
	}
}

function GetRadioValue(name)
{
	var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		if (obj[i].checked)	return obj[i].value;
	}
}
function SetRadioValue(name, value)
{
	var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		if (obj[i].value==value)
		{
			obj[i].checked = true;
			break;
		}
	}
}
function RadioStatus(name, stat)
{
	var obj = document.getElementsByName(name);
	for (var i=0; i<obj.length; i++)
	{
		if (stat == "disabled") obj[i].disabled = true;
		else 			obj[i].disabled = false;
	}
}
function SetBNode(value)
{
	if (COMM_ToBOOL(value))
		return "1";
	else
		return "0";
}
</script>
