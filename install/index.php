<?
/**
 * Copyright (c) 21/8/2019 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

global $MESS;
$strPath2Lang = str_replace("\\", "/", __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang)-18);
include(GetLangFileName($strPath2Lang."/lang/", "/install/index.php"));

Class web_ruble extends CModule {
	var $MODULE_ID = "web.ruble";
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;
	var $MODULE_CSS;

	function web_ruble() {
		$arModuleVersion = array();
		$path = str_replace("\\", "/", __FILE__);
		$path = substr($path, 0, strlen($path) - strlen("/index.php"));
		include($path."/version.php");
		if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
			$this->MODULE_VERSION = $arModuleVersion["VERSION"];
			$this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
		} else {
			$this->MODULE_VERSION = WEB_RUBLE_VERSION;
			$this->MODULE_VERSION_DATE = WEB_RUBLE_DATE;
		}
		$this->PARTNER_NAME = GetMessage("WEB_RUBLE_PARTNER_NAME");
		$this->PARTNER_URI = GetMessage("WEB_RUBLE_PARTNER_URI");
		$this->MODULE_NAME = GetMessage("WEB_RUBLE_MODULE_NAME");
		$this->MODULE_DESCRIPTION = GetMessage("WEB_RUBLE_MODULE_DESC");
	}

	function DoInstall() {
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/web.ruble/install/themes/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes", true, true);
		RegisterModule("web.ruble");
		RegisterModuleDependences("main", "OnProlog","web.ruble","CWebRuble", "OnProlog");
		RegisterModuleDependences("currency", "CurrencyFormat","web.ruble","CWebRuble", "CurrencyFormat");
		RegisterModuleDependences('main','OnEndBufferContent','web.ruble',"CWebRuble", "OnEndBufferContent");
		$web_ruble_regex_exclude = "";
		$web_ruble_regex_exclude .= "#^/bitrix/admin/sale_print.php#";
		$web_ruble_regex_exclude .= "\n#^/personal/order/payment/#";
		$web_ruble_regex_exclude .= "\n#^/bitrix/admin/#";
		COption::SetOptionString("web.ruble", "web_ruble_regex_exclude", $web_ruble_regex_exclude);
		$web_ruble_regex_include .= "";
		$web_ruble_regex_include .= "#^/bitrix/components/.*?/ajax.php($|\?.*)$#";
		COption::SetOptionString("web.ruble", "web_ruble_regex_include", $web_ruble_regex_include);
		COption::SetOptionString("web.ruble", "web_ruble_additional_code", "if ($"."_GET[\"rub\"]==\"N\") return false;");
	}

	function DoUninstall() {
		UnRegisterModuleDependences("main", "OnProlog","web.ruble","CWebRuble", "OnProlog");
		UnRegisterModuleDependences("currency", "CurrencyFormat","web.ruble","CWebRuble", "CurrencyFormat");
		UnRegisterModuleDependences('main','OnEndBufferContent','web.ruble',"CWebRuble", "OnEndBufferContent");
		UnRegisterModule("web.ruble");
		DeleteDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/web.ruble/install/themes/.default/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/themes/.default");
		DeleteDirFilesEx("/bitrix/themes/.default/web.ruble.font/");
		COption::RemoveOption("web.ruble");
	}
}
?>