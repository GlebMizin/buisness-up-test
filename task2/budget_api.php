<?php
// Подключение Битрикс
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
require($_SERVER['DOCUMENT_ROOT'].'/local/php_interface/include/budget_functions.php'); // Подключаем функции

use Bitrix\Main\Entity;
use Bitrix\Main\Type;
use Bitrix\Main\Application;
use Bitrix\Main\Context;

// Определение класса ORM
class BudgetTransactionTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_budget_transactions';
    }

    public static function getMap()
    {
        return [
            'ID' => [
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ],
            'AMOUNT' => [
                'data_type' => 'float',
            ],
            'TYPE' => [
                'data_type' => 'string',
                'validation' => function() {
                    return [
                        new Entity\Validator\Length(1, 10)
                    ];
                }
            ],
            'DATE' => [
                'data_type' => 'datetime'
            ],
            'DESCRIPTION' => [
                'data_type' => 'text'
            ],
            'BALANCE_AFTER' => [
                'data_type' => 'float',
            ],
        ];
    }
}

$action = $_GET['action'] ?? '';

if ($action == 'deposit') {
    $amount = floatval($_GET['amount']);
    $description = $_GET['description'] ?? '';

    if ($amount > 0) {
        $newBalance = deposit($amount, $description);
        echo "Пополнено на $amount. Новый баланс: $newBalance";
    } else {
        echo "Сумма пополнения должна быть больше 0.";
    }
}

elseif ($action == 'withdraw') {
    $amount = floatval($_GET['amount']);
    $description = $_GET['description'] ?? '';

    if ($amount > 0) {
        $newBalance = withdraw($amount, $description);
        if (is_numeric($newBalance)) {
            echo "Снято $amount. Новый баланс: $newBalance";
        } else {
            echo $newBalance;
        }
    } else {
        echo "Сумма снятия должна быть больше 0.";
    }
}

elseif ($action == 'transactions') {
    $transactions = getTransactions();
    echo json_encode($transactions, JSON_PRETTY_PRINT);
}

else {
    $balance = getBalance();
    echo "Текущий баланс: " . $balance;
}
?>
