<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentParameters = array(
    "PARAMETERS" => array(
        "USER_ID" => array(
            "PARENT" => "BASE",
            "NAME" => "ID пользователя",
            "TYPE" => "STRING",
            "DEFAULT" => "",
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