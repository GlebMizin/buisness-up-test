<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

if (!Loader::includeModule('main')) {
    ShowError('Модуль main не подключен');
    return;
}

$userId = intval($arParams["USER_ID"]);

if ($userId <= 0) {
    ShowError("Не указан ID пользователя");
    return;
}

$cache = Bitrix\Main\Data\Cache::createInstance();
$cacheId = 'user_detail_' . $userId;
$cacheDir = '/gleb_comp/user_detail/';
$cacheTime = intval($arParams["CACHE_TIME"]);

if ($arParams["CACHE_TYPE"] != "N" && $cache->initCache($cacheTime, $cacheId, $cacheDir)) {
    $vars = $cache->getVars();
    $this->arResult = $vars["RESULT"];
} elseif ($arParams["CACHE_TYPE"] == "N" || $cache->startDataCache()) {

    $userQuery = UserTable::getList([
        'select' => ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHONE'],
        'filter' => ['ID' => $userId, 'ACTIVE' => 'Y'],
    ]);

    $user = $userQuery->fetch();

    if (!$user) {
        $cache->abortDataCache();
        ShowError("Пользователь не найден");
        return;
    }

    $this->arResult = [
        'USER' => $user,
    ];

    if ($arParams["CACHE_TYPE"] != "N") {
        $cache->endDataCache(array("RESULT" => $this->arResult));
    }
}

$this->includeComponentTemplate();