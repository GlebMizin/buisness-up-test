<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

if (!Loader::includeModule('main')) {
    ShowError('Модуль main не подключен');
    return;
}

// Дефолтные параметры
$arDefaultUrlTemplates404 = array(
    "list" => "",
    "detail" => "#ID#/",
);

$arDefaultVariableAliases404 = array();
$arDefaultVariableAliases = array();

$arComponentVariables = array("ID");

if ($arParams["SEF_MODE"] == "Y") {
    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
        $arDefaultUrlTemplates404,
        $arParams["SEF_URL_TEMPLATES"]
    );

    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
        $arDefaultVariableAliases404,
        $arParams["VARIABLE_ALIASES"]
    );

    $componentPage = CComponentEngine::ParseComponentPath(
        $arParams["SEF_FOLDER"],
        $arUrlTemplates,
        $arVariables
    );

    if (empty($componentPage) || $componentPage == "index") {
        $componentPage = "list";
    }

    CComponentEngine::InitComponentVariables(
        $componentPage,
        $arComponentVariables,
        $arVariableAliases,
        $arVariables
    );

    $arResult = array(
        "FOLDER" => $arParams["SEF_FOLDER"],
        "URL_TEMPLATES" => $arUrlTemplates,
        "VARIABLES" => $arVariables,
        "ALIASES" => $arVariableAliases
    );
} else {
    $arVariables = array();

    if (isset($_REQUEST["ID"])) {
        $arVariables["ID"] = $_REQUEST["ID"];
        $componentPage = "detail";
    } else {
        $componentPage = "list";
    }

    $arResult = array(
        "VARIABLES" => $arVariables,
    );
}

$this->IncludeComponentTemplate($componentPage);