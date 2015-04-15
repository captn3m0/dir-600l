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
		this.wifip = PXML.FindModule("WIFI.WLAN-1");
		if (!this.wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.wifip = GPBT(this.wifip+"/wifi", "entry", "uid", "WIFI-1", false);
		OBJ("en_wps").checked = COMM_ToBOOL(XG(this.wifip+"/wps/enable"));
		if (XG(this.wifip+"/wps/pin")=="")
			this.curpin = OBJ("pin").innerHTML = this.defpin;
		else
			this.curpin = OBJ("pin").innerHTML = XG(this.wifip+"/wps/pin");

		if (XG(this.wifip+"/wps/enable") === "1")
			OBJ("st_wps").innerHTML = "<?echo i18n("Enabled");?>";
		else
			OBJ("st_wps").innerHTML = "<?echo i18n("Disabled");?>";
			
		if (XG(this.wifip+"/wps/configured") === "0")
			OBJ("st_config").innerHTML = "<?echo i18n("Not Configured");?>";
		else
			OBJ("st_config").innerHTML = "<?echo i18n("Configured");?>";

		this.OnClickEnWPS();
		return true;
	},
	PreSubmit: function()
	{
		XS(this.wifip+"/wps/locksecurity", (OBJ("lock_security").checked)? "1":"0");
		XS(this.wifip+"/wps/enable", (OBJ("en_wps").checked)? "1":"0");
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function()
	{
		if (OBJ("pin").innerHTML!=this.curpin)
		{
			OBJ("mainform").setAttribute("modified", "true");
			XS(this.wifip+"/wps/pin", OBJ("pin").innerHTML);
		}
	},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	wifip: null,
	defpin: "<?echo query("/runtime/devdata/pin");?>",
	curpin: null,
	OnClickEnWPS: function()
	{
		if (OBJ("en_wps").checked)
		{
			if (XG(this.wifip+"/wps/configured")=="0")
			{
				OBJ("reset_cfg").disabled = true;
				OBJ("lock_security").disabled = true;
				OBJ("lock_security").checked = false;
			}
			else
			{
				OBJ("reset_cfg").disabled = false;
				OBJ("lock_security").disabled = false;
				if (XG(this.wifip+"/wps/locksecurity") == "")
					OBJ("lock_security").checked = true;
				else
					OBJ("lock_security").checked = COMM_ToBOOL(XG(this.wifip+"/wps/locksecurity"));
			}
			OBJ("reset_pin").disabled	= false;
			OBJ("gen_pin").disabled		= false;
			OBJ("go_wps").disabled		= false;
		}
		else
		{
			OBJ("reset_cfg").disabled	= true;
			OBJ("reset_pin").disabled	= true;
			OBJ("gen_pin").disabled		= true;
			OBJ("go_wps").disabled		= true;
			OBJ("lock_security").disabled	= true;
		}
	},
	OnClickResetCfg: function()
	{
		if (confirm("<?echo i18n("Are you sure you want to reset the device to Unconfigured?")."\\n".
					i18n("This will cause wireless settings to be lost.");?>"))
		{
			OBJ("mainform").setAttribute("modified", "true");
			XS(this.wifip+"/ssid",			"dlink"	);
			XS(this.wifip+"/authtype",		"OPEN"	);
			XS(this.wifip+"/encrtype",		"NONE"	);
			XS(this.wifip+"/wps/configured","0"		);
			OBJ("lock_security").checked = false;
			BODY.OnSubmit();
		}
	},
	OnClickResetPIN: function()
	{
		OBJ("pin").innerHTML = this.defpin;
	},
	OnClickGenPIN: function()
	{
		var pin = "";
		var sum = 0;
		var check_sum = 0;
		var r = 0;
		for(var i=0; i<7; i++)
		{
			r = (Math.floor(Math.random()*9));
			pin += r;
			sum += parseInt(r, [10]) * (((i%2)==0) ? 3:1);
		}
		check_sum = (10-(sum%10))%10;
		pin += check_sum;
		OBJ("pin").innerHTML = pin;
	}
}
</script>
