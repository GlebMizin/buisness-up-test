<?php
use Bitrix\Main\Entity;
use Bitrix\Main\Type;

class BudgetTransactionTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'b_budget_transactions'; // Название вашей таблицы
    }

    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true
            ),
            'AMOUNT' => array(
                'data_type' => 'float',  // Исправляем тип на 'float'
            ),
            'TYPE' => array(
                'data_type' => 'string',
                'validation' => function() {
                    return array(
                        new Entity\Validator\Length(1, 10)
                    );
                }
            ),
            'DATE' => array(
                'data_type' => 'datetime'
            ),
            'DESCRIPTION' => array(
                'data_type' => 'text'
            ),
            'BALANCE_AFTER' => array(
                'data_type' => 'float',  // Исправляем тип на 'float'
            ),
        );
    }
}
