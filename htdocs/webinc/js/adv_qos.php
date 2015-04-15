<script type="text/javascript">
function Page() {}
Page.prototype =
{
	services: "QOS",
	OnLoad: function()
	{
		if (!this.rgmode)
		{
			BODY.DisableCfgElements(true);
		}
	},
	OnUnload: function() {},
	OnSubmitCallback: function () {},
	InitValue: function(xml)
	{
		PXML.doc = xml;
		var qos = PXML.FindModule("QOS");

		if (qos === "")		{ alert("InitValue ERROR!"); return false; }		
		OBJ("en_qos").checked = (XG(qos+"/device/qos/enable")==="1");
		OBJ("auto_speed").checked = (XG(qos+"/device/qos/autobandwidth")==="1");
		OBJ("upstream").value = XG(qos+"/inf/bandwidth/upstream");
		OBJ("conntype").value = XG(qos+"/inf/bandwidth/type");
		//////////////////////////////////////////// by bisonpan
		if(XG(qos+"/device/qos/enable")==="1")
		{
			var inf2="<?echo query("/runtime/inf:2/uid");?>";

			if( inf2 == "WAN-1" )
			OBJ("st_type").innerHTML  = "Cable Or Other Broadband Network";
			else
			OBJ("st_type").innerHTML  = "xDSL Or Other Frame Relay Network";
		}
		else
		{
			OBJ("st_type").innerHTML = "";
		}
		if(XG(qos+"/device/qos/autobandwidth")==="1")
		{
			var bwup=XG(qos+"/runtime/device/qos/bwup");

			if( bwup == "" || bwup == "0" )
			OBJ("st_upstream").innerHTML = "Not Estimated";
			else
			OBJ("st_upstream").innerHTML  = bwup+" kbps";
		}
		else
		{
			OBJ("st_upstream").innerHTML = "Not Estimated";
		}
		////////////////////////////////////////////////////////
		this.OnClickQosEnable();
				return true;
	},
	
	PreSubmit: function()
	{
		var qos = PXML.FindModule("QOS");
		
		if( !(XG(qos+"/device/qos/enable")==="1") && OBJ("en_qos").checked )    // QoS has been reenabled, should also redetect.
			XS(qos+"/runtime/device/qos/bwup",	"");
		XS(qos+"/device/qos/enable", OBJ("en_qos").checked ? "1":"0");
		XS(qos+"/device/qos/autobandwidth", OBJ("auto_speed").checked ? "1":"0");
		XS(qos+"/inf/bandwidth/upstream",	OBJ("upstream").value);
		XS(qos+"/inf/bandwidth/type",	OBJ("conntype").value);
		return PXML.doc;
	},

	IsDirty: null,
	Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	rgmode: <?if (query("/runtime/device/layout")=="bridge") echo "false"; else echo "true";?>,
	OnClickQosEnable: function()
	{
		if (OBJ("en_qos").checked)
		{
			OBJ("auto_speed").setAttribute("modified", "false");
			OBJ("auto_speed").disabled = false;
			this.OnClickQosAuto();
			OBJ("conntype").setAttribute("modified", "false");
			OBJ("conntype").disabled = false;
		}
		else
		{
			OBJ("auto_speed").setAttribute("modified", "ignore");
			OBJ("auto_speed").disabled = true;
			OBJ("upstream").setAttribute("modified", "ignore");
			OBJ("upstream").disabled = true;
			OBJ("select_upstream").disabled = true;
			OBJ("conntype").setAttribute("modified", "ignore");
			OBJ("conntype").disabled = true;
		}
	},
	OnClickQosAuto: function()
	{
		if (OBJ("auto_speed").checked)
		{
			OBJ("upstream").setAttribute("modified", "ignore");
			OBJ("upstream").disabled = true;
			OBJ("select_upstream").disabled = true;
		}
		else
		{
			OBJ("upstream").setAttribute("modified", "false");
			OBJ("upstream").disabled = false;
			OBJ("select_upstream").disabled = false;
		}
	},
	OnChangeQosUpstream: function()
	{
		OBJ("upstream").value = OBJ("select_upstream").value;
		OBJ("select_upstream").value=0;
	}
}
</script>
