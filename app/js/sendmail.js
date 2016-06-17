$(document).ready(function () {

    // Нажатие на кнопку 'Отправить сообщение'
    $('#contactform').submit(function(event){
        event.preventDefault();

        
        // Как делать вывод сообщений - описываем тут (через val() или html())
        $.fn.messageOut = function(msg){
            return $(this).html('<span>' + msg + '</span>');
            // return $(this).val(msg);
        }
        
        // Получаем единожды данные из инпутов, чтоб 100 раз не обращаться к DOM
        var thisForm         = $(this),
            sendButton       = thisForm.find(':submit'),
            messagesOut      = sendButton,
            val_phone        = thisForm.find('.phone').val(),
            default_btn_text = sendButton.val(),
            okClass          = 'ok',
            errClass         = 'err';
        
        // Валидация данных формы
        if((!val_phone) || (val_phone.length < 9)) {
            var extErr = '';
            messagesOut
                .removeClass(okClass)
                .addClass(errClass)
                .messageOut('Все поля обязательны к заполнению' + extErr);
            sendButton
                .prop('disabled', true);
            setTimeout(function(){
                messagesOut
                    .removeClass(okClass)
                    .removeClass(errClass)
                    .messageOut();
                sendButton
                    .messageOut(default_btn_text)
                    .prop('disabled', false);
            }, 3000);
            return false;
        }
        
        // Делаем кнопки отправки недоступной (чтоб по ней не клацали over9000 раз)
        sendButton.prop('disabled', true);
        // Формат ответа: {status: -1, msg: "Текст сообщения"} 
        $.getJSON(thisForm.attr('action'), {'phone': val_phone})
            // Если запрос выполнен
            .done(function(answerJSON) {
                // console.log(answerJSON);
                // Если скрипт вернул корректный ответ
                if(answerJSON.status === 1){ // Опа-опа - ключевой ответ, по которому 
                                            // определяем - корректно ли отработал скриптяра,
                                            // отправил ли он письмо
                    // Делаем недоступными все поля
                    thisForm.find('input,textarea,button').prop('disabled', true);
                    // Показываем сообщение об успешной отправке
                    messagesOut
                        .removeClass(errClass)
                        .addClass(okClass)
                        .messageOut('Успешно отправлено');
                    // И через некоторое время
                    setTimeout(function(){
                        // ...
                        // Код, который выполнится после таймера (показать 'Спасибо за ваш 
                        //   отзыв' или просто скрыть форму)
                        // ...
                        
                        messagesOut.messageOut(default_btn_text);
                        sendButton.prop('disabled', false);
                    }, 10000);
                // Если скрипт вернул не корректный ответ
                } else {
                    // Выводим мессадж
                    messagesOut
                        .removeClass(okClass)
                        .addClass(errClass)
                        .messageOut(answerJSON.msg);
                    // И через некоторое время
                    setTimeout(function(){
                        messagesOut.messageOut(default_btn_text);
                        sendButton.prop('disabled', false);
                    }, 5000);
                }
            })
            // Если при запросе произошла ошибка (например, скрипта нет, или сервер недоступен)
            .fail(function(answerJSON) {
                // console.log(answerJSON);
                messagesOut
                    .removeClass(okClass)
                    .addClass(errClass)
                    .messageOut('Сообщение не отправлено, попробуйте повторить попозже');
                // И через некоторое время
                setTimeout(function(){
                    messagesOut.messageOut(default_btn_text);
                    sendButton.prop('disabled', false);
                }, 15000);   
            });

        return false;
    });

});