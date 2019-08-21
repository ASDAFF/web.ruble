<?
/**
 * Copyright (c) 21/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
class CWebRuble {
	/*
	*  Event handler for event "CurrencyFormat" (this event is used in function "CurrencyFormat", "currency" module)
	*/
	public static function CurrencyFormat($fSum=0, $strCurrency="RUB") {
		if (!isset($fSum) || strlen($fSum)<=0) return false;
		if (!in_array($strCurrency,array("RUB","RUR"))) return false;
		
		$bExclude = false;
		
		// Skip if defined W_RUBLE_SKIP_FLAG
		if (defined('W_RUBLE_SKIP_FLAG') && W_RUBLE_SKIP_FLAG===true) {
			$bExclude = true;
		}
		
		// Skip POST
		if (!isset($GLOBALS["web.ruble"]["web_ruble_skip_post"])) {
			$GLOBALS["web.ruble"]["web_ruble_skip_post"] = COption::GetOptionString("web.ruble", "web_ruble_skip_post");
		}
		if ($GLOBALS["web.ruble"]["web_ruble_skip_post"]=="Y" && isset($_POST) && !empty($_POST)) {
			$bExclude = true;
		}
		
		// Exclude by REGEX
		if (!isset($GLOBALS["web.ruble"]["web_ruble_regex_exclude"])) {
			$GLOBALS["web.ruble"]["web_ruble_regex_exclude"] = COption::GetOptionString("web.ruble", "web_ruble_regex_exclude");
			$GLOBALS["web.ruble"]["web_ruble_regex_exclude"] = explode("\n", $GLOBALS["web.ruble"]["web_ruble_regex_exclude"]);
		}
		if (!is_array($GLOBALS["web.ruble"]["web_ruble_regex_exclude"])) {
			$GLOBALS["web.ruble"]["web_ruble_regex_exclude"] = array();
		}
		foreach ($GLOBALS["web.ruble"]["web_ruble_regex_exclude"] as $Key => $Row) {
			$Row = trim($Row);
			if ($Row != '' && preg_match($Row, $_SERVER["REQUEST_URI"], $M)) {
				$bExclude = true;
			}
		}
		if ($bExclude) {
			if (!isset($GLOBALS["web.ruble"]["web_ruble_regex_include"])) {
				$GLOBALS["web.ruble"]["web_ruble_regex_include"] = COption::GetOptionString("web.ruble", "web_ruble_regex_include");
				$GLOBALS["web.ruble"]["web_ruble_regex_include"] = explode("\n", $GLOBALS["web.ruble"]["web_ruble_regex_include"]);
			}
			if (!is_array($GLOBALS["web.ruble"]["web_ruble_regex_include"])) {
				$GLOBALS["web.ruble"]["web_ruble_regex_include"] = array();
			}
			foreach ($GLOBALS["web.ruble"]["web_ruble_regex_include"] as $Key => $Row) {
				$Row = trim($Row);
				if ($Row != '' && preg_match($Row, $_SERVER["REQUEST_URI"], $M)) {
					$bExclude = false;
				}
			}
		}
		if ($bExclude) {
			return false;
		}
		
		// Additional code (eval)
		if (!isset($GLOBALS["web.ruble"]["web_ruble_additional_code"])) {
			$GLOBALS["web.ruble"]["web_ruble_additional_code"] = COption::GetOptionString("web.ruble", "web_ruble_additional_code").";";
		}
		if (trim($GLOBALS["web.ruble"]["web_ruble_additional_code"])!="") {
			$Eval = eval($GLOBALS["web.ruble"]["web_ruble_additional_code"]);
			if ($Eval==="" || $Eval===false) return false;
		}
		
		// If in SEO
		$arBacktrace = debug_backtrace(0);
		foreach($arBacktrace as $arFunction) {
			if ($arFunction['function']=='loadFromDatabase' && $arFunction['class']=='Bitrix\Iblock\Template\Entity\ElementPrice') {
				return false;
			}
		}

		$arCurFormat = CCurrencyLang::GetCurrencyFormat($strCurrency);

		if (!isset($arCurFormat["DECIMALS"]))
			$arCurFormat["DECIMALS"] = 2;
		$arCurFormat["DECIMALS"] = IntVal($arCurFormat["DECIMALS"]);
		if ($arCurFormat["HIDE_ZERO"]=='Y' && Round($fSum,$arCurFormat["DECIMALS"])==Round($fSum,0)) {
			$arCurFormat["DECIMALS"] = 0;
		}
		

		if (!isset($arCurFormat["DEC_POINT"]))
			$arCurFormat["DEC_POINT"] = ".";
		if(!empty($arCurFormat["THOUSANDS_VARIANT"]))
		{
			if($arCurFormat["THOUSANDS_VARIANT"] == "N")
				$arCurFormat["THOUSANDS_SEP"] = "";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "D")
				$arCurFormat["THOUSANDS_SEP"] = ".";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "C")
				$arCurFormat["THOUSANDS_SEP"] = ",";
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "S")
				$arCurFormat["THOUSANDS_SEP"] = chr(32);
			elseif($arCurFormat["THOUSANDS_VARIANT"] == "B")
				$arCurFormat["THOUSANDS_SEP"] = chr(32);
		}
		
		// Get letter for selected char
		$num = number_format($fSum, $arCurFormat["DECIMALS"], $arCurFormat["DEC_POINT"], $arCurFormat["THOUSANDS_SEP"]);
		if($arCurFormat["THOUSANDS_VARIANT"] == "B")
			$num = str_replace(" ", "&nbsp;", $num);
		$Price = $num;
		$RubleChar = COption::GetOptionString("web.ruble", "web_ruble_font_char");
		
		$RubleText = self::GetRuble();
		
		$Space = COption::GetOptionString("web.ruble", "web_ruble_add_space")=="Y" ? " " : "";
		if (COption::GetOptionString("web.ruble", "web_ruble_symbol_location")=="R") {
			$Price = $Price.$Space.$RubleText;
		} else {
			$Price = $RubleText.$Space.$Price;
		}
		return $Price;
	}
	
	public static function GetRuble() {
		$RubleText = '';
		$Use20 = COption::GetOptionString("web.ruble", "web_ruble_2_0")=='Y';
		$RubleChar = COption::GetOptionString('web.ruble', 'web_ruble_font_char');
		$Style = '';
		$Title = COption::GetOptionString("web.ruble", "web_ruble_title");
		if ($Title) $Title = ' title=\''.$Title.'\'';
		$OwnTag = COption::GetOptionString("web.ruble", "web_ruble_own_tag");
		if ($OwnTag=='Y') {
			if($Use20){
				$RubleText = '<ruble'.$Style.$Title.'><span class="text">'.GetMessage('W_RUBLE_TITLE').'</span></ruble>';
			} else {
				$RubleText = '<ruble'.$Style.$Title.'>'.$RubleChar.'</ruble>';
			}
		} else {
			if($Use20){
				$RubleText = '<span class=\'w_rub\''.$Style.$Title.'><span class="text">'.GetMessage('W_RUBLE_TITLE').'</span></span>';
			} else {
				$RubleText = '<span class=\'web-ruble-symbol\''.$Style.$Title.'>'.$RubleChar.'</span>';
			}
		}
		return $RubleText;
	}
	
	public static function OnProlog() {
		$RubleChar = COption::GetOptionString("web.ruble", "web_ruble_font_char");
		if(COption::GetOptionString('web.ruble', 'web_ruble_2_0')=='Y') {
			$GLOBALS['APPLICATION']->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/web.ruble/web.ruble.'.$RubleChar.'.css" />', true);
		} else {
			$GLOBALS['APPLICATION']->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/web.ruble.css" />', true);
		}
	}
	
	public static function OnOrderSave($orderId, $arFields, $arOrder, $isNew) {
		define('W_RUBLE_SKIP_FLAG',true);
	}
	
	public static function OnEndBufferContent(&$Content) {
		if(COption::GetOptionString('web.ruble', 'web_ruble_js_currency_replace')=='Y') {
			//
			$From = "{'CURRENCY':'RUB','FORMAT':{'FORMAT_STRING':'.*?','DEC_POINT':'(.*?)','THOUSANDS_SEP':'(.*?)','DECIMALS':(.*?),'THOUSANDS_VARIANT':'(.*?)','HIDE_ZERO':'(.*?)'}}";
			$To = "{'CURRENCY':'RUB','FORMAT':{'FORMAT_STRING':'".str_replace('0','#',CurrencyFormat_Ruble('0','RUB',0,'',''))."','DEC_POINT':'$1','THOUSANDS_SEP':'$2','DECIMALS':$3,'THOUSANDS_VARIANT':'#4','HIDE_ZERO':'$5'}},";
			$Content = preg_replace("/{$From}/i", $To, $Content);
			//
			$From = "BX\.Currency\.setCurrencyFormat\('RUB', {'CURRENCY':'RUB','LID':'(.*?)','FORMAT_STRING':'(.*?)','FULL_NAME':'(.*?)','DEC_POINT':'(.*?)','THOUSANDS_SEP':'(.*?)','DECIMALS':'(.*?)','THOUSANDS_VARIANT':'(.*?)','HIDE_ZERO':'(.*?)','CREATED_BY':'(.*?)','DATE_CREATE':'(.*?)','MODIFIED_BY':'(.*?)','TIMESTAMP_X':'(.*?)'}\)";
			$To = "BX.Currency.setCurrencyFormat('RUB', {'CURRENCY':'RUB','LID':'$1','FORMAT_STRING':'".str_replace('0','#',CurrencyFormat_Ruble('0','RUB',0,'',''))."','FULL_NAME':'$3','DEC_POINT':'$4','THOUSANDS_SEP':'$5','DECIMALS':'$6','THOUSANDS_VARIANT':'$7','HIDE_ZERO':'$8','CREATED_BY':'$9','DATE_CREATE':'$10','MODIFIED_BY':'$11','TIMESTAMP_X':'$12'});";
			$Content = preg_replace("/{$From}/i", $To, $Content);
			//
		}
	}
	
}

function CurrencyFormat_Ruble($Value, $Currency="RUB", $Decimals=0, $DecPoint='.', $ThousandsSep=' ') {
	$Price = number_format($Value, $Decimals, $DecPoint, $ThousandsSep);
	$RubleChar = CWebRuble::GetRuble();
	$Space = COption::GetOptionString("web.ruble", "web_ruble_add_space")=="Y" ? " " : "";
	if (COption::GetOptionString("web.ruble", "web_ruble_symbol_location")=="R") {
		$Price = $Price.$Space.$RubleChar;
	} else {
		$Price = $RubleChar.$Space.$Price;
	}
	return $Price;
}

function Web_RubleSymbol() {
	return CWebRuble::GetRuble();
}

function W_Rub() {
	return CWebRuble::GetRuble();
}
?>