<style>
/* The CSS is only for this page.
 * Notice:
 *	If the items are few, we put them here,
 *	If the items are a lot, please put them into the file, htdocs/web/css/$TEMP_MYNAME.css.
 */
select.broad	{ width: 126px; }
select.narrow	{ width: 65px; }
</style>

<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "PFWD.NAT-1",
	OnLoad: function()
	{
		/* draw the 'Application Name' select */
		var str = "";
		for(var i=1; i<=<?=$PFWD_MAX_COUNT?>; i+=1)
		{
			str = "";
			str += '<select id="app_'+i+'" class="broad">'; // Joseph Chao
			for(var j=0; j<this.apps.length; j+=1)
				str += '<option value="'+j+'">'+this.apps[j].name+'</option>';
			str += '</select>';
			OBJ("span_app_"+i).innerHTML = str;
		}
		if (!this.rgmode)
		{
			BODY.DisableCfgElements(true);
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function(code, result) { return false; },
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var p = PXML.FindModule("PFWD.NAT-1");
		if (p === "") alert("ERROR!");
		p += "/nat/entry/portforward";
		TEMP_RulesCount(p, "rmd");
		var count = XG(p+"/count");
		var netid = COMM_IPv4NETWORK(this.lanip, this.mask);
		for (var i=1; i<=<?=$PFWD_MAX_COUNT?>; i+=1)
		{
			var b = p+"/entry:"+i;
			var offset = XG(b+"/external/end") - XG(b+"/external/start");
			OBJ("uid_"+i).value = XG(b+"/uid");
			OBJ("en_"+i).checked = XG(b+"/enable")==="1";
			OBJ("dsc_"+i).value = XG(b+"/description");
			OBJ("pub_start_"+i).value = XG(b+"/external/start");
			OBJ("pub_end_"+i).value = XG(b+"/external/end");
			OBJ("pri_start_"+i).value = XG(b+"/internal/start");
			if (OBJ("pri_start_"+i).value!="")
				OBJ("pri_end_"+i).value = S2I(OBJ("pri_start_"+i).value) + offset;
			else
				OBJ("pri_end_"+i).value = "";
			COMM_SetSelectValue(OBJ("pro_"+i), (XG(b+"/protocol")=="")? "TCP+UDP":XG(b+"/protocol"));
			<?
			if ($FEATURE_NOSCH!="1")
				echo 'COMM_SetSelectValue(OBJ("sch_"+i), (XG(b+"/schedule")=="")? "-1":XG(b+"/schedule"));\n';
			?>
			var hostid = XG(b+"/internal/hostid");
			if (hostid !== "")	OBJ("ip_"+i).value = COMM_IPv4IPADDR(netid, this.mask, hostid);
			else				OBJ("ip_"+i).value = "";
			OBJ("pc_"+i).value = "";
		}
		return true;
	},
	PreSubmit: function()
	{
		var p = PXML.FindModule("PFWD.NAT-1");
		p += "/nat/entry/portforward";
		var old_count = parseInt(XG(p+"/count"), 10);
		var cur_count = 0;
		var cur_seqno = parseInt(XG(p+"/seqno"), 10);
		/* delete the old entries
		 * Notice: Must delte the entries from tail to head */
		while(old_count > 0)
		{
			XD(p+"/entry:"+old_count);
			old_count -= 1;
		}
		/* update the entries */
		for (var i=1; i<=<?=$PFWD_MAX_COUNT?>; i+=1)
		{
			if (OBJ("pub_start_"+i).value!="" && !TEMP_IsDigit(OBJ("pub_start_"+i).value))
			{
				BODY.ShowAlert("<?echo i18n("The input public port range is invalid.");?>");
				OBJ("pub_start_"+i).focus();
				return null;
			}
			if (OBJ("pub_end_"+i).value!="" && !TEMP_IsDigit(OBJ("pub_end_"+i).value))
			{
				BODY.ShowAlert("<?echo i18n("The input public port range is invalid.");?>");
				OBJ("pub_end_"+i).focus();
				return null;
			}
			if (OBJ("pri_start_"+i).value!="" && !TEMP_IsDigit(OBJ("pri_start_"+i).value))
			{
				BODY.ShowAlert("<?echo i18n("The input private port range is invalid.");?>");
				OBJ("pri_start_"+i).focus();
				return null;
			}
			if (OBJ("ip_"+i).value!="" && !TEMP_CheckNetworkAddr(OBJ("ip_"+i).value, null, null))
			{
				BODY.ShowAlert("<?echo i18n("Invalid host IP address.");?>");
				OBJ("ip_"+i).focus();
				return null;
			}
			/* if the description field is empty, it means to remove this entry,
			 * so skip this entry. */
			if (OBJ("dsc_"+i).value!=="")
			{
				// Make sure the different rules have different names.
				for (var j=1; j<=cur_count; j+=1)
				{
					var b = p+"/entry:"+j;					
					if( XG(b+"/description")===OBJ("dsc_"+i).value  ) 
					{
						BODY.ShowAlert("<?echo i18n("The different rules could not set the same name.");?>");
						OBJ("dsc_"+i).focus();
						return null;
					}
				}
				
				cur_count+=1;
				var b = p+"/entry:"+cur_count;
				XS(b+"/enable",			OBJ("en_"+i).checked ? "1" : "0");
				XS(b+"/uid",			OBJ("uid_"+i).value);
				if (OBJ("uid_"+i).value == "")
				{
					XS(b+"/uid",	"PFWD-"+cur_seqno);
					cur_seqno += 1;
				}
				<?
				if ($FEATURE_NOSCH!="1")
					echo 'XS(b+"/schedule",		(OBJ("sch_"+i).value==="-1") ? "" : OBJ("sch_"+i).value);\n';
				?>
				XS(b+"/description",	OBJ("dsc_"+i).value);
				XS(b+"/protocol",		OBJ("pro_"+i).value);
				XS(b+"/internal/inf",	"LAN-1");
				if (OBJ("ip_"+i).value == "") XS(b+"/internal/hostid", "");
				else XS(b+"/internal/hostid",COMM_IPv4HOST(OBJ("ip_"+i).value, this.mask));
				XS(b+"/internal/start",	OBJ("pri_start_"+i).value);
				XS(b+"/external/start",	OBJ("pub_start_"+i).value);
				XS(b+"/external/end",	OBJ("pub_end_"+i).value);
			}
		}
		
		XS(p+"/count", cur_count);
		XS(p+"/seqno", cur_seqno);
		return PXML.doc;
	},
	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////
	rgmode: <?if (query("/runtime/device/layout")=="bridge") echo "false"; else echo "true";?>,
	apps: [	{name: "<?echo i18n("Application Name");?>",
										protocol:"TCP", port:{start:"",		end:""}},
			{name: "FTP",				protocol:"TCP", port:{start:"21",	end:"21"}},
			{name: "HTTP",				protocol:"TCP", port:{start:"80",	end:"80"}},
			{name: "HTTPS",				protocol:"TCP", port:{start:"443",	end:"443"}},
			{name: "DNS",				protocol:"UDP", port:{start:"53",	end:"53"}},
			{name: "SMTP",				protocol:"TCP", port:{start:"25",	end:"25"}},
			{name: "POP3",				protocol:"TCP", port:{start:"110",	end:"110"}},
			{name: "Telnet",			protocol:"TCP", port:{start:"23",	end:"23"}},
			{name: "IPSec",				protocol:"UDP", port:{start:"500",	end:"500"}},
			{name: "PPTP",				protocol:"TCP", port:{start:"1723",	end:"1723"}},
			{name: "DCS-1000",			protocol:"TCP", port:{start:"1720",	end:"1720"}},
			{name: "DCS-2000/DCS-5300",	protocol:"TCP", port:{start:"801",	end:"801"}},
			{name: "i2eye",				protocol:"TCP", port:{start:"800",	end:"800"}}
		  ],
	lanip: "<? echo INF_getcurripaddr("LAN-1"); ?>",
	mask: "<? echo INF_getcurrmask("LAN-1"); ?>",
	CursorFocus: function(node)
	{
		var i = node.lastIndexOf("entry:");
		if(node.charAt(i+7)==="/") var idx = parseInt(node.charAt(i+6), 10);
		else var idx = parseInt(node.charAt(i+6), 10)*10 + parseInt(node.charAt(i+7), 10);
		var indx = 1;
		var valid_dsc_cnt = 0;		
		for(indx=1; indx <= <?=$PFWD_MAX_COUNT?>; indx++)
		{
			if(OBJ("dsc_"+indx).value!=="") valid_dsc_cnt++;
			if(valid_dsc_cnt===idx) break;
		}
		if(node.match("description"))			OBJ("dsc_"+indx).focus();
		else if(node.match("internal/hostid"))	OBJ("ip_"+indx).focus();
		else if(node.match("external/start"))	OBJ("pub_start_"+indx).focus();
		else if(node.match("internal/start"))	OBJ("pri_start_"+indx).focus();
	}
};

function OnClickAppArrow(idx)
{
	var i = OBJ("app_"+idx).value;
	OBJ("dsc_"+idx).value = (i==="0") ? "" : PAGE.apps[i].name;
	OBJ("pro_"+idx).value = PAGE.apps[i].protocol;
	OBJ("pub_start_"+idx).value = OBJ("pri_start_"+idx).value = PAGE.apps[i].port.start;
	OBJ("pub_end_"+idx).value = OBJ("pri_end_"+idx).value = PAGE.apps[i].port.end;
	OBJ("app_"+idx).selectedIndex = 0;
}
function OnClickPCArrow(idx)
{
	OBJ("ip_"+idx).value = OBJ("pc_"+idx).value;
	OBJ("pc_"+idx).selectedIndex = 0;
}


</script>
