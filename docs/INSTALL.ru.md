# Руководство по установке платежного шлюза Novapay в OpenCart 3

## Загрузка по FTP

1. Загрузите файлы (пример с [FTP-клиентом Filezilla] (https://filezilla-project.org/)) на ваш хостинг (сервер / облако):
    - Распакуйте ZIP архив и войдите в распакованую директорию  
    ![Upload files with FTP](images/en/21-FTP-Unpack.png)
    - Загрузите файлы на сервер в корневую директорию интернет-магазина `htdocs/`  
    ![Check files on FTP](images/en/22-FTP-Upload.png)
1. Вы можете использовать любой другой доступный метод загрузки, например `ssh`, `hosting panel file manager`, и другие.

## Настройка в панели администрирования

### Модуль доставки Novapay

1. Перейдите в Расширения.
    - Нажмите на `Extensions` в боковой навигации  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Выберите `Extension type` > `Shipping` на открывшейся странице
    ![Stores](images/en/41-Admin-Extensions-Delivery.png)
    - Пролистайте вниз до метода оплаты `Novapay`
    ![Sales](images/en/42-Admin-Novapay-Delivery-settings.png)
1. Настройте расширение
    ![Configure](images/en/43-Admin-Novapay-Delivery-configure.png)
    - Статус `Enabled` или `Disabled`, включает и выключает модуль
    - `Sort Order` число отвечающее за сортировку на странице оформления заказа среди таких же методов доставки.
    > Единицы измерения размеров и веса взяты из стандартных настроек магазина.
1. После настройки способа доставки вы можете следить за заказами с выбранными способами доставки, чтобы проверять номер отслеживания, распечатывать его и отслеживать доставку посылки.
    - Перейдите в `Sales` > `Orders`
    ![Orders](images/en/44-Admin-Orders.png)
    - Посмотрите (кликните) один заказ с доставкой Novapay
    ![View order](images/en/45-Admin-View-Order.png)
    - Проверьте информацию о доставке в блоке Детали заказа.
    ![Check info](images/en/46-Admin-Check-Delivery.png)
    - Пролистайте вних до `Order History` и кликните таб `Novapay`
    ![Tab Novapay](images/en/47-Admin-Tab-Novapay.png)
    - Нажмите кнопку `Confirm hold` когда вы будете готовы отправить заказ
    ![Confirm hold](images/en/48-Admin-Confirm-Hold.png)
    - Когда удержание подтверждено (вы можете нажать `Check Status`), вы увидите кнопку `Print PDF`.
    ![Print PDF](images/en/49-Admin-Print-PDF.png)
    - Чтобы распечатать транспортный документ, нажмите кнопку `Print PDF`.
    ![PDF](images/en/50-Admin-PDF.png)


### Модуль оплаты Novapay

1. Войдите в панель администратора на своем интернет магазине, обычно URL-адрес `https://your-webshop-domain.com/admin/`, но он может быть изменен в конфигурации.  
![Административный логин](images/en/01-Admin-Login.png)
1. Перейдите в Расширения.
    - Нажмите `Extensions` на боковой панели навигации  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Выберите `Extension type` в открытой странице  
    ![Stores](images/en/03-Admin-Extensions-Payments.png)
    - Прокрутите вниз до платежного расширения `Novapay`  
    ![Sales](images/en/06-Admin-Novapay-settings.png)
1. Настройте расширение
    - API info
    ![Enable](images/en/07-Admin-Novapay-API.png)
	    - Включите платежный модуль Novapay. Измените статус на **Enabled**.  
        - `Test mode` — LIVE (рабочий) или TEST (тестовый) режим;
        - `Merchant ID` — идентификатор продавца, предоставляемый Novapay;
        - `Public key` — публичный ключ для запроса postback API;
        - `Private key` — закрытый ключ для запросов API;
        - `Password private key` — пароль к закрытому ключу, используется только в LIVE режиме;
        - `Success Url` — url успешной страницы после обработки платежа;
        - `Fail Url` — url страницы с ошибкой после обработки платежа;
        - `Payment type` — тип платежа DIRECT (прямой) или HOLD (УДЕРЖАНИЕ);
	- Статусы оплат
    ![Enable](images/en/08-Admin-Novapay-Status.png)
        - `Payment Action Created` — установить состояние заказа при создании платежа;
        - `Payment Action Expired` — установить состояние заказа по истечении срока платежа;
        - `Payment Action Processing` — установить состояние заказа при обработке платежа;
        - `Payment Action Holded` — установить состояние заказа при удержании платежа;
        - `Payment Action Hold confirmed` — установить состояние заказа при подтверждении удержания платежа;
        - `Payment Action Hold completion` — установить состояние заказа при обработке	завершения удержания платежа;
        - `Payment Action Paid` — установить состояние заказа при успешной оплате;
        - `Payment Action Failed` — установить состояние заказа при неудачной оплате;
        - `Payment Action Processing void` — установить состояние заказа при аннулировании платежа;
        - `Payment Action Voided` — установить состояние заказа при аннулировании платежа;

## Тестирование на стороне интернет магазина

### Модуль доставки
1. Зайдите в свой магазин и добавьте товар в корзину. Зайдите в кассу, заполните необходимую информацию и перейдите к шагу `Delivery Method`.
1. Выберите `Novapay Shipping` и введите название города.
![Delivery method](images/en/49-Front-Delivery-Method.png)
1. После ввода города (или нескольких первых букв) выберите правильный город из раскрывающегося списка автозаполнения.
![Select a city](images/en/50-Front-Select-City.png)
1. Перейдите к следующему входу `Отделение` и укажите номер нужного вам отделения. Затем выберите его в раскрывающемся списке автозаполнения.
![Select a department](images/en/51-Front-Select-Department.png)
1. Стоимость доставки должна пересчитаться и обновиться.
![Select a department](images/en/52-Front-Shipping-Cost.png)
1. На шаге `Payment Method` вы можете выбрать только `Novapay payment`.
![Select a department](images/en/53-Front-Select-Payment.png)
1. После отправки формы оформления заказа и перенаправления на страницу обработки платежей Novopay. Введите данные карты для оплаты. Стоимость доставки вы можете увидеть на этой странице.
![Payment processing](images/en/54-Front-Payment-Processing.png)

### Модуль оплаты
1. Зайдите в свой магазин и добавьте товар в корзину. Перейдите на страницу оформления заказа и заполните `Delivery method` шаг и перейти к `Payment method`.  
![Метод оплаты](images/en/31-Front-Reviews-and-Payments.png)  
Вы должны увидеть переключатель **Novapay оплата**.
1. Есть ограничения для **Country**, **Telephone** и **Currency**. 
1. Если вся информация верна, Вы будете перенаправлены на обработку платежей.
![Процесс оплаты](images/en/32-Front-Limitations.png)