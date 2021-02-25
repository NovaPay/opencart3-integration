# Керівництво по установці платіжного шлюзу Novapay в OpenCart 3

## Завантаження по FTP

1. Завантажте файли (приклад з [Filezilla FTP client](https://filezilla-project.org/)) на Ваш хостинг (сервер / хмара):
    - Розпакуйте ZIP архів і увійдіть в распакованую директорію  
    ![Upload files with FTP](images/en/21-FTP-Unpack.png)
    - Завантажте файли на сервер в кореневу директорію інтернет-магазину `htdocs/`  
    ![Check files on FTP](images/en/22-FTP-Upload.png)
2. Ви можете вибрати будь який інший спосіб загрузки файлів `ssh`, `hosting panel file manager`, та інші.

## Налаштування в панелі адміністрування

### Модуль доставки
1. Перейдіть до Розширення.
    - Натисніть `Extensions` в боковій навігації  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Виберіть `Extension type` > `Shipping` на відкритій сторінці
    ![Stores](images/en/41-Admin-Extensions-Delivery.png)
    - Прокрутіть униз до способу доставки `Novapay`
    ![Sales](images/en/42-Admin-Novapay-Delivery-settings.png)
1. Налаштування розширення
    ![Configure](images/en/43-Admin-Novapay-Delivery-configure.png)
    - Статус `Enabled` або` Disabled`, включає або вимикає розширення
    - `Sort Order` - це номер для сортування способів доставки на сторінці оформлення замовлення від мінімального до максимального (за зростанням)
    > Розміри та одиниці ваги використовуються із стандартних налаштувань магазину.
1. Після налаштування способу доставки ви можете стежити за замовленнями з обраними методами доставки, щоб перевірити номер відстеження, роздрукувати його та відстежити доставку пакета.
    - Перейдіть до замовленнь `Sales` > `Orders`
    ![Orders](images/en/44-Admin-Orders.png)
    - Перегляньте (клацніть) одне замовлення з доставкою Novapay
    ![View order](images/en/45-Admin-View-Order.png)
    - Перевірте інформацію про доставку в блоці Деталі замовлення
    ![Check info](images/en/46-Admin-Check-Delivery.png)
    - Прокрутіть вниз до `Order History` до та натисніть на вкладку` Novopay`
    ![Tab Novapay](images/en/47-Admin-Tab-Novapay.png)
    - Натисніть кнопку `Confirm Hold`, коли будете готові доставити замовлення
    ![Confirm hold](images/en/48-Admin-Confirm-Hold.png)
    - Коли підтвердження буде підтверджено (можливо треба буде натиснути `Check status`), ви побачите кнопку `Print PDF`.
    ![Print PDF](images/en/49-Admin-Print-PDF.png)
    - Щоб роздрукувати транспортний документ, натисніть кнопку `Print PDF`.
    ![PDF](images/en/50-Admin-PDF.png)

### Модуль оплати

1. Увійдіть в панель адміністратора на своєму інтернет магазині, зазвичай URL-адреса `https://your-webshop-domain.com/admin/`, але вона може бути змінений в конфігурації.  
![Admin Log in](images/en/01-Admin-Login.png)
1. Перейдіть в Розширення.
    - Натисніть `Extensions` на бічній панелі навігації  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Виберіть `Extension type` у відкритій сторінці
    ![Stores](images/en/03-Admin-Extensions-Payments.png)
    - Прокрутіть вниз до платіжного розширення `Novapay`
    ![Sales](images/en/06-Admin-Novapay-settings.png)
1. Налаштуйте розширення
    - API info
    ![Enable](images/en/07-Admin-Novapay-API.png)
        - Увімкніть платіжний модуль Novapay. Змініть статус на **Enabled**.  
        - `Test mode` — LIVE (робочий) або TEST (тестовий) режим;
        - `Merchant ID` — ідентифікатор продавця, що надається Novapay;
        - `Public key` — публічний ключ для запиту postback API;
        - `Private key` — приватний ключ для запитів API;
        - `Password private key` — пароль до приватного ключа, використовується тільки в LIVE режимі;
        - `Success Url` — url успішної сторінки після обробки платежу;
        - `Fail Url` — url сторінки з помилкою після обробки платежу;
        - `Payment type` — тип платежу DIRECT (прямий) або HOLD (утримання);
    - Статуси оплат
    ![Enable](images/en/08-Admin-Novapay-Status.png)
        - `Payment Action Created` — встановити стан замовлення при створенні платежу;
        - `Payment Action Expired` — встановити стан замовлення після закінчення терміну платежу;
        - `Payment Action Processing` — встановити стан замовлення при обробці платежу;
        - `Payment Action Holded` — встановити стан замовлення при утриманні платежу;
        - `Payment Action Hold confirmed` — встановити стан замовлення при підтвердженні утримання платежу;
        - `Payment Action Hold completion` — встановити стан замовлення при обробці завершення утримання платежу;
        - `Payment Action Paid` — встановити стан замовлення при успішну оплату;
        - `Payment Action Failed` — встановити стан замовлення при невдалій оплаті;
        - `Payment Action Processing void` — встановити стан замовлення при анулюванні платежу;
        - `Payment Action Voided` — встановити стан замовлення при анулюванні платежу;

## Тестування на стороні інтернет магазину

### Модуль доставки
1. Зайдіть у свій магазин і додайте товар у кошик для покупок. Перейдіть до каси, заповніть необхідну інформацію та перейдіть до кроку `Delivery Method`.
1. Виберіть `Novapay Shipping` і введіть назву міста.
![Delivery method](images/en/49-Front-Delivery-Method.png)
1. Коли введено місто (або кілька перших літер), виберіть потрібне місто зі спадного меню автозаповнення.
![Select a city](images/en/50-Front-Select-City.png)
1. Перейдіть до наступного вводу `Department` та напишіть номер потрібного Вам відділення. Потім виберіть його зі спадного меню автозаповнення.
![Select a department](images/en/51-Front-Select-Department.png)
1. Вартість перевезення повинна бути перерахована та оновлена.
![Select a department](images/en/52-Front-Shipping-Cost.png)
1. На кроці `Payment Method` ви можете вибрати лише `Novapay Payment`.
![Select a department](images/en/53-Front-Select-Payment.png)
1. Після подання форми оформлення замовлення та переадресації на сторінку обробки платежів Novopay. Введіть дані картки для оплати. Ви можете побачити вартість доставки на цій сторінці.
![Payment processing](images/en/54-Front-Payment-Processing.png)

### Модуль оплати
1. Зайдіть в свій магазин і додайте товар в корзину. Перейдіть на сторінку оформлення замовлення і заповніть `Delivery method` крок і перейти до `Payment method`.  
![Payment method](images/en/31-Front-Reviews-and-Payments.png)  
Ви повинні побачити перемикач **Novapay payment**.
1. Є обмеження для **Country**, **Telephone** та **Currency**. 
1. Якщо вся інформація вірна, Ви будете перенаправлені на обробку платежів.
![Payment processing](images/en/32-Front-Limitations.png)