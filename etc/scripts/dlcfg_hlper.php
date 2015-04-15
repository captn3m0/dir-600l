<?
if ($ACTION=="STARTTODOWNLOADFILE")
{
	/* hendry,fix :I want a node that is very UNIQUE !! */
	set("/runtime/hendry_user_setting_tmp","");
	mov("/device/account","/runtime/hendry_user_setting_tmp");
}
else if ($ACTION=="ENDTODOWNLOADFILE")
{
	mov("/runtime/hendry_user_setting_tmp/account","/device");
	del("/runtime/hendry_user_setting_tmp");
}
?>
