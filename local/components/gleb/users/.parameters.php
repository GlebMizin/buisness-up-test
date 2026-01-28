<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "PAGE_TITLE" => array(
            "PARENT" => "BASE",
            "NAME" => "Заголовок страницы",
            "TYPE" => "STRING",
            "DEFAULT" => "Список пользователей",
        ),
        "SEF_MODE" => array(
            "list" => array(
                "NAME" => "Список пользователей",
                "DEFAULT" => "index.php",
                "VARIABLES" => array(),
            ),
            "detail" => array(
                "NAME" => "Детальная страница",
                "DEFAULT" => "#ID#/",
                "VARIABLES" => array("ID"),
            ),
        ),
        "CACHE_TYPE" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => "Тип кеширования",
            "TYPE" => "LIST",
            "DEFAULT" => "A",
            "VALUES" => array(
                "A" => "Авто + Управляемое",
                "Y" => "Кешировать",
                "N" => "Не кешировать"
            ),
        ),
        "CACHE_TIME" => array(
            "PARENT" => "CACHE_SETTINGS",
            "NAME" => "Время кэширования (сек.)",
            "TYPE" => "STRING",
            "DEFAULT" => "3600",
        ),
    ),
);