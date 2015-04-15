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
fieldset label.duple {
width: 203px;
}
</style>
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
			return ("" !== "") ? "/Basic/Setup.asp" : "/Advanced/Multiple_SSID.asp";
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
function draw_cipher_type(index)
{
	var wlan_band=<%getIndexInfo("band");%>;
	if(wlan_band != 0){
		if(index == 3)
		{
			get_by_id("cipher_type").length=0;
			get_by_id("cipher_type")[0] = new Option(sw("txtTKIPandAES"), "3", false, false);
			get_by_id("cipher_type")[1] = new Option("TKIP", "1", false, false);
			get_by_id("cipher_type")[2] = new Option("AES", "2", false, false);
		}
		else
		{
			get_by_id("cipher_type").length=0;
			get_by_id("cipher_type")[0] = new Option("TKIP", "1", false, false);
			get_by_id("cipher_type")[1] = new Option("AES", "2", false, false);
		}
	}else{
		get_by_id("cipher_type").length=0;
		get_by_id("cipher_type")[0] = new Option("AES", "2", false, false);
	}

}
function chg_wep_def_key(selectValue)
	{
	get_by_id("wep_def_key").value = selectValue;
	//chg_wep_type(get_by_id("wep_key_len").value);
}

function chg_cipher_type(selectValue)
{
	get_by_id("cipher_type").value = selectValue;
}

function chg_wep_auth_type(selectValue)
{
	get_by_id("wireless_wep_auth_type").value=selectValue;
	if(selectValue == 1) //shared
			get_by_id("auth_type").value=1;
	else //open or both
		get_by_id("auth_type").value=0;

}

function chg_wep_type(selectValue)
	{
	get_by_id("wep_key_len").value = selectValue;
	get_by_id("wireless_wep_key_len").value = selectValue;

	get_by_id("wep_key_64").style.display		= "none";
	get_by_id("wep_key_128").style.display		= "none";
	print_keys("wepkey_64","10");
	print_keys("wepkey_128","26");	
	if(mf.wep_key_len.value==2)	
		get_by_id("wep_key_128").style.display	= "";
	else
		get_by_id("wep_key_64").style.display	= "";
}
function draw_cipher_type_11n_band(wpa_mode)
{
	get_by_id("cipher_type").length=0;
	get_by_id("cipher_type")[0] = new Option("AES", "2", false, false);
}
function on_change_security_type(selectValue)
{
	//-1:router; 0:ap; 1:client; 2:wds: 3:wds+ap; 5:wisp; 6:wds+router
	var selectindex=get_by_id("multi_ssid_index").value;
	var opmode;
	get_by_id("security_type").value = selectValue;
	switch (selectindex*1)
	{
		case -1:
		opmode= <%getIndexInfo("wlanMode");%>;
		if(<%getIndexInfo("wpa_enterprise_enabled");%> == true)
		{
			get_by_id("wireless_ieee8021x_enabled").value= 1;
		}
		else
		{
			get_by_id("wireless_ieee8021x_enabled").value= 0;	
		}
		break;

		case 0:
		opmode= temp_apinfo0[2];
		get_by_id("wireless_ieee8021x_enabled").value= temp_apinfo0[22];
		break;
		
		case 1:
		opmode= temp_apinfo1[2];
		get_by_id("wireless_ieee8021x_enabled").value= temp_apinfo1[22];
		break;
		
		case 2:
		opmode= temp_apinfo2[2];
		get_by_id("wireless_ieee8021x_enabled").value= temp_apinfo2[22];
		break;		
	}
	var CurrentWlanMode=(opmode.value*1);
	var wlan_band=<%getIndexInfo("band");%>;

	if(wlan_band==0){//11n band, remove tkip option
		if(selectValue==2 || selectValue==4 || selectValue==6)
			draw_cipher_type_11n_band(selectValue);
		else {
			if(CurrentWlanMode !=1 && CurrentWlanMode !=5){
				draw_cipher_type(3);
			}else{
				draw_cipher_type(2);
			}
		}
	}else{
		if(CurrentWlanMode !=1 && CurrentWlanMode !=5){
			draw_cipher_type(3);
		}else{
			draw_cipher_type(2);
		}
	}
	
	if(selectValue==2 || selectValue==4 || selectValue==6){
		if(get_by_id("cipher_type").length==1 && get_by_id("cipher_type").value != 2)
			get_by_id("cipher_type").selectedIndex=0; 
	}

	if(selectValue ==1)
	{
		mf.wireless_wepon.value = true;
		mf.wireless_wpa_enabled.value = false;
	}
	else if(selectValue ==2)
	{
		mf.wireless_wepon.value = false;
		mf.wireless_wpa_enabled.value = true;
		mf.wireless_wpa_mode.value = 1;
		}
	else if(selectValue ==4)
		{
		mf.wireless_wepon.value = false;
		mf.wireless_wpa_enabled.value = true;
		mf.wireless_wpa_mode.value = 3;
		}
	else if(selectValue ==6)
		{
		mf.wireless_wepon.value = false;
		mf.wireless_wpa_enabled.value = true;
		mf.wireless_wpa_mode.value = 2;
	}
	else
	{
		mf.wireless_wepon.value = false;
		mf.wireless_wpa_enabled.value = false;
	}
	
	get_by_id("show_wep").style.display = "none";
	get_by_id("show_wpa").style.display = "none";
	
	if (selectValue == 1)
	{
		if( selectindex == -1)
		{
			get_by_id("show_wep").style.display = "none";
		}else{
			get_by_id("show_wep").style.display = "";
		}
	}
	else if(selectValue >= 2)
	{
		
		if( selectindex == -1)
		{
			
			if(selectValue == 2)		get_by_id("title_wpa").style.display		= "none";
			if(selectValue == 4)		get_by_id("title_wpa2").style.display		= "none";
			if(selectValue == 6)		get_by_id("title_wpa2_auto").style.display= "none";
		
		}else{
		get_by_id("show_wpa").style.display = "";
		
		get_by_id("title_wpa").style.display			= "none";
		get_by_id("title_wpa2").style.display			= "none";
		get_by_id("title_wpa2_auto").style.display	= "none";
		
		if(selectValue == 2)		get_by_id("title_wpa").style.display		= "";
		if(selectValue == 4)		get_by_id("title_wpa2").style.display		= "";
		if(selectValue == 6)		get_by_id("title_wpa2_auto").style.display= "";
		
		if(get_by_id("wireless_ieee8021x_enabled").value == '1')
			chg_psk_eap(1);
		else
			chg_psk_eap(2);
		}
	}
	
	//init_wlan_security_select=sec_type.selectedIndex;//?
}
function chg_psk_eap(selectValue)
{
//	if(get_by_id("wifisc_enable_select").checked == true && selectValue == 1)
//	{
//		alert(sw("txtWPSCantWorkAtWPAEAPMode"));
//		get_by_id("psk_eap").value = 2;
//		return false;
//	}
	if(selectValue == 1)
	{
		get_by_id("wireless_ieee8021x_enabled").value = 1;
	}
	else
	{
		get_by_id("wireless_ieee8021x_enabled").value = 0;
	}
	
	get_by_id("psk_eap").value = selectValue;
	
	var wpa_type = get_by_id("psk_eap");
	get_by_id("psk_setting").style.display = "none";
	get_by_id("eap_setting").style.display = "none";
	if(wpa_type.value==2)	{	get_by_id("psk_setting").style.display = "";	}
	else					{	get_by_id("eap_setting").style.display = "";	}
}
var init_wlan_security_select;
function init_show_wlan_security_type()
{
		var sec_type;
		if("<%getIndexInfo("wep_enabled");%>" == "true")
		{
			sec_type = 1;		
		}
		else if("<%getIndexInfo("wpa_enabled");%>" == "true")
		{
			if(<%getIndexInfo("wpa_mode");%> == 1)
			{
				sec_type = 2;
			}
			else if(<%getIndexInfo("wpa_mode");%> == 3)
			{
				sec_type = 4;
			}
			else
			{
				sec_type = 6;
		}
		}
		else
		{
			sec_type = 0;
		}
		on_change_security_type(sec_type);
}
function multi_enable_selector(checked)
{
	get_by_id("multi_enable").value = checked ? "0" : "1";
	get_by_id("multi_enable_select").checked = checked;
}
var temp_apinfo0=new Array();
var temp_apinfo1=new Array();
var temp_apinfo2=new Array();
var ap_info0,ap_info1,ap_info2;
//ap_info0 = "0<divide>1<divide>0<divide>keith-va0<divide>1<divide>2<divide>1<divide>0<divide>3132333435<divide>3132333435<divide>3132333435<divide>3132333435<divide>31323334353637383930313233<divide>31323334353637383930313233<divide>31323334353637383930313233<divide>31323334353637383930313233<divide>0<divide>3<divide>3<divide>2<divide>0<divide>12345678<divide>0<divide>192.168.1.188<divide>1812<divide>realtek_123";
//ap_info1 = "1<divide>0<divide>0<divide>keith-va1<divide>6<divide>0<divide>2<divide>0<divide>3132333435<divide>3132333435<divide>3132333435<divide>3132333435<divide>31323334353637383930313233<divide>31323334353637383930313233<divide>31323334353637383930313233<divide>31323334353637383930313233<divide>0<divide>3<divide>3<divide>1<divide>0<divide>12345678<divide>1<divide>192.168.1.188<divide>1812<divide>realtek_123";
ap_info0 = "<%getIndexInfo("Muti_Info0");%>";
ap_info1 = "<%getIndexInfo("Muti_Info1");%>";
ap_info2 = "<%getIndexInfo("Muti_Info2");%>";
temp_apinfo0=ap_info0.split("<divide>");
temp_apinfo1=ap_info1.split("<divide>");
temp_apinfo2=ap_info2.split("<divide>");
function on_change_index(indexValue)
{
	get_by_id("multi_ssid_index").value = indexValue ;
	var security_type = get_by_id("security_type").value ;
	switch (indexValue*1)
	{
		case -1:
		get_by_id("ssid").value = "<%getInfo("ssid");%>";
		get_by_id("ssid").disabled=true;
		multi_enable_selector("<%getIndexInfo("wlanEnabled")%>" == "1");
		get_by_id("multi_enable_select").disabled=true;
		if(<%getIndexInfo("wep_auth");%> == 1)
		{
			get_by_id("wireless_wep_key_len").value= 0;
		}
		else
		{
			get_by_id("wireless_wep_key_len").value= 1;	
		}
		if(<%getIndexInfo("wep_mode");%> == 0)
		{
			get_by_id("wireless_wep_key_len").value= 1;
		}
		else
		{
			get_by_id("wireless_wep_key_len").value= 2;	
		}
		get_by_id("wireless_wep_def_key").value= <%getIndexInfo("defaultKeyId");%>-1;
		get_by_id("wireless_cipher_type").value=<%getIndexInfo("wpaCipher");%>;
		init_show_wlan_security_type();
		get_by_id("security_type").disabled=true;
		get_by_id("wpapsk1").value="<%getInfo("pskValue");%>";
		get_by_id("srv_ip1").value="<%getInfo("rsIp1");%>";
		get_by_id("srv_port1").value="<%getInfo("rsPort1");%>";
		get_by_id("srv_sec1").value="<%getInfo("rsPassword1");%>";
		get_by_id("AddSettings").disabled=true;
		break;
		
		case 0:
		get_by_id("ssid").value = temp_apinfo0[3];
		get_by_id("ssid").disabled=false;
		multi_enable_selector(temp_apinfo0[1] == "0");
		get_by_id("multi_enable_select").disabled=false;
		get_by_id("wireless_wep_auth_type").value= temp_apinfo0[5];
		get_by_id("wireless_wep_key_len").value= temp_apinfo0[6];	
		get_by_id("wireless_wep_def_key").value= temp_apinfo0[16];
		get_by_id("wireless_cipher_type").value=temp_apinfo0[17];	
		on_change_security_type(temp_apinfo0[4]*1);	
		get_by_id("security_type").disabled=false;
		get_by_id("wpapsk1").value=temp_apinfo0[21];
		get_by_id("srv_ip1").value=temp_apinfo0[23];
		get_by_id("srv_port1").value=temp_apinfo0[24];
		get_by_id("srv_sec1").value=temp_apinfo0[25];
		get_by_id("AddSettings").disabled=false;
		break;		
		
		case 1:
		get_by_id("ssid").value = temp_apinfo1[3];
		get_by_id("ssid").disabled=false;
		multi_enable_selector(temp_apinfo1[1] == "0");
		get_by_id("multi_enable_select").disabled=false;
		get_by_id("wireless_wep_auth_type").value= temp_apinfo1[5];
		get_by_id("wireless_wep_key_len").value= temp_apinfo1[6];
		get_by_id("wireless_wep_def_key").value= temp_apinfo1[16];
		get_by_id("wireless_cipher_type").value=temp_apinfo1[17];		
		on_change_security_type(temp_apinfo1[4]*1);	
		get_by_id("security_type").disabled=false;
		get_by_id("wpapsk1").value=temp_apinfo1[21];
		get_by_id("srv_ip1").value=temp_apinfo1[23];
		get_by_id("srv_port1").value=temp_apinfo1[24];
		get_by_id("srv_sec1").value=temp_apinfo1[25];
		get_by_id("AddSettings").disabled=false;
		break;
				
		case 2:
		get_by_id("ssid").value = temp_apinfo2[3];
		get_by_id("ssid").disabled=false;
		multi_enable_selector(temp_apinfo2[1] == "0");
		get_by_id("multi_enable_select").disabled=false;
		get_by_id("wireless_wep_auth_type").value= temp_apinfo2[5];
		get_by_id("wireless_wep_key_len").value= temp_apinfo2[6];
		get_by_id("wireless_wep_def_key").value= temp_apinfo2[16];
		get_by_id("wireless_cipher_type").value=temp_apinfo2[17];	
		on_change_security_type(temp_apinfo2[4]*1);	
		get_by_id("security_type").disabled=false;
		get_by_id("wpapsk1").value=temp_apinfo2[21];
		get_by_id("srv_ip1").value=temp_apinfo2[23];
		get_by_id("srv_port1").value=temp_apinfo2[24];
		get_by_id("srv_sec1").value=temp_apinfo2[25];
		get_by_id("AddSettings").disabled=false;
		break;				
	}
	chg_wep_auth_type(get_by_id("wireless_wep_auth_type").value);
	chg_wep_type(get_by_id("wireless_wep_key_len").value);
	chg_wep_def_key(get_by_id("wireless_wep_def_key").value);
	chg_cipher_type(get_by_id("wireless_cipher_type").value);	
	print_keys("wepkey_64","10");
	print_keys("wepkey_128","26");
}
function print_security_info()
{
		var wep_on,wpa_on,psk_enable,wpa_mode,ieeex_on,info_security_type,selectValue;
		var str=new String("");
		str+="<table class=\"formlisting\" summary=\"\" cellspacing=\"1\" cellpadding=\"0\">";
		str+="<col width=\"32%\" />";
		str+="<col width=\"26%\" />";
		str+="<col width=\"52%\" />";
		str+="<tr>";
		str+="<th>"+sw("txtMultipleIndex")+"</th>";
		str+="<th>"+sw("txtRepeaterSsid")+"</th>";
		str+="<th>"+sw("txtEncryption")+"</th>";
		str+="</tr>";
		str+="<tr>";
		str+="<td>"+sw("txtMultiplePrimary")+"</td>";
		str+="<td>"+get_by_id("ssid").value+"</td>";
		
		wep_on = "<%getIndexInfo("wep_enabled");%>";
		wpa_on = ("<%getIndexInfo("wpa_psk_enabled");%>" == "true" || 
					 "<%getIndexInfo("wpa_enterprise_enabled");%>" == "true") ? "true" : "false";
		wpa_mode = "<%getIndexInfo("wpa_mode");%>";
		ieeex_on = get_by_id("wireless_ieee8021x_enabled").value;			
		info_security_type = sw("txtDisabled");
		if (wep_on == "true") {
			info_security_type = sw("txtWEPSecurity");
		}
		else if (wpa_on == "true") {
			if (wpa_mode == 1) {
				info_security_type = sw("txtWPAOnly");
			} else if (wpa_mode == 2) {
				info_security_type = sw("txtWPAWPA2");
			} else if (wpa_mode == 3) {
				info_security_type = sw("txtWPA2Only");
			}
			if (ieeex_on == 1) {
				info_security_type += " - "+sw("txtEnterprise");
			} else {
				info_security_type += " - "+sw("txtPersonal");
			}
		}	
		
		str+="<td>"+info_security_type+"</td>";
		str+="</tr>";

		if(temp_apinfo0[1]==0)
		{
			selectValue = temp_apinfo0[4];	
			if(selectValue ==1)
			{
				wep_on = true;
				psk_enable = false;
			}
			else if(selectValue ==2)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 1;
			}
			else if(selectValue ==4)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 3;
			}
			else if(selectValue ==6)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 2;
			}
			else
			{
				wep_on = false;
				psk_enable = false;
			}
			ieeex_on = temp_apinfo0[22];
			wpa_on = (psk_enable == true || ieeex_on == 1) ? "true" : "false";
			info_security_type = sw("txtDisabled");
			if (wep_on == true) {
				info_security_type = sw("txtWEPSecurity");
			}
			else if (wpa_on == "true") {
				if (wpa_mode == 1) {
					info_security_type = sw("txtWPAOnly");
				} else if (wpa_mode == 2) {
					info_security_type = sw("txtWPAWPA2");
				} else if (wpa_mode == 3) {
					info_security_type = sw("txtWPA2Only");
				}
				if (ieeex_on == 1) {
					info_security_type += " - "+sw("txtEnterprise");
				} else {
					info_security_type += " - "+sw("txtPersonal");
				}
			}	
			str+="<tr>";
			str+="<td>"+sw("txtMultipleSSID")+"1</td>";
			str+="<td>"+temp_apinfo0[3]+"</td>";
			str+="<td>"+info_security_type+"</td>";
			str+="</tr>";			
		}
		if(temp_apinfo1[1]==0)
		{
			selectValue = temp_apinfo1[4];
			if(selectValue ==1)
			{
				wep_on = true;
				psk_enable = false;
			}
			else if(selectValue ==2)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 1;
			}
			else if(selectValue ==4)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 3;
			}
			else if(selectValue ==6)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 2;
			}
			else
			{
				wep_on = false;
				psk_enable = false;
			}
			ieeex_on = temp_apinfo1[22];
			wpa_on = (psk_enable == true || ieeex_on == 1) ? "true" : "false";
			info_security_type = sw("txtDisabled");
			if (wep_on == true) {
				info_security_type = sw("txtWEPSecurity");
			}
			else if (wpa_on == "true") {
				if (wpa_mode == 1) {
					info_security_type = sw("txtWPAOnly");
				} else if (wpa_mode == 2) {
					info_security_type = sw("txtWPAWPA2");
				} else if (wpa_mode == 3) {
					info_security_type = sw("txtWPA2Only");
				}
				if (ieeex_on == 1) {
					info_security_type += " - "+sw("txtEnterprise");
				} else {
					info_security_type += " - "+sw("txtPersonal");
				}
			}	
			str+="<tr>";
			str+="<td>"+sw("txtMultipleSSID")+"2</th>";
			str+="<td>"+temp_apinfo1[3]+"</td>";
			str+="<td>"+info_security_type+"</td>";
			str+="</tr>";				
		}
		if(temp_apinfo2[1]==0)
		{
			selectValue = temp_apinfo2[4];
			if(selectValue ==1)
			{
				wep_on = true;
				psk_enable = false;
			}
			else if(selectValue ==2)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 1;
			}
			else if(selectValue ==4)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 3;
			}
			else if(selectValue ==6)
			{
				wep_on = false;
				psk_enable = true;
				wpa_mode = 2;
			}
			else
			{
				wep_on = false;
				psk_enable = false;
			}
			ieeex_on = temp_apinfo2[22];
			wpa_on = (psk_enable == true || ieeex_on == 1) ? "true" : "false";
			info_security_type = sw("txtDisabled");
			if (wep_on == true) {
				info_security_type = sw("txtWEPSecurity");
			}
			else if (wpa_on == "true") {
				if (wpa_mode == 1) {
					info_security_type = sw("txtWPAOnly");
				} else if (wpa_mode == 2) {
					info_security_type = sw("txtWPAWPA2");
				} else if (wpa_mode == 3) {
					info_security_type = sw("txtWPA2Only");
				}
				if (ieeex_on == 1) {
					info_security_type += " - "+sw("txtEnterprise");
				} else {
					info_security_type += " - "+sw("txtPersonal");
				}
			}	
			str+="<tr>";
			str+="<td>"+sw("txtMultipleSSID")+"3</td>";
			str+="<td>"+temp_apinfo2[3]+"</td>";
			str+="<td>"+info_security_type+"</td>";
			str+="</tr>";				
		}
		str+="</table>";
		document.getElementById("multi_apinfo_show").innerHTML = str;
}
function page_load()
{
		mf = document.forms.mainform;
		
		on_change_index(-1);	
								
		print_security_info();
}
function chk_wepkey(obj_name, key_type, key_len)
{
	var key_obj=get_by_id(obj_name);
	if(key_type==0)	//ascii
	{
		if(strchk_unicode(key_obj.value))
		{
			if(key_len==13)	alert(sw("txtInvalidKeyfor13char")+".");
			else			alert(sw("txtInvalidKeyfor5char"));
			key_obj.select();
			return false;
		}
	}
	else	//hex
	{
		var test_char, i;
		for(i=0; i<key_obj.value.length; i++)
		{
			test_char=key_obj.value.charAt(i);
			if( (test_char >= '0' && test_char <= '9') ||
				(test_char >= 'a' && test_char <= 'f') ||
				(test_char >= 'A' && test_char <= 'F'))
				continue;

			if(key_len==26)	alert(sw("txtInvalidKeyfor26Dec"));
			else			alert(sw("txtInvalidKeyfor10Dec"));
			key_obj.select();
			return false;
		}
	}
	return true;
}

// Get Object by ID.
function get_by_id(name)
{
	if (document.getElementById)	return document.getElementById(name);//.style;
	if (document.all)				return document.all[name].style;
	if (document.layers)			return document.layers[name];
}
function strchk_special(str)
{
        var strlen=str.length;
        if(strlen>0)
        {
        				var c = '';
                for(var i=0;i<strlen;i++)
                {   
                				c = escape(str.charAt(i));             
                  			if(c.charAt(0) == '%' && c.charAt(1)=='B' && c.charAt(2)=='7')
                          return true;
                }
        
        }
        return false;
}
function page_submit()
{
	mf = document.forms.mainform;
	var f_final	=get_by_id("final_form");
		
	f_final.wlanDisabled_0.value = temp_apinfo0[1];
	f_final.wlanMode_0.value = temp_apinfo0[2];
	f_final.ssid_0.value = temp_apinfo0[3];
	f_final.encrypt_0.value = temp_apinfo0[4];
	f_final.authType_0.value = temp_apinfo0[5];
	f_final.wep_0.value = temp_apinfo0[6];
	f_final.wepKeyType_0.value = temp_apinfo0[7];
	f_final.wep64Key1_0.value = temp_apinfo0[8];
	f_final.wep64Key2_0.value = temp_apinfo0[9];
	f_final.wep64Key3_0.value = temp_apinfo0[10];
	f_final.wep64Key4_0.value = temp_apinfo0[11];
	f_final.wep128Key1_0.value = temp_apinfo0[12];
	f_final.wep128Key2_0.value = temp_apinfo0[13];
	f_final.wep128Key3_0.value = temp_apinfo0[14];
	f_final.wep128Key4_0.value = temp_apinfo0[15];
	f_final.wepDefaultKey_0.value = temp_apinfo0[16];
	f_final.wpaCipher_0.value = temp_apinfo0[17];
	f_final.wpa2Cipher_0.value = temp_apinfo0[18];
	f_final.wpaAuth_0.value = temp_apinfo0[19];
	f_final.wpaPSKFormat_0.value = temp_apinfo0[20];
	f_final.wpaPSK_0.value = temp_apinfo0[21];
	f_final.enable1X_0.value = temp_apinfo0[22];
	f_final.rsIpAddr1_0.value = temp_apinfo0[23];
	f_final.rsPort1_0.value = temp_apinfo0[24];
	f_final.rsPassword1_0.value = temp_apinfo0[25];
	
	f_final.wlanDisabled_1.value = temp_apinfo1[1];
	f_final.wlanMode_1.value = temp_apinfo1[2];
	f_final.ssid_1.value = temp_apinfo1[3];
	f_final.encrypt_1.value = temp_apinfo1[4];
	f_final.authType_1.value = temp_apinfo1[5];
	f_final.wep_1.value = temp_apinfo1[6];
	f_final.wepKeyType_1.value = temp_apinfo1[7];
	f_final.wep64Key1_1.value = temp_apinfo1[8];
	f_final.wep64Key2_1.value = temp_apinfo1[9];
	f_final.wep64Key3_1.value = temp_apinfo1[10];
	f_final.wep64Key4_1.value = temp_apinfo1[11];
	f_final.wep128Key1_1.value = temp_apinfo1[12];
	f_final.wep128Key2_1.value = temp_apinfo1[13];
	f_final.wep128Key3_1.value = temp_apinfo1[14];
	f_final.wep128Key4_1.value = temp_apinfo1[15];
	f_final.wepDefaultKey_1.value = temp_apinfo1[16];
	f_final.wpaCipher_1.value = temp_apinfo1[17];
	f_final.wpa2Cipher_1.value = temp_apinfo1[18];
	f_final.wpaAuth_1.value = temp_apinfo1[19];
	f_final.wpaPSKFormat_1.value = temp_apinfo1[20];
	f_final.wpaPSK_1.value = temp_apinfo1[21];
	f_final.enable1X_1.value = temp_apinfo1[22];
	f_final.rsIpAddr1_1.value = temp_apinfo1[23];
	f_final.rsPort1_1.value = temp_apinfo1[24];
	f_final.rsPassword1_1.value = temp_apinfo1[25];
	
	f_final.wlanDisabled_2.value = temp_apinfo2[1];
	f_final.wlanMode_2.value = temp_apinfo2[2];
	f_final.ssid_2.value = temp_apinfo2[3];
	f_final.encrypt_2.value = temp_apinfo2[4];
	f_final.authType_2.value = temp_apinfo2[5];
	f_final.wep_2.value = temp_apinfo2[6];
	f_final.wepKeyType_2.value = temp_apinfo2[7];
	f_final.wep64Key1_2.value = temp_apinfo2[8];
	f_final.wep64Key2_2.value = temp_apinfo2[9];
	f_final.wep64Key3_2.value = temp_apinfo2[10];
	f_final.wep64Key4_2.value = temp_apinfo2[11];
	f_final.wep128Key1_2.value = temp_apinfo2[12];
	f_final.wep128Key2_2.value = temp_apinfo2[13];
	f_final.wep128Key3_2.value = temp_apinfo2[14];
	f_final.wep128Key4_2.value = temp_apinfo2[15];
	f_final.wepDefaultKey_2.value = temp_apinfo2[16];
	f_final.wpaCipher_2.value = temp_apinfo2[17];
	f_final.wpa2Cipher_2.value = temp_apinfo2[18];
	f_final.wpaAuth_2.value = temp_apinfo2[19];
	f_final.wpaPSKFormat_2.value = temp_apinfo2[20];
	f_final.wpaPSK_2.value = temp_apinfo2[21];
	f_final.enable1X_2.value = temp_apinfo2[22];
	f_final.rsIpAddr1_2.value = temp_apinfo2[23];
	f_final.rsPort1_2.value = temp_apinfo2[24];
	f_final.rsPassword1_2.value = temp_apinfo2[25];
	
	if(is_blank(mf.ssid.value))
	{
		alert(sw("txtSSIDBlank"));
		mf.ssid.focus();
		return false;
	}
	
	if(strchk_unicode(mf.ssid.value))
	{
		alert(sw("txtInvalidSSID"));
		mf.ssid.select();
		return false;
	}

	if(strchk_special(mf.ssid.value))
	{
		alert(sw("txtInvalidSSID"));
		mf.ssid.select();
		return false;
	}
			
	var selectindex=get_by_id("multi_ssid_index").value;

	if(selectindex*1 != -1)
	{
		if(mf.security_type.value=="0")
		{
		}else if(mf.security_type.value=="1")
		{
			var test_len=10;
			var test_wep_obj;
			var key_type;		
			if(mf.wep_key_len.value==2) // 1:64 ; 2:128
			{
				test_wep_obj=get_by_id("wepkey_128");
				if(test_wep_obj.value.length!=13 && test_wep_obj.value.length!=26)
				{
					alert(sw("txtInvalidKeyforwep128"));
					test_wep_obj.select();
					return false;
				}
					
				key_type=(test_wep_obj.value.length==13?0:1);
				if(chk_wepkey("wepkey_128", key_type, test_wep_obj.value.length)==false)	return false;
			}	else
			{
				test_wep_obj=get_by_id("wepkey_64");
				if(test_wep_obj.value.length!=5 && test_wep_obj.value.length!=10)
				{
					alert(sw("txtInvalidKeyforwep64"));
					test_wep_obj.select();
					return false;
				}
				key_type=(test_wep_obj.value.length==5?0:1);
				if(chk_wepkey("wepkey_64", key_type, test_wep_obj.value.length)==false)	return false;
			}
		}
		else if(mf.security_type.value>="2")
		{
			var lan_ip_str = "<% getInfo("ip-rom"); %>";
			var lan_mask_str = "<% getInfo("mask-rom"); %>";
			if(mf.psk_eap.value==1)
			{		
				if(!is_valid_ip(mf.srv_ip1.value,0) || !is_valid_gateway(lan_ip_str,lan_mask_str,mf.srv_ip1.value))
				{		
					alert(sw("txtInvalidRadiusIP"));
					mf.srv_ip1.select();
					return false;
				}
				if(is_blank(mf.srv_port1.value))
				{
					alert(sw("txtInvalidRadiusPort"));
					mf.srv_port1.focus();
					return false;
				}
				if(!is_port_valid(mf.srv_port1.value))
				{
					alert(sw("txtInvalidRadiusPort"));
					mf.srv_port1.select();
					return false;
				}
				if(is_blank(mf.srv_sec1.value))
				{
					alert(sw("txtRadiusSecretCannotEmpty"));
					mf.srv_sec1.focus();
					return false;
				}
				if(strchk_unicode(mf.srv_sec1.value))
				{
					alert(sw("txtRadiusSecretShouldbeAscii"));
					mf.srv_sec1.select();
					return false;
				}
			}
			else
			{
				if(mf.wpapsk1.value.length == 64)
				{
					var test_char,j;
					for(j=0; j<mf.wpapsk1.value.length; j++)
					{
						test_char=mf.wpapsk1.value.charAt(j);
						if( (test_char >= '0' && test_char <= '9') ||
								(test_char >= 'a' && test_char <= 'f') ||
								(test_char >= 'A' && test_char <= 'F'))
							continue;
					
						alert(sw("txtPSKShouldbeHex"));
						mf.wpapsk1.select();
						return false;
					}
				}
				else if(mf.wpapsk1.value.length >=8 && mf.wpapsk1.value.length <64)
                               {
                             		if(mf.wpapsk1.value.charAt(0) == ' '||mf.wpapsk1.value.charAt(mf.wpapsk1.value.length-1) ==' ')
                                                {
                                                        alert(sw("txtheadtailnospeace"));
                                                        mf.wpapsk1.select();
                                                        return false;
                                                }
                                }
				else
				{
					if(mf.wpapsk1.value.length <8 || mf.wpapsk1.value.length > 63)
					{
						alert(sw("txtInvalidPSKLen"));
						mf.wpapsk1.select();
						return false;
		    			}
					if(strchk_unicode(mf.wpapsk1.value))
		    			{
						alert(sw("txtPSKShouldbeAscii"));
						mf.wpapsk1.select();
						return false;
					}
					
	    	}	
	    }				
		}		
	}
	switch (selectindex*1)
	{
		case -1:
		break;

		case 0:
		f_final.wlanDisabled_0.value = mf.multi_enable.value;
		//f_final.wlanMode_0.value = temp_apinfo0[2];
		f_final.ssid_0.value = mf.ssid.value;
		f_final.encrypt_0.value = mf.security_type.value;
		if(mf.security_type.value=="0")
		{
		}else if(mf.security_type.value=="1")
		{
			if(mf.wep_key_len.value==2){
				f_final.wep128Key1_0.value = test_wep_obj.value;
				f_final.wep128Key2_0.value = test_wep_obj.value;
				f_final.wep128Key3_0.value = test_wep_obj.value;
				f_final.wep128Key4_0.value = test_wep_obj.value;
			}	else{
				f_final.wep64Key1_0.value = test_wep_obj.value;
				f_final.wep64Key2_0.value = test_wep_obj.value;
				f_final.wep64Key3_0.value = test_wep_obj.value;
				f_final.wep64Key4_0.value = test_wep_obj.value;
			}
			f_final.authType_0.value = mf.auth_type.value;
			f_final.wep_0.value = mf.wep_key_len.value;
			f_final.wepKeyType_0.value = key_type;
			f_final.wepDefaultKey_0.value = mf.wep_def_key.value;
		}
		else if(mf.security_type.value>="2")
		{
			f_final.wpaCipher_0.value = mf.cipher_type.value;
			f_final.wpa2Cipher_0.value = mf.cipher_type.value;
			f_final.wpaAuth_0.value = mf.psk_eap.value;
			//f_final.wpaPSKFormat_0.value = temp_apinfo0[20];
			f_final.wpaPSK_0.value = mf.wpapsk1.value;
			f_final.enable1X_0.value = mf.wireless_ieee8021x_enabled.value;
			f_final.rsIpAddr1_0.value = mf.srv_ip1.value;
			f_final.rsPort1_0.value = mf.srv_port1.value;
			f_final.rsPassword1_0.value = mf.srv_sec1.value;
		}
		break;
		
		case 1:
		f_final.wlanDisabled_1.value = mf.multi_enable.value;
		f_final.ssid_1.value = mf.ssid.value;
		f_final.encrypt_1.value = mf.security_type.value;
		if(mf.security_type.value=="0")
		{
		}else if(mf.security_type.value=="1")
		{
			if(mf.wep_key_len.value==2){
				f_final.wep128Key1_1.value = test_wep_obj.value;
				f_final.wep128Key2_1.value = test_wep_obj.value;
				f_final.wep128Key3_1.value = test_wep_obj.value;
				f_final.wep128Key4_1.value = test_wep_obj.value;
			}	else
			{
				f_final.wep64Key1_1.value = test_wep_obj.value;
				f_final.wep64Key2_1.value = test_wep_obj.value;
				f_final.wep64Key3_1.value = test_wep_obj.value;
				f_final.wep64Key4_1.value = test_wep_obj.value;
			}
			f_final.authType_1.value = mf.auth_type.value;
			f_final.wep_1.value = mf.wep_key_len.value;
			f_final.wepKeyType_1.value = key_type;
			f_final.wepDefaultKey_1.value = mf.wep_def_key.value;
		}
		else if(mf.security_type.value>="2")
		{		
			f_final.wpaCipher_1.value = mf.cipher_type.value;
			f_final.wpa2Cipher_1.value = mf.cipher_type.value;
			f_final.wpaAuth_1.value = mf.psk_eap.value;
			f_final.wpaPSK_1.value = mf.wpapsk1.value;
			f_final.enable1X_1.value = mf.wireless_ieee8021x_enabled.value;
			f_final.rsIpAddr1_1.value = mf.srv_ip1.value;
			f_final.rsPort1_1.value = mf.srv_port1.value;
			f_final.rsPassword1_1.value = mf.srv_sec1.value;
		}
		break;
		
		case 2:
		f_final.wlanDisabled_2.value = mf.multi_enable.value;
		f_final.ssid_2.value = mf.ssid.value;
		f_final.encrypt_2.value = mf.security_type.value;
		if(mf.security_type.value=="0")
		{
		}else if(mf.security_type.value=="1")
		{
			if(mf.wep_key_len.value==2){
				f_final.wep128Key1_2.value = test_wep_obj.value;
				f_final.wep128Key2_2.value = test_wep_obj.value;
				f_final.wep128Key3_2.value = test_wep_obj.value;
				f_final.wep128Key4_2.value = test_wep_obj.value;
			}	else
			{
				f_final.wep64Key1_2.value = test_wep_obj.value;
				f_final.wep64Key2_2.value = test_wep_obj.value;
				f_final.wep64Key3_2.value = test_wep_obj.value;
				f_final.wep64Key4_2.value = test_wep_obj.value;
			}
			f_final.authType_2.value = mf.auth_type.value;
			f_final.wep_2.value = mf.wep_key_len.value;
			f_final.wepKeyType_2.value = key_type;
			f_final.wepDefaultKey_2.value = mf.wep_def_key.value;
		}
		else if(mf.security_type.value>="2")
		{
			f_final.wpaCipher_2.value = mf.cipher_type.value;
			f_final.wpa2Cipher_2.value = mf.cipher_type.value;
			f_final.wpaAuth_2.value = mf.psk_eap.value;
			f_final.wpaPSK_2.value = mf.wpapsk1.value;
			f_final.enable1X_2.value = mf.wireless_ieee8021x_enabled.value;
			f_final.rsIpAddr1_2.value = mf.srv_ip1.value;
			f_final.rsPort1_2.value = mf.srv_port1.value;
			f_final.rsPassword1_2.value = mf.srv_sec1.value;
		}
		break;		
	}
	f_final.settingsChanged.value = 1;

	f_final.submit();
}
function init()
{
	var DOC_Title= sw("txtTitle")+" : "+sw("txtSetup")+'/'+sw("txtMydlinkSetting");
	document.title = DOC_Title;	
	document.getElementById("AddSettings").value=	sw("txtAdd");		
	get_by_id("RestartNow").value = sw("txtRebootNow");
	get_by_id("RestartLater").value = sw("txtRebootLater");
	displayOnloadPage("<%getInfo("ok_msg")%>");

}
function page_cancel()
{
	page_load();
	init();
}
function print_keys(key_name, max_length)
{
	var str="";
	var key_value="";
	var field_size=decstr2int(max_length)+5;
	var hint_wording;
	var selectindex=get_by_id("multi_ssid_index").value;

	if(get_by_id("wireless_wep_key_len").value == 1 && max_length == "10") // 64
	{
		switch (selectindex*1)
		{
			case -1:
			key_value = "<%getIndexInfo("wepDefKey");%>";
			break;
	
			case 0:
			key_value = temp_apinfo0[8];
			break;
			
			case 1:
			key_value = temp_apinfo1[8];
			break;
			
			case 2:
			key_value = temp_apinfo2[8];
			break;		
		}
		if(key_value == "00000000000000000000000000" || key_value == "0000000000")
		{
			key_value="";
		}
		if(max_length=="10")	{hint_wording=sw("txt5AOr10H");}
		else					{hint_wording=sw("txt13AOr26H");}
		str="<table>";
		str+="\t<tr>";
		str+="\t\t<td class='r_tb' width='200'>WEP "+sw("txtPassword")+"</td>";
		str+="\t\t<td class='l_tb'>:&nbsp;&nbsp;"
		str+="<input type='text' id='"+key_name+"' name='"+key_name+"' maxlength='"+max_length+"' size='"+field_size+"' value='"+ key_value +"'>&nbsp;"+hint_wording+"</td>";
		str+="\t</tr>";
		str+="</table>";
		get_by_id("wep_key_64").innerHTML = str;	
	}
	else if(get_by_id("wireless_wep_key_len").value == 2 && max_length == "26") // 128
	{
		switch (selectindex*1)
		{
			case -1:
			key_value = "<%getIndexInfo("wepDefKey");%>";
			break;
	
			case 0:
			key_value = temp_apinfo0[12];
			break;
			
			case 1:
			key_value = temp_apinfo1[12];
			break;
			
			case 2:
			key_value = temp_apinfo2[12];
			break;		
		}
		if(key_value == "00000000000000000000000000" || key_value == "0000000000")
		{
			key_value="";
		}
		if(max_length=="10")	{hint_wording=sw("txt5AOr10H");}
		else					{hint_wording=sw("txt13AOr26H");}
		str="<table>";
		str+="\t<tr>";
		str+="\t\t<td class='r_tb' width='200'>WEP "+sw("txtPassword")+"</td>";
		str+="\t\t<td class='l_tb'>:&nbsp;&nbsp;"
		str+="<input type='text' id='"+key_name+"' name='"+key_name+"' maxlength='"+max_length+"' size='"+field_size+"' value='"+ key_value +"'>&nbsp;"+hint_wording+"</td>";
		str+="\t</tr>";
		str+="</table>";
		get_by_id("wep_key_128").innerHTML = str;	
	}
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
<div class="section">
<div class="section_head">
<h2><SCRIPT >ddw("txtMultipleSSID");</SCRIPT></h2>
<br>
<p></p>
</div></div>

<div class="box" id="mult-ssid_setting">
<h2><SCRIPT >ddw("txtMultSSIDSettings");</SCRIPT></h2>
<TABLE cellSpacing=1 cellPadding=1 width=525 border=0>
        <TBODY>
			<TR>
				<TD class=r_tb width=200><SCRIPT>ddw("txtMultipleIndex");</SCRIPT></TD>
				<TD class=l_tb>:&nbsp;
				<input type="hidden" id="multi_ssid_index" name="multi_ssid_index" value="-1" />
				<select id=index name="index0" onChange="on_change_index(this .value)" >
				<option value=-1 selected><SCRIPT>ddw("txtMultiplePrimary");</SCRIPT> </option>
				<option value=0	><SCRIPT>ddw("txtMultipleSSID")</SCRIPT>1</option>
				<option value=1 ><SCRIPT>ddw("txtMultipleSSID")</SCRIPT>2</option>
				<option value=2 ><SCRIPT>ddw("txtMultipleSSID")</SCRIPT>3</option>
				</select>
				</TD>
			</TR>

        <TR>
        	<input type="hidden" id="multi_enable" name="config.multi_enable" value=""/>
          <TD class=r_tb width=200><SCRIPT>ddw("txtEnable");</SCRIPT></TD>
          <TD class=l_tb>:&nbsp; 
		  <INPUT id=multi_enable_select name="multi_enable_select" value= 0  onclick=multi_enable_selector(this.checked) type=checkbox> 
          </TD>
        <TR>
 	
          <TD class=r_tb width=200><SCRIPT>ddw("txtWirelessNetworkNameSSID");</SCRIPT></TD>
          <TD class=l_tb>:&nbsp;&nbsp; <INPUT id=ssid maxLength=32 name="ssid0"  value=""> 
            &nbsp;<SCRIPT>ddw("txtAlsoCallSSID");</SCRIPT> </TD>

        </TR>
        </TBODY></TABLE>
</div>
      <DIV class=box id="show_security">
      <H2><SCRIPT>ddw("txtWirelessSecurityMode");</SCRIPT></H2>
      <TABLE cellSpacing=1 cellPadding=1 width=525 border=0>
        <TBODY>
				<input type="hidden" id="wireless_wepon" name="config.wireless.wepon" value="<%getIndexInfo("wep_enabled");%>"/>				
				<input type="hidden" id="wireless_wpa_enabled" name="config.wireless.wpa_enabled" value="<%getIndexInfo("wpa_enabled");%>" />
				<input type="hidden" id="wireless_wpa_mode" name="config.wireless.wpa_mode" value="<%getIndexInfo("wpa_mode");%>"/>
        <TR>
          <TD class=r_tb width=200><SCRIPT>ddw("txtSecurityMode");</SCRIPT></TD>
          <TD class=l_tb>:&nbsp;
            <select id=security_type onChange=on_change_security_type(this.value) name="method0" >
              <option value=0  ><SCRIPT>ddw("txtNONE");</SCRIPT></option>
              <option value=1  ><SCRIPT>ddw("txtWEPSecurity");</SCRIPT></option>
              <option value=2  ><SCRIPT>ddw("txtWPAPersonal");</SCRIPT></option>
              <option value=4  ><SCRIPT>ddw("txtWPA2");</SCRIPT></option>
              <option value=6  ><SCRIPT>ddw("txtWPA2Auto");</SCRIPT></option>
            </select></TD>
      </TR>
      </TBODY>
      </TABLE>
      </DIV>

      <DIV class=box id=show_wep style="DISPLAY: none">
      <H2>WEP</H2>
      <p><SCRIPT>ddw("txtWirelessStr4");</SCRIPT></p>
<p>	<SCRIPT>ddw("txtWirelessStr5");</SCRIPT></p>
      <TABLE cellSpacing=1 cellPadding=1 width=525 border=0>
        <TBODY>
        
        <TR>

          <TD class=r_tb width=200><SCRIPT>ddw("txtAuthentication");</SCRIPT></TD>
          <TD class=l_tb>:&nbsp;
        <input type="hidden" id="wireless_wep_auth_type" value="<%getIndexInfo("wep_auth");%>" />
		  	<SELECT id=auth_type name=authType value="<%getIndexInfo("wep_auth");%>" onChange="chg_wep_auth_type(this.value)">
          <OPTION value=0  ><SCRIPT>ddw("txtOpen");</SCRIPT></OPTION>
			 		<OPTION value=1  ><SCRIPT>ddw("txtSharedKey");</SCRIPT></OPTION></SELECT> </TD></TR>
        <TR>
          <TD class=r_tb width=200><SCRIPT>ddw("txtWepKeyLength");</SCRIPT></TD>

          <TD class=l_tb>:&nbsp; 
      <input type="hidden" id="wireless_wep_key_len" value="<%getIndexInfo("wep_mode");%>" />
		  <SELECT id=wep_key_len  size=1 name="wepKeyLen0" selectedValue=<%getIndexInfo("wep_mode");%> onChange="chg_wep_type(this.value)">
		   <OPTION value=1   >64Bit</OPTION> 
		   <OPTION value=2  >128Bit</OPTION></SELECT> 
          </TD></TR>

        <TR>
          <TD class=r_tb width=200><SCRIPT>ddw("txtDefaultWEPKey");</SCRIPT></TD>
          <TD class=l_tb>:&nbsp; 
     <input type="hidden" id="wireless_wep_def_key" value="<%getIndexInfo("defaultKeyId");%>" /> 
		 <SELECT id=wep_def_key name="wep_def_key0" selectedValue=<%getIndexInfo("defaultKeyId");%> onChange="chg_wep_def_key(this.value)">
        <OPTION value=0 >WEP Key 1</OPTION> 
			  <OPTION value=1 >WEP Key 2</OPTION> 
			  <OPTION value=2 >WEP Key 3</OPTION> 
			  <OPTION value=3 >WEP Key 4</OPTION>

			  </SELECT> </TD></TR></TBODY></TABLE>

	<table>
	</table>		
	<div id="wep_key_64"	style="display:none"></div>
	<div id="wep_key_128" style="display:none"></div>

      </DIV>

      <DIV class=box id=show_wpa style="DISPLAY: none">
      <DIV id=title_wpa style="DISPLAY: none">

      <H2>WPA-Personal</H2>
      <P><SCRIPT>ddw("txtWirelessStr7_1");</SCRIPT></P></DIV>
      	
      <DIV id=title_wpa2 style="DISPLAY: none">
      <H2>WPA2</H2>
      <P><SCRIPT>ddw("txtWirelessStr7_2");</SCRIPT></P></DIV>
      	
      <DIV id=title_wpa2_auto style="DISPLAY: none">
      <H2><SCRIPT>ddw("txtWPAWPA2");</SCRIPT></H2>

      <P><SCRIPT>ddw("txtWirelessStr7");</SCRIPT></P></DIV>
      <DIV>
      <TABLE>
        <TBODY>
        <TR>        	
          <TD class=r_tb width=200><SCRIPT>ddw("txtCipherType");</SCRIPT></TD>
          <TD class=l_tb>: 
      <input type="hidden" id="wireless_cipher_type" value="<%getIndexInfo("wpaCipher");%>" />
		  <SELECT id=cipher_type name="ciphersuite0" selectValue="<%getIndexInfo("wpaCipher");%>"> 
<option value=3><SCRIPT>ddw("txtTKIPandAES");</SCRIPT></option>
		  <OPTION value=1>TKIP</OPTION> 
		  <OPTION value=2>AES</OPTION>
		  
		  <!--<OPTION  value=4>Auto</OPTION>-->

		  </SELECT> </TD></TR>
        <TR>
        	<input type="hidden" id="wireless_ieee8021x_enabled" name="config.wireless.ieee8021x_enabled" value="<%getIndexInfo("wpa_enterprise_enabled");%>" />
          <TD class=r_tb width=200>PSK / EAP</TD>
          <TD class=l_tb>:&nbsp; 
		  <SELECT id=psk_eap onchange=chg_psk_eap(this.value) name="wpaAuth0"> 
		  <OPTION value=2  >PSK</OPTION>
			<OPTION value=1  >EAP</OPTION> 
			 </SELECT>
			</TD></TR></TBODY></TABLE></DIV>

      <DIV id=psk_setting style="DISPLAY: none">
      <TABLE>
        <TBODY>
        <TR>
          <TD class=r_tb width=200><SCRIPT>ddw("txtPreSharedKey");</SCRIPT></TD>
          <TD class=l_tb>:&nbsp;
		   <INPUT id=wpapsk1 type="text" maxLength=64 size=40 name="pskValue0"  id=wpapsk1 > </TD></TR>

        <INPUT type="hidden" id="wpa_key_type" name="pskFormat0">
        
		  <tr>
			  <td class="r_tb" width="200"></td>
				<td class="l_tb">&nbsp;&nbsp;<SCRIPT>ddw("txtWpaKeyType");</SCRIPT></td>
			</tr>
					
					</TBODY></TABLE></DIV>
      <DIV id=eap_setting style="DISPLAY: none">
      <TABLE>

        <TBODY>
        <TR>
          <TD class=l_tb>802.1X</TD></TR>
        <TR>
          <TD>
            <TABLE>
              <TBODY>
              <TR>

                <TD class=r_tb width=120><SCRIPT>ddw("txtRADIUSserverIPAddress");</SCRIPT>&nbsp;</TD>
                <TD class=l_tb><SCRIPT>ddw("txtIPAddress");</SCRIPT></TD>
                <TD class=l_tb>:&nbsp; 
				<INPUT id=srv_ip1 maxLength=15 size=15  name="srv_ip10" value="<%getInfo("rsIp1");%>" > </TD></TR>
              <TR>
                <TD class=r_tb width=120></TD>
                <TD class=l_tb><SCRIPT>ddw("txtPort");</SCRIPT></TD>

                <TD class=l_tb>:&nbsp; 
				<INPUT id=srv_port1 maxLength=5 size=8 name="srv_port10" value="<%getInfo("rsPort1");%>"> </TD></TR>
              <TR>
                <TD class=r_tb width=120></TD>
                <TD class=l_tb><SCRIPT>ddw("txtSharedSecret");</SCRIPT></TD>
                <TD class=l_tb>:&nbsp; 
				<INPUT id=srv_sec1 type="password" maxLength=64 size=50 name="srv_sec10" value="<%getInfo("rsPassword1");%>"> </TD></TR>

              </TBODY></TABLE></TD></TR></TBODY>
			  </TABLE>
			  </DIV>		  
			  </DIV>
			  <br>
<div align="right"><input class="button_submit" type="button" id="AddSettings" name="AddSettings" value="" onclick="page_submit()"/></div>			  
      <DIV class=box id="show_multi_ap">
      <H2><SCRIPT>ddw("txtMultipleSSID");</SCRIPT></H2>
		<div id="multi_apinfo_show" style="display:block;"></div>
    </div>		
<!--@ENDOPTIONAL@-->
</form>
<form name="final_form" id="final_form" method="post" action="/goform/formSetMuti">
	
<input type="hidden" name="ACTION_POST"			value="final">
<input type="hidden" name="wlanDisabled_0"			value="">
<input type="hidden" name="wlanMode_0"			value="">
<input type="hidden" name="ssid_0"			value="">
<input type="hidden" name="encrypt_0"			value="">
<input type="hidden" name="authType_0"			value="">
<input type="hidden" name="wep_0"			value="">
<input type="hidden" name="wepKeyType_0"			value="">
<input type="hidden" name="wep64Key1_0"			value="">
<input type="hidden" name="wep64Key2_0"			value="">
<input type="hidden" name="wep64Key3_0"			value="">
<input type="hidden" name="wep64Key4_0"			value="">
<input type="hidden" name="wep128Key1_0"			value="">
<input type="hidden" name="wep128Key2_0"			value="">
<input type="hidden" name="wep128Key3_0"			value="">
<input type="hidden" name="wep128Key4_0"			value="">
<input type="hidden" name="wepDefaultKey_0"			value="">
<input type="hidden" name="wpaCipher_0"			value="">
<input type="hidden" name="wpa2Cipher_0"			value="">
<input type="hidden" name="wpaAuth_0"			value="">
<input type="hidden" name="wpaPSKFormat_0"			value="">
<input type="hidden" name="wpaPSK_0"			value="">
<input type="hidden" name="enable1X_0"			value="">
<input type="hidden" name="rsIpAddr1_0"			value="">
<input type="hidden" name="rsPort1_0"			value="">
<input type="hidden" name="rsPassword1_0"			value="">

<input type="hidden" name="wlanDisabled_1"			value="">
<input type="hidden" name="wlanMode_1"			value="">
<input type="hidden" name="ssid_1"			value="">
<input type="hidden" name="encrypt_1"			value="">
<input type="hidden" name="authType_1"			value="">
<input type="hidden" name="wep_1"			value="">
<input type="hidden" name="wepKeyType_1"			value="">
<input type="hidden" name="wep64Key1_1"			value="">
<input type="hidden" name="wep64Key2_1"			value="">
<input type="hidden" name="wep64Key3_1"			value="">
<input type="hidden" name="wep64Key4_1"			value="">
<input type="hidden" name="wep128Key1_1"			value="">
<input type="hidden" name="wep128Key2_1"			value="">
<input type="hidden" name="wep128Key3_1"			value="">
<input type="hidden" name="wep128Key4_1"			value="">
<input type="hidden" name="wepDefaultKey_1"			value="">
<input type="hidden" name="wpaCipher_1"			value="">
<input type="hidden" name="wpa2Cipher_1"			value="">
<input type="hidden" name="wpaAuth_1"			value="">
<input type="hidden" name="wpaPSKFormat_1"			value="">
<input type="hidden" name="wpaPSK_1"			value="">
<input type="hidden" name="enable1X_1"			value="">
<input type="hidden" name="rsIpAddr1_1"			value="">
<input type="hidden" name="rsPort1_1"			value="">
<input type="hidden" name="rsPassword1_1"			value="">

<input type="hidden" name="wlanDisabled_2"			value="">
<input type="hidden" name="wlanMode_2"			value="">
<input type="hidden" name="ssid_2"			value="">
<input type="hidden" name="encrypt_2"			value="">
<input type="hidden" name="authType_2"			value="">
<input type="hidden" name="wep_2"			value="">
<input type="hidden" name="wepKeyType_2"			value="">
<input type="hidden" name="wep64Key1_2"			value="">
<input type="hidden" name="wep64Key2_2"			value="">
<input type="hidden" name="wep64Key3_2"			value="">
<input type="hidden" name="wep64Key4_2"			value="">
<input type="hidden" name="wep128Key1_2"			value="">
<input type="hidden" name="wep128Key2_2"			value="">
<input type="hidden" name="wep128Key3_2"			value="">
<input type="hidden" name="wep128Key4_2"			value="">
<input type="hidden" name="wepDefaultKey_2"			value="">
<input type="hidden" name="wpaCipher_2"			value="">
<input type="hidden" name="wpa2Cipher_2"			value="">
<input type="hidden" name="wpaAuth_2"			value="">
<input type="hidden" name="wpaPSKFormat_2"			value="">
<input type="hidden" name="wpaPSK_2"			value="">
<input type="hidden" name="enable1X_2"			value="">
<input type="hidden" name="rsIpAddr1_2"			value="">
<input type="hidden" name="rsPort1_2"			value="">
<input type="hidden" name="rsPassword1_2"			value="">

<input type="hidden" id="settingsChanged" name="settingsChanged" value="0"/>
</form>
<!-- InstanceEndEditable --></div></td>
<td id="sidehelp_container">
<div id="help_text">
<!-- InstanceBeginEditable name="Help_Text" -->
<strong><SCRIPT >ddw("txtHelpfulHints");</SCRIPT>...</strong>
<p class="more">
<!-- Link to more help -->
<a href="../Help/Advanced.asp#Advanced_Wireless" onclick="return jump_if();">
<SCRIPT >ddw("txtMore");</SCRIPT>...</a>										</p>
<!-- InstanceEndEditable -->
</div></td></tr></table>
<SCRIPT >Write_footerContainer();</SCRIPT>
<SCRIPT >print_copyright();</SCRIPT>
</div><!-- outside -->
</body>
<!-- InstanceEnd -->
</html>

