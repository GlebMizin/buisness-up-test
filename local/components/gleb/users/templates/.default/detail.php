<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

$userId = intval($arResult["VARIABLES"]["ID"]);

if ($userId <= 0) {
    ShowError("Некорректный ID пользователя");
    return;
}

$APPLICATION->SetTitle("Пользователь #" . $userId);

$APPLICATION->IncludeComponent(
    "gleb:user.detail",
    ".default",
    array(
        "USER_ID" => $userId,
        "CACHE_TYPE" => $arParams["CACHE_TYPE"],
        "CACHE_TIME" => $arParams["CACHE_TIME"],
    ),
    $component
);
?>

<br>
<p><a href="<?= $arResult["FOLDER"] ?>">← Вернуться к списку пользователей</a></p>