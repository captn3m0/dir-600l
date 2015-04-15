<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "WIFI.WLAN-1",
	OnLoad: function()
	{
		this.ShowCurrentStage();
	},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result)
	{
		switch (code)
		{
			case "OK":
				self.location.href = "./bsc_wlan_main.php";
				return true;
				break;
			default : 
				this.currentStage--;
				this.ShowCurrentStage();
				return false;
		}
	},
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
	randomkey: null,
	stages: new Array ("wiz_stage_1", "wiz_stage_2", "wiz_stage_3"),
	currentStage: 0,	// 0 ~ this.stages.length
	Initial: function()
	{
		this.wifip = PXML.FindModule("WIFI.WLAN-1");
		this.phyinf = this.wifip;
		if (!this.wifip)
		{
			BODY.ShowAlert("Initial() ERROR!!!");
			return false;
		}
		this.randomkey = RandomHex(64);
		this.wifip = GPBT(this.wifip+"/wifi", "entry", "uid", "WIFI-1", false);
		this.phyinf += "/phyinf";
		OBJ("wiz_ssid").value = XG(this.wifip+"/ssid");
		return true;
	},
	SaveXML: function()
	{
		XS(this.wifip+"/ssid", OBJ("wiz_ssid").value);
		XS(this.wifip+"/ssidhidden", "0");
		XS(this.wifip+"/authtype", "WPA+2PSK");
		XS(this.wifip+"/encrtype", "TKIP+AES");
		XS(this.wifip+"/nwkey/psk/passphrase", "");
		if (OBJ("autokey").checked)
			XS(this.wifip+"/nwkey/psk/key", this.randomkey);
		else
			XS(this.wifip+"/nwkey/psk/key", OBJ("wiz_key").value);
		XS(this.wifip+"/wps/configured", "1");
		XS(this.wifip+"/wps/locksecurity", "1");
		XS(this.phyinf+"/active", "1");
		return true;
	},
	ShowCurrentStage: function()
	{
		for (var i=0; i<this.stages.length; i++)
		{
			if (i==this.currentStage)
				OBJ(this.stages[i]).style.display = "block";
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
			OBJ("mainform").setAttribute("modified", "true");
			UpdateCFG();
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
		this.currentStage += offset;
	},
	OnClickPre: function()
	{
		switch (this.currentStage)
		{
		case 2:
			if (OBJ("autokey").checked)
				this.SetStage(-2);
			else
				this.SetStage(-1);
			this.ShowCurrentStage();
			break;
		default:
			this.SetStage(-1);
			this.ShowCurrentStage();
		}
	},
	OnClickNext: function()
	{
		switch (this.currentStage)
		{
		case 0:
			if (OBJ("wiz_ssid").value=="")
			{
				BODY.ShowAlert("<?echo i18n("The SSID field can not be blank.");?>");
				return;
			}
			if (OBJ("autokey").checked)
				this.SetStage(1);
			break;
		case 1:
			if (OBJ("wiz_key").value.length < 8)
			{
				BODY.ShowAlert("<?echo i18n("Incorrect key length, should be 8 to 63 characters long.");?>");
				return;
			}
			if (OBJ("wiz_key").value.length == 64 && OBJ("wiz_key").value.match(/\W/))
			{
				BODY.ShowAlert("<?echo i18n("Invalid key, should be 64 characters using 0-9 and A-F.");?>");
				return;
			}
			break;
		default:
		}
		this.SetStage(1);
		this.ShowCurrentStage();
	},
	OnClickCancel: function()
	{
		if (!COMM_IsDirty(false)||confirm("<?echo i18n("Do you want to abandon all changes you made to this wizard?");?>"))
			self.location.href = "./bsc_wlan_main.php";
	}
}

function SetButtonDisabled(name, disable)
{
	var button = document.getElementsByName(name);
	for (i=0; i<button.length; i++)
		button[i].disabled = disable;
}

function UpdateCFG()
{
	OBJ("ssid").innerHTML = OBJ("wiz_ssid").value;
	if (OBJ("autokey").checked)
	{
		OBJ("s_key").style.display = "none";
		OBJ("l_key").style.display = "block";
		OBJ("l_key").innerHTML = PAGE.randomkey;
	}
	else if (OBJ("wiz_key").value.length > 50)
	{
		OBJ("s_key").style.display = "none";
		OBJ("l_key").style.display = "block";
		OBJ("l_key").innerHTML = OBJ("wiz_key").value;
	}
	else
	{
		OBJ("l_key").style.display = "none";
		OBJ("s_key").style.display = "block";
		OBJ("s_key").innerHTML = OBJ("wiz_key").value;
	}
}

function RandomHex(len)
{
	var c = "0123456789abcdef";
	var str = '';
	for (var i = 0; i < len; i+=1)
	{
		var rand_char = Math.floor(Math.random() * c.length);
		str += c.substring(rand_char, rand_char + 1);
	}
	return str;
}
</script>
