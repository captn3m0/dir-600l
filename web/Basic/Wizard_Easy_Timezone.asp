<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<head>
<meta http-equiv=X-UA-Compatible content=IE=EmulateIE7>
<meta http-equiv="content-type" content="text/html; charset=<% getLangInfo("charset");%>" />
<link rel="stylesheet" rev="stylesheet" href="../style.css" type="text/css" />
<link rel="stylesheet" rev="stylesheet" href="../<% getInfo("substyle");%>" type="text/css" />
<script language="JavaScript" type="text/javascript">
<!--
var lang = "<% getLangInfo("lang");%>";
//-->
</script>
<style type="text/css">
#wan_modes p {
	margin-bottom: 1px;
}
#wan_modes input {
	float: left;
	margin-right: 1em;
}
#wan_modes label.duple {
	float: none;
	width: auto;
	text-align: left;
}
#wan_modes .itemhelp {
	margin: 0 0 1em 2em;
}
#wz_buttons {
	margin-top: 1em;
	border: none;
}
</style>
<script type="text/javascript" src="../ubicom.js"></script>
<script type="text/javascript" src="../xml_data.js"></script>
<script type="text/javascript" src="../navigation.js"></script>
<% getLangInfo("LangPathWizard");%>
<script type="text/javascript" src="../utility.js"></script>
<script type="text/javascript" src="../time_array.js"></script>
<script type="text/javascript">
//<![CDATA[
function web_timeout()
{
setTimeout('do_timeout()','<%getIndexInfo("logintimeout");%>'*60*1000);
}
function template_load()
{
	<% getFeatureMark("MultiLangSupport_Head_script");%>
	lang_form = document.forms.lang_form;
	if ("" === "")
	{
		assign_i18n();
		lang_form.i18n_language.value = "<%getLangInfo("langSelect")%>";
	}
	<% getFeatureMark("MultiLangSupport_Tail_script");%>
	var global_fw_minor_version = "<%getInfo("fwVersion")%>";
	var fw_extend_ver = "";			
	var fw_minor;
	assign_firmware_version(global_fw_minor_version,fw_extend_ver,fw_minor);
	var hw_version="<%getInfo("hwVersion")%>";
	document.getElementById("hw_version_head").innerHTML = hw_version;
	document.getElementById("product_model_head").innerHTML = modelname;
	page_load();
	RenderWarnings();
}

function prev()
{
	document.write("<input type='button' name='prev' value=\""+sw("txtPrev")+"\" onClick=\"page_prev()\">&nbsp;");
}

function next()
{
	document.write("<input type='button' name='next' value=\""+sw("txtNext")+"\" onClick=\"page_submit()\">&nbsp;");
}

function page_prev()
{
	self.location.href="Wizard_Easy_SetPassword.asp?t="+new Date().getTime();
}

function page_submit()
{
	get_by_id("curTime").value = new Date().getTime();
	document.wz_form_pg_6.submit();
}
function tz_daylight_on_off(checkValue)
{
	//alert("tz_daylight_on_off="+checkValue);
	if(checkValue == true)
	{
		get_by_id("tz_daylight_select").disabled = false;
	}
	else
	{
		get_by_id("tz_daylight").value = false;
		get_by_id("tz_daylight_select").disabled = true;
	}
	
}
function tz_timezone_selector()
{
		daylightOffset = ntp_zone_array[get_by_id("select_timezone").selectedIndex].daylightOffset;
		tz_daylight_on_off(daylightOffset-7200 != 0);	
}
function page_load() 
{
	timezone_init();	
	autoselecttz();
	tz_timezone_selector();
	tz_daylight_selector(get_by_id("tz_daylight").value == "true");
	var tz_timezone_handle = document.getElementById("select_timezone");
		if (typeof(tz_timezone_handle.addEventListener) != "undefined") {
			tz_timezone_handle.addEventListener("change", tz_timezone_selector, false);
		} else if (typeof(tz_timezone_handle.attachEvent) != "undefined") {
			tz_timezone_handle.attachEvent("onchange", tz_timezone_selector);
		} else {
			tz_timezone_handle.onchange = tz_timezone_selector;
		}
}

function page_cancel()
{
	if (is_form_modified("wz_form_pg_6") || get_by_id("settingsChanged").value == 1) {
		if (confirm (sw("txtAbandonAallChanges"))) {
			top.location='Internet.asp?t='+new Date().getTime();
		}
	} else {
		top.location='Internet.asp?t='+new Date().getTime();
	}			
}

function init()
{
	var DOC_Title= sw("txtTitle")+" : "+sw("txtSetup")+'/'+sw("txtInternetConnectionSetupWizard");
	document.title = DOC_Title;
	set_form_default_values("wz_form_pg_6");		
}

function tzselect_selector()
{
	var tz_sel = document.getElementById("select_timezone");
	document.getElementById("tz_timezone_index").value = tz_sel.selectedIndex;
	document.getElementById("tz_timezone").value = tz_sel.value;
}

function autoselecttz()
{
	var tzselect = document.getElementById("select_timezone");
	var Timer = new Date();
	var timezone = Timer.getTimezoneOffset()/60;
/*	var d = new Date(2005, 0, 1);
	var d1 = new Date(2005, 6, 1);
	if(d.getTimezoneOffset() != d1.getTimezoneOffset())
	{
		alert(sw("txtAlertAutoTimezone"));
	}
*/
//	alert("timezone="+timezone);

	if(timezone == "12")	tzselect.selectedIndex = "0"; //-12
	else if(timezone == "11") tzselect.selectedIndex = "1"; //-11
	else if(timezone == "10") tzselect.selectedIndex = "2"; //-10
	else if(timezone == "9") tzselect.selectedIndex = "3"; //-9
	else if(timezone == "8") tzselect.selectedIndex = "4"; //-8
	else if(timezone == "7") tzselect.selectedIndex = "5"; //-7
	else if(timezone == "6") tzselect.selectedIndex = "7"; //-6
	else if(timezone == "5") tzselect.selectedIndex = "11"; //-5
	else if(timezone == "4.5") tzselect.selectedIndex = "14"; //-4:30
	else if(timezone == "4") tzselect.selectedIndex = "15"; //-4
	else if(timezone == "3.5") tzselect.selectedIndex = "18"; //-3.5
	else if(timezone == "3") tzselect.selectedIndex = "19"; 
	else if(timezone == "2") tzselect.selectedIndex = "22";
	else if(timezone == "1") tzselect.selectedIndex = "23";
	else if(timezone == "0") tzselect.selectedIndex = "25";
	else if(timezone == "-1") tzselect.selectedIndex = "27";
	else if(timezone == "-2") tzselect.selectedIndex = "33";
	else if(timezone == "-3") tzselect.selectedIndex = "39";
	else if(timezone == "-3.5") tzselect.selectedIndex = "42";
	else if(timezone == "-4") tzselect.selectedIndex = "43";
	else if(timezone == "-4.5") tzselect.selectedIndex = "46";
	else if(timezone == "-5") tzselect.selectedIndex = "47";
	else if(timezone == "-5.5") tzselect.selectedIndex = "48";
	else if(timezone == "-5.75") tzselect.selectedIndex = "49";
	else if(timezone == "-6") tzselect.selectedIndex = "50";
	else if(timezone == "-6.5") tzselect.selectedIndex = "53";
	else if(timezone == "-7") tzselect.selectedIndex = "54";
	else if(timezone == "-8") tzselect.selectedIndex = "56";
	else if(timezone == "-9") tzselect.selectedIndex = "61";
	else if(timezone == "-9.5") tzselect.selectedIndex = "64";
	else if(timezone == "-10") tzselect.selectedIndex = "66";
	else if(timezone == "-11") tzselect.selectedIndex = "71";
	else if(timezone == "-12") tzselect.selectedIndex = "72";
	else if(timezone == "-13") tzselect.selectedIndex = "75";

	tzselect.selectedIndex = "4";
	tzselect_selector();
}
function tz_daylight_selector(checked)
{
	get_by_id("tz_daylight").value = checked;
	get_by_id("tz_daylight_select").checked = checked;
}
function ntpSrv_selector(selectValue)
{
	document.getElementById("config_ntpSrv").value = selectValue;
	document.getElementById("select_ntpSrv").value = selectValue;
}

add_onload_listener(timezone_init);

function timezone_init()
{	
	var tzselect = document.getElementById("select_timezone");
	var tzform = tzselect.form;
	var dF=document.forms[0];
	tzselect.selectedIndex = tzform.tz_timezone_index.value;//63
	ntpSrv_selector(document.getElementById("config_ntpSrv").value);
	set_form_default_values(tzform.id);
	if (typeof(tzselect.addEventListener) != "undefined")
	{
		tzselect.addEventListener("change", tzselect_selector, false);
	}
	else if (typeof(tzselect.attachEvent) != "undefined")
	{
		tzselect.attachEvent("onchange", tzselect_selector);
	}
	else
	{
		tzselect.onchange = tzselect_selector;
	}
}

//]]>
</script>	
</head>
<body onload="template_load();init();web_timeout();">
<div id="loader_container" onclick="return false;" style="display: none">&nbsp;</div>
<div id="outside_1col">
<table id="table_shell" cellspacing="0" summary=""><col span="1"/>
<tbody><tr><td>
<SCRIPT language=javascript type=text/javascript>
DrawHeaderContainer();
DrawMastheadContainer();
</SCRIPT>
<table id="content_container" border="0" cellspacing="0" summary="">
<tr><td id="sidenav_container">&nbsp;</td><td id="maincontent_container">
<div id="maincontent_1col" style="display: block">

<div id="wz_page_6" style="display:block">
<form id="wz_form_pg_6" name="wz_form_pg_6" action="http://<% getInfo("goformIpAddr"); %>/goform/formEasySetTimezone" method="post">
<input type="hidden" id="settingsChanged" name="settingsChanged" value="<%getWizardInformation("wizardSettingChanged");%>"/>
<input type="hidden" name="config.tz_timezone_index" id="tz_timezone_index" value="<% getWizardInformation("ntpTimeZoneIdx"); %>" />
<input type="hidden" name="config.tz_timezone" id="tz_timezone" value="<% getWizardInformation("ntpTimeZone"); %>" />
<!--<input type="hidden" name="config_ntpSrv" id="config_ntpSrv" value="<% getWizardInformation("ntpSrv"); %>" />-->
<input type="hidden" name="config_ntpSrv" id="config_ntpSrv" value="ntp1.dlink.com" />
<input type="hidden" name="WEBSERVER_SSI_OPLOCK_ACTION" value="post"/>
<input type="hidden" name="WEBSERVER_SSI_OPLOCK_VALUE" value=""/>
<input type="hidden" name="wz_pg_prev" value=""/>
<input type="hidden" name="wz_pg_cur" value=""/>
<input type="hidden" name="wz_modified" value=""/>
<input type="hidden" id="curTime" name="curTime" value=""/>
<input type="hidden" id="tz_daylight" name="config.tz_daylight" value="<% getInfo("ntpdst"); %>"/>
<div id="box_header">
		<h1><SCRIPT language=javascript type=text/javascript>ddw("txtWizardEasy113Step3");</SCRIPT></h1>
<p><SCRIPT language=javascript type=text/javascript>ddw("txtWizardWanStr7");</SCRIPT></p>

		<table align="center">
		<tr>
			<td align=right><SCRIPT >ddw("txtTimeZone");</SCRIPT>&nbsp;:&nbsp;</td>
			<td>
			
<select id="select_timezone" name="select_timezone"><SCRIPT>
var i;
if(LangCode=="SC"){
for(i=0;i<ntp_zone_array_sc.length;i++){
	if (i == ntp_zone_index)
			document.write('<option value="',ntp_zone_array_sc[i].value,'" selected>',ntp_zone_array_sc[i].name,'</option>');
	else
			document.write('<option value="',ntp_zone_array_sc[i].value,'">',ntp_zone_array_sc[i].name,'</option>');
}
}else if(LangCode=="TW")
{
for(i=0;i<ntp_zone_array_tw.length;i++){
    if (i == ntp_zone_index)
            document.write('<option value="',ntp_zone_array_tw[i].value,'" selected>',ntp_zone_array_tw[i].name,'</option>');
    else
            document.write('<option value="',ntp_zone_array_tw[i].value,'">',ntp_zone_array_tw[i].name,'</option>');
}
}else{
 	for(i=0;i<ntp_zone_array.length;i++){
	if (i == ntp_zone_index)
			document.write('<option value="',ntp_zone_array[i].value,'" selected>',ntp_zone_array[i].name,'</option>');
	else
			document.write('<option value="',ntp_zone_array[i].value,'">',ntp_zone_array[i].name,'</option>');
	}
}
</SCRIPT></select>
			</td>
		</tr>
		<tr>
		<td align=right><SCRIPT >ddw("txtEnableDaylightSaving");</SCRIPT>&nbsp;:&nbsp;</td>
		<td><input type="checkbox" id="tz_daylight_select" onclick="tz_daylight_selector(this.checked);"/></td>
		</tr>
		<tr style="display: none">
			<td><SCRIPT >ddw("txtNTPServerUsed");</SCRIPT>&nbsp;:&nbsp;</td>
			<td>
				<select id="select_ntpSrv" onchange="ntpSrv_selector(this.value);">
					<option value="ntp1.dlink.com">ntp1.dlink.com</option>
					<option value="ntp.dlink.com.tw">ntp.dlink.com.tw</option>
				</select>
			</td>
		</tr>
		</table>
		<br>
		<center><script>prev();next();</script></center>
		<br>
		</div>
</form></div><!-- wz_page_6 -->

</div>
<% getFeatureMark("MultiLangSupport_Head");%>
<SCRIPT language=javascript type=text/javascript>DrawLanguageList();	</SCRIPT>
<% getFeatureMark("MultiLangSupport_Tail"); %>
</td><td id="sidehelp_container">&nbsp;</td></tr></table>
<SCRIPT language=javascript type=text/javascript>Write_footerContainer();</SCRIPT>
</td></tr></tbody></table>
<SCRIPT language=javascript>print_copyright();</SCRIPT>
</div></body>
</html>
