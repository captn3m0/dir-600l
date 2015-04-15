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
		COMM_SetSelectValue(OBJ("wlan_mode"), XG(this.phyinf+"/wlmode"));
		COMM_SetSelectValue(OBJ("bw"), XG(this.phyinf+"/dot11n/bandwidth"));
		SetRadioValue("preamble", XG(this.phyinf+"/preamble"));
//		SetRadioValue("cts", XG(this.phyinf+"/ctsmode"));
		OBJ("beacon").value	= XG(this.phyinf+"/beacon");
		OBJ("rts").value	= XG(this.phyinf+"/rtsthresh");
		OBJ("frag").value	= XG(this.phyinf+"/fragthresh");
		OBJ("dtim").value	= XG(this.phyinf+"/dtim");
		OBJ("sgi").checked	= COMM_EqNUMBER(XG(this.phyinf+"/dot11n/guardinterval"), 400);
		this.OnChangeWLMode();
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
		XS(this.phyinf+"/wlmode",		OBJ("wlan_mode").value);
		if (/n/.test(OBJ("wlan_mode").value))
		{
			XS(this.phyinf+"/dot11n/bandwidth",		OBJ("bw").value);
			XS(this.phyinf+"/dot11n/guardinterval",	(OBJ("sgi").checked)? "400":"800");
			XS(this.phyinf+"/wmm/enable",			"1");
		}
		return true;
	},
	OnChangeWLMode: function()
	{
		if (/n/.test(OBJ("wlan_mode").value))
		{
			OBJ("bw").disabled	= false;
			OBJ("sgi").disabled	= false;
		}
		else
		{
			OBJ("bw").disabled	= true;
			OBJ("sgi").disabled	= true;
		}
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
</script>
