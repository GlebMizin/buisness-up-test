<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
$APPLICATION->SetTitle('ФОС');
?>
    <form action="/local/tools/submit.php" method="post" enctype="multipart/form-data">
        <div class="inputs">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" required pattern="[A-Za-zА-Яа-яЁё\s]+" title="Имя может содержать только буквы и пробелы."><br>

        <label for="email">Почта:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" required maxlength="18" placeholder="+7(999)-999-99-99"><br>

        <label for="file">Прикрепить резюме:</label>
        <input type="file" id="file" required name="resume" accept=".pdf,.doc,.docx,.txt"><br>

        <div class="g-recaptcha" data-sitekey="6LfcuFgsAAAAAIr4UL6eydyIBKwgXuJm03XIjVNu"></div><br>

        <button type="submit">Отправить</button>
        </div>
    </form>

<?
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>