<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Пользователи");
?>

<?$APPLICATION->IncludeComponent(
    "gleb:users",
    ".default",
    array(
        "PAGE_TITLE" => "Список пользователей",
        "SEF_MODE" => "Y",
        "SEF_FOLDER" => "/task3/",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "3600",
    ),
    false
);?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>