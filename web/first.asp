<html>
<head>
</head>
<% getLangInfo("LangPathWizard");%>
<script>
function init()
{
	var ecflag = <% getIndexInfo("enableecflag") %>;
	if(ecflag == 0)
	{
		if((LangCode == "SC")||(LangCode == "TW")||(LangCode == "KO"))
		{
			self.location.href="Basic/Wizard_Easy_Welcome.asp?t="+new Date().getTime();
		}
		else
		{
			self.location.href="Basic/Wizard_Easy_LangSelect.asp?="+new Date().getTime();
		}
	}
	else
	{
		self.location.href="index.asp";
	}
}
</script>
<body onLoad="init();">
</html>
