<span class="t3-form-palette-field class-main15">
	<span onmouseout="this.className='t3-tceforms-input-wrapper-datetime';" onmouseover="if (document.getElementById('tceforms-datetimefield-###ID###').value) {this.className='t3-tceforms-input-wrapper-datetime-hover';} else {this.className='t3-tceforms-input-wrapper-datetime';};" class="t3-tceforms-input-wrapper-datetime">
		<span onclick="document.getElementById('tceforms-datetimefield-###ID###').value='';document.getElementById('tceforms-datetimefield-###ID###').focus();" class="t3-icon t3-icon-actions t3-icon-actions-input t3-icon-input-clear t3-tceforms-input-clearer" tag="a">&nbsp;</span>
		<input type="text" maxlength="20" style="width: 163px; " value="###VALUE###" name="data[###TABLE###][###UID###][tx_aoe_dbsquenzer_protectoverwrite_till]" class="formField1 tceforms-textfield tceforms-datetimefield" id="tceforms-datetimefield-###ID###" />
		<span class="t3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-pick-date" id="picker-tceforms-datetimefield-###ID###" style="cursor:pointer;">&nbsp;</span>
	</span>
</span>
 ###LABEL_MODE### 
<select name="data[###TABLE###][###UID###][tx_aoe_dbsquenzer_protectoverwrite_mode]">
	<option value="0" ###CONFLICT_MODE###>###LABEL_MODE_CONFLICT###</option>
	<option value="1" ###OVERWIRTE_MODE###>###LABEL_MODE_OVERWIRTE###</option>
</select>