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
		this.wifip  = PXML.FindModule("WIFI.WLAN-1");
		if (!this.wifip)
		{
			BODY.ShowAlert("InitValue() ERROR!!!");
			return false;
		}
		this.wifip = GPBT(this.wifip+"/wifi", "entry", "uid", "WIFI-1", false);
		if (XG(this.wifip+"/wps/enable")=="1")	this.en_wps = true;
		
		if (!this.en_wps) OBJ("wldevicesetup").disabled = true;
		else OBJ("wldevicesetup").disabled = false;

		return true;
    },
    PreSubmit: function() { return null; },
    IsDirty: null,
    Synchronize: function() {},
	// The above are MUST HAVE methods ...
	///////////////////////////////////////////////////////////////////////
	en_wps: false,
	wifip: null 
}
</script>
