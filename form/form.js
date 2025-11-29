// form/form.js

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.drums-form-widget form');
    if (!form) return; // Если форма не найдена на странице, выходим

    const formMessage = document.querySelector('.drums-form-message');
    const submitButton = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        // Показываем загрузку
        formMessage.textContent = 'Отправляем заявку...';
        formMessage.className = 'form-message loading show';

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                return response.text();
            } else {
                throw new Error('Network response was not ok. Status: ' + response.status);
            }
        })
        .then(data => {
            formMessage.className = 'form-message success show';
            formMessage.textContent = data;
            form.reset(); // Очищаем форму
        })
        .catch(error => {
            console.error('Form error:', error);
            formMessage.className = 'form-message error show';
            formMessage.textContent = 'Произошла ошибка: ' + error.message;
        });
    });
});