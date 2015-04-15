<html>
<script text="text/script">
var lang = "<% getLangInfo("lang");%>";
</script>
<script type="text/javascript" src="../ubicom.js"></script>
<% getLangInfo("LangPath");%>
<body>
<form action="" method=POST name="aaa" id="aaa">
<div class="box" width="560">
<table class="formlisting" cellpadding="0" cellspacing="1" border="1" bgcolor="#DFDFDF">
<col width="140">
<col width="160">
<col width="180">
<col width="80">
        <tr>
                <th class="duple"><SCRIPT >ddw("txtHostName");</SCRIPT></th>
                <th class="duple"><SCRIPT >ddw("txtIPAddress");</SCRIPT></th>
                <th class="duple"><SCRIPT >ddw("txtMACAddress");</SCRIPT></th>
                <th class="duple"><SCRIPT >ddw("txtStatus");</SCRIPT></th>
        </tr>
        <% getInfo("wol_client_list"); %>
</table>
</div>
</form>
</body>
</html>
