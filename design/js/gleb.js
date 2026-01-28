// Маска для телефона +7(999)-999-99-99
document.getElementById('phone').addEventListener('input', function (e) {
    let input = e.target;
    let value = input.value.replace(/\D/g, '');

    if (value.length > 11) {
        value = value.substring(0, 11);
    }

    let formattedValue = '+7';
    if (value.length > 1) formattedValue += '(' + value.substring(1, 4);
    if (value.length > 4) formattedValue += ')-' + value.substring(4, 7);
    if (value.length > 7) formattedValue += '-' + value.substring(7, 9);
    if (value.length > 9) formattedValue += '-' + value.substring(9, 11);

    input.value = formattedValue;
});


document.querySelector('form').addEventListener('submit', function (e) {
    const phoneInput = document.getElementById('phone');
    const nameInput = document.getElementById('name');

    const namePattern = /^[A-Za-zА-Яа-яЁё\s-]+$/;
    if (!namePattern.test(nameInput.value)) {
        alert('Имя может содержать только буквы, пробелы и символ "-".');
        e.preventDefault();
        return;
    }

    const phonePattern = /^\+7\(\d{3}\)-\d{3}-\d{2}-\d{2}$/;
    if (!phonePattern.test(phoneInput.value)) {
        alert('Телефон должен быть в формате +7(999)-999-99-99.');
        e.preventDefault();
    }
});

// Обработчик отправки формы через AJAX
$(document).ready(function() {
    $('form').on('submit', function(e) {
        e.preventDefault();


        var formData = new FormData(this);


        $.ajax({
            url: '/local/tools/submit.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                alert('Форма отправлена успешно!');
                $('form')[0].reset();
                grecaptcha.reset();
            },
            error: function(xhr, status, error) {
                alert('Ошибка при отправке формы: ' + error);
            }
        });
    });
});
