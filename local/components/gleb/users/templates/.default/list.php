<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;
use Bitrix\Main\GroupTable;

if (!Loader::includeModule('main')) {
    ShowError('Модуль main не подключен');
    return;
}

// Кеширование
$cache = Bitrix\Main\Data\Cache::createInstance();
$cacheId = 'users_list_' . md5(serialize($arParams));
$cacheDir = '/gleb_comp/users/';
$cacheTime = intval($arParams["CACHE_TIME"]);

if ($arParams["CACHE_TYPE"] != "N" && $cache->initCache($cacheTime, $cacheId, $cacheDir)) {
    $vars = $cache->getVars();
    $arUsers = $vars["USERS"];
} elseif ($arParams["CACHE_TYPE"] == "N" || $cache->startDataCache()) {

    $arUsers = [];
    $usersQuery = UserTable::getList([
        'select' => ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'DATE_REGISTER'],
        'order' => ['ID' => 'ASC'],
        'filter' => ['ACTIVE' => 'Y'],
    ]);

    while ($user = $usersQuery->fetch()) {
        $groupIds = CUser::GetUserGroup($user['ID']);

        $groupNames = [];
        if (!empty($groupIds)) {
            $groupsQuery = GroupTable::getList([
                'select' => ['NAME'],
                'filter' => ['ID' => $groupIds]
            ]);

            while ($group = $groupsQuery->fetch()) {
                $groupNames[] = $group['NAME'];
            }
        }

        $user['GROUP_NAMES'] = $groupNames;
        $arUsers[] = $user;
    }

    if ($arParams["CACHE_TYPE"] != "N") {
        $cache->endDataCache(array("USERS" => $arUsers));
    }
}
?>

    <h1><?= htmlspecialchars($arParams['PAGE_TITLE']) ?></h1>

<?php if (!empty($arUsers)): ?>
    <table class="user-list-table" border="1" cellpadding="5" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Дата регистрации</th>
            <th>Email</th>
            <th>ФИО</th>
            <th>Группы пользователя</th>
            <th>Детали</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($arUsers as $user): ?>
            <tr>
                <td><?= intval($user['ID']) ?></td>
                <td><?= $user['DATE_REGISTER']->format('d.m.Y H:i') ?></td>
                <td><?= $user['EMAIL']?></td>
                <td><?=trim($user['NAME'] . ' ' . $user['LAST_NAME'])?></td>
                <td><?=implode(', ', $user['GROUP_NAMES'])?></td>
                <td>
                    <?php if ($arParams["SEF_MODE"] == "Y"): ?>
                        <a href="<?= $arResult["FOLDER"] . str_replace("#ID#", $user['ID'], $arResult["URL_TEMPLATES"]["detail"]) ?>">
                            Подробнее
                        </a>
                    <?php else: ?>
                        <a href="<?= $APPLICATION->GetCurPage() ?>?ID=<?= $user['ID'] ?>">
                            Подробнее
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Пользователи не найдены</p>
<?php endif; ?>