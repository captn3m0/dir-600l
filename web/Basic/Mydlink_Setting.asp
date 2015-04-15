<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<head>
<meta http-equiv="content-type" content="text/html; charset=<% getLangInfo("charset");%>" />
<link rel="stylesheet" rev="stylesheet" href="../style.css" type="text/css" />
<link rel="stylesheet" rev="stylesheet" href="../<% getInfo("substyle");%>" type="text/css" />
<script language="JavaScript" type="text/javascript">
<!--
var lang = "<% getLangInfo("lang");%>";
//-->
</script>
<style type="text/css">
fieldset label.duple {
width: 203px;
}
</style>
<!-- InstanceEndEditable -->
<script type="text/javascript" src="../ubicom.js"></script>
<script type="text/javascript" src="../xml_data.js"></script>
<script type="text/javascript" src="../navigation.js"></script>
<% getLangInfo("LangPath");%>
<script type="text/javascript" src="../utility.js"></script>
<script type="text/javascript">
//<![CDATA[
var WLAN_ENABLED; 
var OP_MODE;
if('<%getInfo("opmode");%>' =='Disabled')
	OP_MODE='1';
else
	OP_MODE='0';
if('<%getIndexInfo("wlanDisabled");%>'=='Disabled')
	WLAN_ENABLED='0';
else
	WLAN_ENABLED='1';

function get_webserver_ssi_uri() {
			return ("" !== "") ? "/Basic/Setup.asp" : "/Basic/Mydlink_Setting.asp";
}
function onclick_mydlink_service()
{
	if("<%getIndexInfo("adminPass");%>" == "")
	{
		alert (sw("txtWizardMydlinkRouterPasswd")+ sw("txtIsBlank"));
	}
	else{
		window.location='Wizard_Mydlink_Sign.asp?t='+new Date().getTime()+'&err=0&sign_in&setting';
	}
}
function web_timeout()
{
setTimeout('do_timeout()','<%getIndexInfo("logintimeout");%>'*60*1000);
}
function template_load()
{
		<% getFeatureMark("MultiLangSupport_Head_script");%>
		lang_form = document.forms.lang_form;
		if ("" === "") {
			assign_i18n();
			lang_form.i18n_language.value = "<%getLangInfo("langSelect")%>";
		}
		<% getFeatureMark("MultiLangSupport_Tail_script");%>
var global_fw_minor_version = "<%getInfo("fwVersion")%>";
var fw_extend_ver = "";			
var fw_minor;
assign_firmware_version(global_fw_minor_version,fw_extend_ver,fw_minor);
var hw_version="<%getInfo("hwVersion")%>";
var productModel="<%getInfo("productModel")%>";
document.getElementById("hw_version_head").innerHTML = hw_version;
document.getElementById("product_model_head").innerHTML = productModel;
SubnavigationLinks(WLAN_ENABLED, OP_MODE);

topnav_init(document.getElementById("topnav_container"));
page_load();
RenderWarnings();
}
//]]>
</script>
<script type="text/javascript">
//<![CDATA[
var mf;
function page_load()
{
		mf = document.forms.mainform;
		var register_status = "<%getInfo("mydlink_register_status");%>";
		if(register_status==1)
		{
			document.getElementById("mydlink_sign_status").innerHTML = sw("txtMydlinkRegisterOn");
			document.getElementById("mydlink_account").innerHTML = "<%getInfo("mydlinkaccount");%>";
			mf.RegisterMydlinkService.disabled=true;
		}
		else
		{	
			document.getElementById("mydlink_sign_status").innerHTML = sw("txtMydlinkegisterOff");
			mf.RegisterMydlinkService.disabled=false;
		}
}

function page_submit()
{
	mf = document.forms.mainform;
	mf.curTime.value = new Date().getTime();

	if (!is_form_modified("mainform") && !confirm(sw("txtSaveAnyway"))) {
			return false;
	}

	if (is_form_modified("mainform")){  //something changed
		mf.settingsChanged.value = 1;
	}
		mf.submit();
}
function init()
{
	var DOC_Title= sw("txtTitle")+" : "+sw("txtSetup")+'/'+sw("txtMydlinkSetting");
	document.title = DOC_Title;	
	//document.getElementById("DontSaveSettings").value=	sw("txtDontSaveSettings");		
	//document.getElementById("SaveSettings").value=	sw("txtSaveSettings");
	get_by_id("RestartNow").value = sw("txtRebootNow");
	get_by_id("RestartLater").value = sw("txtRebootLater");
	get_by_id("RegisterMydlinkService").value = sw("txtEnableMydlinkService");
}
function page_cancel()
{
	page_load();
	init();
}

	//]]>
	</script>
	<!-- InstanceEndEditable -->
</head>
<body onload="template_load(); init();web_timeout();">
<div id="loader_container" onclick="return false;">&nbsp;</div>
<div id="outside">
<table id="table_shell" cellspacing="0" summary=""><col span="1"/>
<tr><td>
<SCRIPT >
DrawHeaderContainer();
DrawMastheadContainer();
DrawTopnavContainer();
</SCRIPT>
<table id="content_container" border="0" cellspacing="0" summary=""><col span="3"/>
<tr><td id="sidenav_container">
<div id="sidenav"><SCRIPT >
DrawBasic_subnav();
DrawAdvanced_subnav();
DrawTools_subnav();
DrawStatus_subnav();
DrawHelp_subnav();
DrawEarth_onlineCheck(<%getWanConnection("");%>);
DrawRebootButton();
</SCRIPT></div>
<% getFeatureMark("MultiLangSupport_Head");%>
<SCRIPT >DrawLanguageList();</SCRIPT>
<% getFeatureMark("MultiLangSupport_Tail"); %>								
</td>
<td id="maincontent_container">
<SCRIPT >
DrawRebootContent();
</SCRIPT>
<div id="warnings_section" style="display:none">
<div class="section" >
<div class="section_head">
<h2><SCRIPT >ddw("txtConfigurationWarnings");</SCRIPT></h2>
<div style="display:block" id="warnings_section_content">
</div><!-- box warnings_section_content -->
</div></div></div> <!-- warnings_section -->
<div id="maincontent" style="display: block">
<!-- InstanceBeginEditable name="Main Content" -->
<form id="mainform" name="mainform" action="" method="post">
	<input type="hidden" id="settingsChanged" name="settingsChanged" value="0"/>
	<input type="hidden" id="curTime" name="curTime" value=""/>
<div class="section">
<div class="section_head">
<h2><SCRIPT >ddw("txtMydlinkSetting");</SCRIPT></h2>
<br>
<p><SCRIPT >ddw("txtMydlinkShow");</SCRIPT></p>
<!--<input class="button_submit" type="button" id="SaveSettings" name="SaveSettings" value="" onclick="page_submit()"/>
<input class="button_submit" type="button" id="DontSaveSettings"  name="DontSaveSettings" value="" onclick="page_cancel()"/>-->
</div></div>

<div class="box" id="mydlink_status">
<h3>MYDLINK</h3>
<fieldset><p><label class="duple"><b><SCRIPT >ddw("txtMydlinkService");</SCRIPT></b>
&nbsp;:</label><span id="mydlink_sign_status"></span>
</p><p><label class="duple"><b><SCRIPT >ddw("txtMydlinkAccount");</SCRIPT></b>
&nbsp;:</label><span id="mydlink_account"></span>
</p>
</fieldset></div>

<div class="box" id="mydlink_status">
<h3><SCRIPT >ddw("txtEnableMydlinkService");</SCRIPT></h3>
<fieldset><p class="centered">
<input onclick="onclick_mydlink_service();" type="button" class="button_submit" id="RegisterMydlinkService" value=""/>
</p>
</fieldset></div>

<!--@ENDOPTIONAL@-->
</form><!-- InstanceEndEditable --></div></td>
<td id="sidehelp_container">
<div id="help_text">
<!-- InstanceBeginEditable name="Help_Text" -->
<strong><SCRIPT >ddw("txtHelpfulHints");</SCRIPT>...</strong>
<p class="more">
<!-- Link to more help -->
<a href="../Help/Tools.asp#Admin" onclick="return jump_if();">
<SCRIPT >ddw("txtMore");</SCRIPT>...</a>										</p>
<!-- InstanceEndEditable -->
</div></td></tr></table>
<SCRIPT >Write_footerContainer();</SCRIPT>
<SCRIPT >print_copyright();</SCRIPT>
</div><!-- outside -->
</body>
<!-- InstanceEnd -->
</html>

