<?
/**
 * Copyright (c) 21/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

if(!$USER->IsAdmin()) return;
CModule::IncludeModule("wedebug.ruble");

IncludeModuleLangFile(__FILE__);

$arAllOptions = Array(
	Array("web_ruble_font_char", GetMessage("WEB_RUBLE_FONT_CHAR"), "a", Array("radio-with-image", 5)),
	Array("web_ruble_symbol_location", GetMessage("WEB_RUBLE_SYMBOL_LOCATION"), "R", Array("radio", 1), array("R"=>GetMessage("WEB_RUBLE_SYMBOL_LOCATION_R"), "L"=>GetMessage("WEB_RUBLE_SYMBOL_LOCATION_L"))),
	Array("web_ruble_2_0", GetMessage("WEB_RUBLE_2_0"), "", Array("checkbox",'')),
	Array("web_ruble_add_space", GetMessage("WEB_RUBLE_ADD_SPACE"), "", Array("checkbox",'')),
	Array("web_ruble_regex_exclude", GetMessage("WEB_RUBLE_REGEX_EXCLUDE"), "", Array("textarea", 50, 10)),
	Array("web_ruble_regex_include", GetMessage("WEB_RUBLE_REGEX_INCLUDE"), "", Array("textarea", 50, 10)),
	Array("web_ruble_skip_post", GetMessage("WEB_RUBLE_SKIP_POST"), "", Array("checkbox",'')),
	Array("web_ruble_own_tag", GetMessage("WEB_RUBLE_OWN_TAG"), "", Array("checkbox",'')),
	Array("web_ruble_js_currency_replace", GetMessage("WEB_RUBLE_JS_CURRENCY_REPLACE"), "", Array("checkbox",'')),
	Array("web_ruble_additional_code", GetMessage("WEB_RUBLE_ADDITIONAL_CODE"), "", Array("textarea", 50, 14)),
	Array("web_ruble_title", GetMessage("WEB_RUBLE_TITLE"), "", Array("text", 50)),
);
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("WEB_RUBLE_TAB_1"), "ICON" => "web_ruble_params", "TITLE" => GetMessage("WEB_RUBLE_TAB_1_TITLE")),
);
$tabControl = new CAdminTabControl("tabControl", $aTabs);
if($REQUEST_METHOD=="POST" && strlen($Update.$Apply.$RestoreDefaults)>0 && check_bitrix_sessid()) {
	if(strlen($RestoreDefaults)>0) {
		COption::RemoveOption("web.ruble");
	} else {
		foreach($arAllOptions as $arOption) {
			$name=$arOption[0];
			$val=$_REQUEST[$name];
			if($arOption[2][0]=="checkbox" && $val!="Y")
				$val="N";
			COption::SetOptionString("web.ruble", $name, $val, $arOption[1]);
		}
	}
	if(strlen($Update)>0 && strlen($_REQUEST["back_url_settings"])>0)
		LocalRedirect($_REQUEST["back_url_settings"]);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($mid)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}

function ShowHint($Text) {
	$Code = ToLower(RandString(12));
	return '<span id="hint_'.$Code.'"></span><script>BX.hint_replace(BX("hint_'.$Code.'"), "'.GetMessage($Text).'");</script>';
}

$tabControl->Begin();
?>

<form method="post" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=urlencode($mid)?>&amp;lang=<?echo LANGUAGE_ID?>">
	<?$tabControl->BeginNextTab();?>
		<tr>
			<td width="50%"><?=ShowHint('WEB_RUBLE_EXAMPLE_TIP');?> <?=GetMessage("WEB_RUBLE_EXAMPLE");?>:</td>
			<td style="font-size:24px;"><?=CurrencyFormat_Ruble(mt_rand(10000, 999999).".34", "RUB", 2);?></td>
		</tr>
		<?
		foreach($arAllOptions as $arOption):
			$val = COption::GetOptionString("web.ruble", $arOption[0], $arOption[2]);
			$OptionValues = $arOption[4];
			$type = $arOption[3];
		?>
		<tr>
			<td valign="top" width="50%"><?=ShowHint(ToUpper($arOption[0]).'_TIP');?>  <?
				if($type[0]=="checkbox")
					echo "<label for=\"".htmlspecialchars($arOption[0])."\">".$arOption[1]."</label>";
				else
					echo $arOption[1];?>:</td>
			<td valign="top" width="50%">
				<?if($type[0]=="checkbox"):?>
					<input type="checkbox" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="Y"<?if($val=="Y")echo" checked='checked'";?> />
				<?elseif($type[0]=="text"):?>
					<input type="text" size="<?echo $type[1]?>" maxlength="255" value="<?echo htmlspecialchars($val)?>" name="<?echo htmlspecialchars($arOption[0])?>" />
				<?elseif($type[0]=="textarea"):?>
					<textarea cols="<?echo $type[1]?>" rows="<?echo $type[2]?>" name="<?echo htmlspecialchars($arOption[0])?>"><?echo htmlspecialchars($val)?></textarea>
				<?elseif($type[0]=="radio"):?>
					<?foreach ($OptionValues as $OptionValue => $OptionName):?>
						<label>
							<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="<?=$OptionValue?>"<?if($OptionValue==$val)echo" checked='checked'";?> />
							<?=$OptionName?>
						</label><br/>
					<?endforeach?>
				<?elseif($type[0]=="radio-with-image"):?>
					<label style="font-size:14px; font-family:'Arial';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="a" <?if($val=="a")echo" checked";?> />
						Arial Regular
					</label><br/>
					<label style="font-size:14px; font-family:'Arial'; font-style:italic;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="b" <?if($val=="b")echo" checked";?> />
						Arial Italic 
					</label><br/>
					<label style="font-size:14px; font-family:'Arial'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="c" <?if($val=="c")echo" checked";?> />
						Arial Bold
					</label><br/>
					<label style="font-size:14px; font-family:'Arial'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="d" <?if($val=="d")echo" checked";?> />
						Arial Bold Italic
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="e" <?if($val=="e")echo" checked";?> />
						Georgia Regular
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="f" <?if($val=="f")echo" checked";?> />
						Georgia Italic
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="g" <?if($val=="g")echo" checked";?> />
						Georgia Bold
					</label><br/>
					<label style="font-size:14px; font-family:'Georgia'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="h" <?if($val=="h")echo" checked";?> />
						Georgia Bold Italic
					</label><br/>
					<label style="font-size:14px; font-family:'Tahoma';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="i" <?if($val=="i")echo" checked";?> />
						Tahoma Regular
					</label><br/>
					<label style="font-size:14px; font-family:'Tahoma'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="j" <?if($val=="j")echo" checked";?> />
						Tahoma Bold
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="k" <?if($val=="k")echo" checked";?> />
						Times Regular
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman'; font-style:italic;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="l" <?if($val=="l")echo" checked";?> />
						Times Italic
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="m" <?if($val=="m")echo" checked";?> />
						Times Bold
					</label><br/>
					<label style="font-size:14px; font-family:'Times New Roman'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="n" <?if($val=="n")echo" checked";?> />
						Times Bold Italic
					</label><br/>
					<label style="font-size:14px; font-family:'Lucida';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="o" <?if($val=="o")echo" checked";?> />
						Lucida Regular
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana';">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="p" <?if($val=="p")echo" checked";?> />
						Verdana Regular
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana'; font-style:italic;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="q" <?if($val=="q")echo" checked";?> />
						Verdana Italic
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana'; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="r" <?if($val=="r")echo" checked";?> />
						Verdana Bold
					</label><br/>
					<label style="font-size:14px; font-family:'Verdana'; font-style:italic; font-weight:bold;">
						<input type="radio" id="<?echo htmlspecialchars($arOption[0])?>" name="<?echo htmlspecialchars($arOption[0])?>" value="s" <?if($val=="s")echo" checked";?> />
						Verdana Bold Italic
					</label><br/>
				<?endif?>
			</td>
		</tr>
		<?endforeach?>
	<?$tabControl->Buttons();?>
		<input type="submit" name="Update" value="<?=GetMessage("WEB_RUBLE_SAVE")?>" title="<?=GetMessage("WEB_RUBLE_SAVE")?>">
		<input type="submit" name="Apply" value="<?=GetMessage("WEB_RUBLE_APPLY")?>" title="<?=GetMessage("WEB_RUBLE_APPLY")?>">
		<?if(strlen($_REQUEST["back_url_settings"])>0):?>
			<input type="button" name="Cancel" value="<?=GetMessage("WEB_RUBLE_CANCEL")?>" title="<?=GetMessage("WEB_RUBLE_CANCEL")?>" onclick="window.location='<?echo htmlspecialchars(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
			<input type="hidden" name="back_url_settings" value="<?=htmlspecialchars($_REQUEST["back_url_settings"])?>">
		<?endif?>
		<input type="submit" name="RestoreDefaults" title="<?echo GetMessage("WEB_RUBLE_RESTORE_DEFAULTS")?>" OnClick="return confirm('<?echo AddSlashes(GetMessage("WEB_RUBLE_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("WEB_RUBLE_RESTORE_DEFAULTS")?>">
		<?=bitrix_sessid_post();?>
	<?$tabControl->End();?>
</form>