<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

if (empty($arResult['USER'])) {
    return;
}

$user = $arResult['USER'];
$fullName = trim($user['NAME'] . ' ' . $user['LAST_NAME']);
?>

<div class="user-detail-info">
    <h2>Информация о пользователе</h2>

    <dl>
        <dt>ID:</dt>
        <dd><?= intval($user['ID']) ?></dd>

        <dt>ФИО:</dt>
        <dd><?=$fullName ?: 'Не указано'?></dd>

        <dt>Email:</dt>
        <dd><?=$user['EMAIL'] ?: 'Не указан'?></dd>

        <dt>Номер телефона:</dt>
        <dd><?=$user['PERSONAL_PHONE'] ?: 'Не указан'?></dd>
    </dl>
</div>