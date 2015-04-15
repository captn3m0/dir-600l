<? include "/htdocs/webinc/body/draw_elements.php"; ?>
		<tr>
			<td rowspan="2" class="centered">
				<!-- the uid of PFWD -->
				<input type="hidden" id="uid_<?=$INDEX?>" value="">
				<input id="en_<?=$INDEX?>" type="checkbox" />
			</td>
			<td><?echo i18n("Name");?><br /><input id="dsc_<?=$INDEX?>" type="text" size="20" maxlength="15" /></td>
			<td class="bottom">
				<input type="button" value="<<" class="arrow" onClick="OnClickAppArrow('<?=$INDEX?>');" />
				<span id="span_app_<?=$INDEX?>"></span>
			</td>
			<td>
				<?echo i18n("Public Port");?><br />
				<input id="pub_start_<?=$INDEX?>" type="text" size="5" maxlength="5" /> ~
				<input id="pub_end_<?=$INDEX?>" type="text" size="5" maxlength="5" />
			</td>
			<td class="centered">
				<?echo i18n("Traffic Type");?><br />
				<select id="pro_<?=$INDEX?>">
					<option value="TCP+UDP"><?echo i18n("All");?></option>				    
					<option value="TCP">TCP</option>
					<option value="UDP">UDP</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<?echo i18n("IP Address");?><br />
				<input id="ip_<?=$INDEX?>" type="text" size="20" maxlength="15" />
			</td>
			<td class="bottom">
				<input type="button" value="<<" class="arrow" onClick="OnClickPCArrow('<?=$INDEX?>');" />
				<? DRAW_select_dhcpclist("LAN-1","pc_".$INDEX, i18n("Computer Name"), "",  "", "1", "broad"); ?>
			</td>
			<td>
				<?echo i18n("Private Port");?><br />
				<input id="pri_start_<?=$INDEX?>" type="text" size="5" maxlength="5" /> ~
				<input id="pri_end_<?=$INDEX?>" type="text" size="5" disabled />
			</td>
			<?
			if ($FEATURE_NOSCH != "1")
			{
				echo '<td class="centered">\n'.i18n("Schedule").'<br />\n';
				DRAW_select_sch("sch_".$INDEX, i18n("Always"), "-1", "", "0", "narrow");
				echo '</td>\n';
			}
			?>
		</tr>

