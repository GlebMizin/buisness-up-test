<?php

use Bitrix\Main\Type;
use Bitrix\Main\Entity;

// Функция для получения текущего баланса
function getBalance() {
    $result = BudgetTransactionTable::getList([
        'select' => ['BALANCE_AFTER'],
        'order' => ['DATE' => 'DESC'],
        'limit' => 1,
    ]);

    $row = $result->fetch();

    return $row ? $row['BALANCE_AFTER'] : 0;
}

// Функция для пополнения баланса
function deposit($amount, $description = '') {
    // Получаем текущий баланс
    $balance = getBalance();

    // Новый баланс после пополнения
    $newBalance = $balance + $amount;

    // Добавляем транзакцию в базу данных
    BudgetTransactionTable::add([
        'AMOUNT' => $amount,
        'TYPE' => 'deposit',  // Тип транзакции: пополнение
        'DATE' => new Type\DateTime(),
        'DESCRIPTION' => $description,
        'BALANCE_AFTER' => $newBalance
    ]);

    return $newBalance;
}

// Функция для снятия средств
function withdraw($amount, $description = '') {
    // Получаем текущий баланс
    $balance = getBalance();

    // Проверяем, достаточно ли средств
    if ($balance < $amount) {
        return "Недостаточно средств на балансе.";
    }

    // Новый баланс после снятия
    $newBalance = $balance - $amount;

    // Добавляем транзакцию в базу данных
    BudgetTransactionTable::add([
        'AMOUNT' => $amount,
        'TYPE' => 'withdraw',  // Тип транзакции: снятие
        'DATE' => new Type\DateTime(),
        'DESCRIPTION' => $description,
        'BALANCE_AFTER' => $newBalance
    ]);

    return $newBalance;
}

// Функция для получения списка транзакций
function getTransactions() {
    $result = BudgetTransactionTable::getList([
        'order' => ['DATE' => 'DESC'],
    ]);

    $transactions = [];
    while ($row = $result->fetch()) {
        // Преобразуем дату в строку
        $row['DATE'] = $row['DATE']->toString();  // Преобразование Bitrix DateTime в строку

        // Преобразуем числа в нужный формат
        $row['AMOUNT'] = (float)$row['AMOUNT'];
        $row['BALANCE_AFTER'] = (float)$row['BALANCE_AFTER'];
        $row['ID'] = (int)$row['ID'];

        // Добавляем транзакцию в список
        $transactions[] = $row;
    }

    return $transactions;
}
?>
