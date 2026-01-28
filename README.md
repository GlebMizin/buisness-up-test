# Тестовое задание PHP/Bitrix

Выполнение тестового задания для позиции PHP/Bitrix разработчика.

## Задание 1: Форма с капчей и отправкой

**Реализовано:**
- HTML форма (имя, email, телефон, файл резюме)
- Google reCAPTCHA v2
- Отправка на email через PHPMailer (SMTP)
- Отправка в Telegram Bot с файлом
- Валидация MIME-типов файлов
- Защита от XSS (htmlspecialchars)

**Файлы:**
- `/task1/index.php` - форма
- `/local/tools/submit.php` - обработчик

## Задание 2: API бюджета

**Реализовано:**
- D7 ORM класс `BudgetTransactionTable`
- 4 GET endpoint'а: пополнить, снять, баланс, список транзакций
- Сохранение в БД с историей транзакций
- Проверка достаточности средств

**Файлы:**
- `/task2/budget_api.php` - API
- `/local/php_interface/include/budget_functions.php` - функции

**Примеры запросов:**
```
GET /task2/budget_api.php?action=deposit&amount=1000&description=Пополнение
GET /task2/budget_api.php?action=withdraw&amount=500&description=Снятие
GET /task2/budget_api.php?action=balance
GET /task2/budget_api.php?action=transactions
```

## Задание 3: Компоненты пользователей

**Реализовано:**
- Комплексный компонент `gleb:users` (список + детальная)
- Простой компонент `gleb:user.detail` (информация о пользователе)
- SEF режим с ЧПУ (/task3/ и /task3/ID/)
- Кэширование данных
- D7 ORM (UserTable, GroupTable)
- Параметры: заголовок страницы, время кэширования

**Файлы:**
- `/local/components/gleb/users/` - комплексный компонент
- `/local/components/gleb/user.detail/` - простой компонент
- `/task3/index.php` - вызов компонента
- `urlrewrite.php` - правила ЧПУ

**Вывод:**
- Таблица со списком: ID, дата регистрации, email, ФИО, группы
- Детальная страница: ID, ФИО, Email, Телефон

## Установка

1. Скопировать файлы в корень сайта
2. Настроить API ключи и доступы в `/bitrix/.settings.php`
пример `'api_keys' =>
   array (
   'recaptcha_secret_key' => 'ключ капчи',
   'telegram_bot_token' => 'токен бота',
   'telegram_chat_id' => 'айди чата ТГ',
   'smtp_host' => 'сервер смтп',
   'smtp_port' => порт (число),
   'smtp_user' => 'email отправителя и получателя (одно лицо в контексте задания)',
   'smtp_password' => 'пароль смтп',
   ),`
3. Создать таблицу БД для задания 2:
```sql
CREATE TABLE b_budget_transactions (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    AMOUNT DECIMAL(10,2),
    TYPE VARCHAR(10),
    DATE DATETIME,
    DESCRIPTION TEXT,
    BALANCE_AFTER DECIMAL(10,2)
);
```