<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "DEVICE.LOG,RUNTIME.LOG",
	OnLoad: function() {},
	OnUnload: function() {},
	OnSubmitCallback: function (code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		
		devlog_p = PXML.FindModule("DEVICE.LOG");	
		if (!devlog_p) { BODY.ShowAlert("<?echo i18n("InitValue ERROR!");?>"); return false; }
		devlog_p += "/device/log";
		
		OBJ("from_addr").value			= XG(devlog_p+"/email/from");
		OBJ("to_addr").value			= XG(devlog_p+"/email/to");
		OBJ("email_subject").value		= XG(devlog_p+"/email/subject");
		OBJ("smtp_server_addr").value	= XG(devlog_p+"/email/smtp/server");
		if(XG(devlog_p+"/email/smtp/port")=="")	OBJ("smtp_server_port").value = "25"
		else					OBJ("smtp_server_port").value = XG(devlog_p+"/email/smtp/port");
		OBJ("authenable").checked = (XG(devlog_p+"/email/authenable") == 1);
		OBJ("account_name").value		= XG(devlog_p+"/email/smtp/user");
		OBJ("passwd").value = OBJ("verify_passwd").value = XG(devlog_p+"/email/smtp/password");
		OBJ("sendmail_now").value="0";
		this.OnClickAuthEnable();

		return true;
	},
	PreSubmit: function()
	{
		var fro = OBJ("from_addr").value;
		var to = OBJ("to_addr").value;
		var sub = OBJ("email_subject").value;
		var server = OBJ("smtp_server_addr").value;
		var user = OBJ("account_name").value;
		var passwd = OBJ("passwd").value;
		var verpasswd = OBJ("verify_passwd").value;
	
		if(this.IsBlank(fro) && this.IsBlank(to) && this.IsBlank(sub) && this.IsBlank(server) && this.IsBlank(user) && this.IsBlank(passwd) && this.IsBlank(passwd) && this.IsBlank(verpasswd)){}
		else
		{
			if(this.IsBlank(fro))
			{
				BODY.ShowAlert("<?echo i18n("Please enter a valid Email Address.");?>");
				OBJ("from_addr").focus();
				return null;
			}
			if(this.IsBlank(to))
			{
				BODY.ShowAlert("<?echo i18n("Please enter a valid Email Address.");?>");
				OBJ("to_addr").focus();
				return null;
			}
			if(this.IsVaildEmail(fro))
			{
				BODY.ShowAlert("<?echo i18n("Please enter a valid Email Address.");?>");
				OBJ("from_addr").focus();
				return null;
			}
			if(this.IsVaildEmail(to))
			{
				BODY.ShowAlert("<?echo i18n("Please enter a valid Email Address.");?>");
				OBJ("to_addr").focus();
				return null;
			}
			if(this.IsBlank(server))
			{
				BODY.ShowAlert("<?echo i18n("Please enter another SMTP Server or IP Address.");?>");
				OBJ("smtp_server_addr").focus();
				return null;
			}
			if(OBJ("authenable").checked)
			{
				if(this.IsBlank(user))
				{
					BODY.ShowAlert("<?echo i18n("Please enter a user name.");?>");
					OBJ("account_name").focus();
					return null;
				}
				if(this.IsBlank(passwd))
				{
					BODY.ShowAlert("<?echo i18n("Please enter a valid Password.");?>");
					OBJ("passwd").focus();
					return null;
				}
				if(this.IsBlank(verpasswd))
				{
					BODY.ShowAlert("<?echo i18n("Please enter a valid Password.");?>");
					OBJ("verify_passwd").focus();
					return null;
				}
				if(passwd != verpasswd)
				{
					BODY.ShowAlert("<?echo i18n("Password isn't match.");?>");
					OBJ("passwd").focus();
					return null;
				}
			}
		}
		XS(devlog_p+"/email/from",			OBJ("from_addr").value);
		XS(devlog_p+"/email/to",			OBJ("to_addr").value);
		XS(devlog_p+"/email/subject",		OBJ("email_subject").value);
		XS(devlog_p+"/email/smtp/server",	OBJ("smtp_server_addr").value);
		XS(devlog_p+"/email/smtp/port",		OBJ("smtp_server_port").value);
		if(OBJ("authenable").checked)
		{
			XS(devlog_p+"/email/authenable",	"1");
			XS(devlog_p+"/email/smtp/user",		OBJ("account_name").value);
			XS(devlog_p+"/email/smtp/password",	OBJ("passwd").value);
		}
		else
			XS(devlog_p+"/email/authenable",	"0");

		PXML.IgnoreModule("RUNTIME.LOG");		
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	devlog_p: null,

        OnClickSendMail: function()
        {
		alert("<?echo i18n("Send Mail Now !");?>");
		OBJ("sendmail_now").value = "1";
		BODY.OnSubmit();
		setTimeout('PAGE.SendMailNow()',3000);
	},
	SendMailNow: function()
	{
                var ajaxObj = GetAjaxObj("sendmail");
                ajaxObj.createRequest();
                ajaxObj.onCallback = function(xml)
		{
                        ajaxObj.release();
                }
                ajaxObj.setHeader("Content-Type", "application/x-www-form-urlencoded");
                ajaxObj.sendRequest("service.cgi", "EVENT=SENDMAIL");
        },
	OnClickAuthEnable: function()
	{
		if (OBJ("authenable").checked)
		{
			OBJ("account_name").setAttribute("modified", "false");
			OBJ("account_name").disabled = false;
			OBJ("passwd").setAttribute("modified", "false");
			OBJ("passwd").disabled = false;
			OBJ("verify_passwd").setAttribute("modified", "false");
			OBJ("verify_passwd").disabled = false;
		}
		else
		{
			OBJ("account_name").setAttribute("modified", "ignore");
			OBJ("account_name").disabled = true;
			OBJ("passwd").setAttribute("modified", "ignore");
			OBJ("passwd").disabled = true;
			OBJ("verify_passwd").setAttribute("modified", "ignore");
			OBJ("verify_passwd").disabled = true;
		}
	},
	// this function is used to check if the inputted string is blank or not.
	IsBlank: function(s)
	{
		var i=0;
		for(i=0;i<s.length;i++)
		{
			c=s.charAt(i);
			if((c!=' ')&&(c!='\n')&&(c!='\t'))return false;
		}
		return true;
	},
	IsVaildEmail: function(str)
	{
		var valid_email=false;
		for(var i=0; i<str.length; i++)
		{
			if( (str.charAt(i) != '@') )	continue;
			else	return false;
		}
		return true;
	}
}
</script>
