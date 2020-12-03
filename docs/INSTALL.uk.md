# Керівництво по установці платіжного шлюзу Novapay в OpenCart 3

## Завантаження по FTP

1. Завантажте файли (приклад з [Filezilla FTP client](https://filezilla-project.org/)) на Ваш хостинг (сервер / хмара):
    - Розпакуйте ZIP архів і увійдіть в распакованую директорію  
    ![Upload files with FTP](images/en/21-FTP-Unpack.png)
    - Завантажте файли на сервер в кореневу директорію інтернет-магазину `htdocs/`  
    ![Check files on FTP](images/en/22-FTP-Upload.png)
2. Ви можете вибрати будь який інший спосіб загрузки файлів `ssh`, `hosting panel file manager`, та інші.

## Налаштування в панелі адміністрування

1. Увійдіть в панель адміністратора на своєму інтернет магазині, зазвичай URL-адреса `https://your-webshop-domain.com/admin/`, але вона може бути змінений в конфігурації.  
![Admin Log in](images/en/01-Admin-Login.png)
2. Перейдіть в Розширення.
    - Натисніть `Extensions` на бічній панелі навігації  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Виберіть `Extension type` у відкритій сторінці
    ![Stores](images/en/03-Admin-Extensions-Payments.png)
    - Прокрутіть вниз до платіжного розширення `Novapay`
    ![Sales](images/en/06-Admin-Novapay-settings.png)
3. Налаштуйте розширення
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
1. Зайдіть в свій магазин і додайте товар в корзину. Перейдіть на сторінку оформлення замовлення і заповніть `Delivery method` крок і перейти до `Payment method`.  
![Payment method](images/en/31-Front-Reviews-and-Payments.png)  
Ви повинні побачити перемикач **Novapay payment**.
2. Є обмеження для **Country**, **Telephone** та **Currency**. 
3. Якщо вся інформація вірна, Ви будете перенаправлені на обробку платежів.
![Payment processing](images/en/32-Front-Limitations.png)