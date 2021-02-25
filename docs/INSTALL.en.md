# OpenCart 3 Novapay Payment Gateway Installation Manual

## FTP Upload

1. Upload files (example with [Filezilla FTP client](https://filezilla-project.org/)) onto your hosting (server/cloud):
    - Unpack ZIP archive and enter into unziped directory  
    ![Upload files with FTP](images/en/21-FTP-Unpack.png)
    - Upload files to the web shop root directory `htdocs/`  
    ![Check files on FTP](images/en/22-FTP-Upload.png)
1. You can use any other available upload method, such as `ssh`, `hosting panel file manager`, etc.

## Administration setup

1. Log in to Admin panel on your website, usually url is `https://your-webshop-domain.com/admin/`, but it might be changed in the configuration.  
![Admin Log in](images/en/01-Admin-Login.png)

### Novapay delivery extension
1. Go to Extensions.
    - Click on `Extensions` in sidebar navigation  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Select `Extension type` > `Shipping` on opened page
    ![Stores](images/en/41-Admin-Extensions-Delivery.png)
    - Scroll down to `Novapay` delivery method
    ![Sales](images/en/42-Admin-Novapay-Delivery-settings.png)
1. Configure extension
    ![Configure](images/en/43-Admin-Novapay-Delivery-configure.png)
    - Status `Enabled` or `Disabled`, enables or disables the extension
    - Sort Order is number to sort shipping methods on checkout page from minimum to maximum (ascending)
    > Dimensions and weight units are used from standard shop settings.
1. After delivery method is configured you can follow orders with selected delivery methods to check a tracking number, print it, and track the package delivery.
    - Go to `Sales` > `Orders`
    ![Orders](images/en/44-Admin-Orders.png)
    - View (click) one order with the Novapay delivery
    ![View order](images/en/45-Admin-View-Order.png)
    - Check the delivery information in the Order details block
    ![Check info](images/en/46-Admin-Check-Delivery.png)
    - Scroll down to `Order History` to and click on tab `Novapay`
    ![Tab Novapay](images/en/47-Admin-Tab-Novapay.png)
    - Click button `Confirm hold` when you are ready to delivery the Order
    ![Confirm hold](images/en/48-Admin-Confirm-Hold.png)
    - When hold is confirmed (you might click `Check Status`) you can see `Print PDF` button.
    ![Print PDF](images/en/49-Admin-Print-PDF.png)
    - To print transport document click on button `Print PDF`.
    ![PDF](images/en/50-Admin-PDF.png)


### Novapay payment extension

1. Go to Extensions.
    - Click on `Extensions` in sidebar navigation  
    ![Stores](images/en/02-Admin-Menu-Extensions.png)
    - Select `Extension type` on opened page
    ![Stores](images/en/03-Admin-Extensions-Payments.png)
    - Scroll down to `Novapay` payment method
    ![Sales](images/en/06-Admin-Novapay-settings.png)
1. Configure extension
    - API info
    ![Enable](images/en/07-Admin-Novapay-API.png)
        - Enable Novapay payment extension. Change status to **Enabled**.  
        - `Test mode` — LIVE or TEST mode;
        - `Merchant ID` — merchant id provided by Novapay;
        - `Public key` — public key for postback API request;
        - `Private key` — private key for API requests;
        - `Password private key` — password for private key, used only in LIVE mode;
        - `Success Url` — url of the successull page after payment processing;
        - `Fail Url` — url of the failed page after payment processing;
        - `Payment type` — DIRECT or HOLD type;
    - Payment Status
    ![Enable](images/en/08-Admin-Novapay-Status.png)
        - `Payment Action Created` — set the order state when payment created;
        - `Payment Action Expired` — set the order state when payment has expired;
        - `Payment Action Processing` — set the order state when payment is processing;
        - `Payment Action Holded` — set the order state when payment is holded;
        - `Payment Action Hold confirmed` — set the order state when hold payment is confirmed;
        - `Payment Action Hold completion` — set the order state when payment is processing hold;
        - `Payment Action Paid` — set the order state when payment is paid;
        - `Payment Action Failed` — set the order state when payment is failed;
        - `Payment Action Processing void` — set the order state when payment is voiding;
        - `Payment Action Voided` — set the order state when payment is voided;

## Front store test

### Delivery module
1. Go to your front store and add some product in the shopping cart. Go to the checkout fill in the required information and go to step `Delivery Method`.
1. Select `Novapay Shipping` and enter city name.
![Delivery method](images/en/49-Front-Delivery-Method.png)
1. When the city (or few first letters) is entered select the correct city from the autocomplete dropdown.
![Select a city](images/en/50-Front-Select-City.png)
1. Go to the next input `Department` and write number of department you need. Then select it from the autocomplete dropdown.
![Select a department](images/en/51-Front-Select-Department.png)
1. The shipping cost must be recalculated and updated.
![Select a department](images/en/52-Front-Shipping-Cost.png)
1. On the `Payment Method` step you can select only `Novapay payment`.
![Select a department](images/en/53-Front-Select-Payment.png)
1. After submitting a checkout form and redirected to Novapay payment processing page. Enter card details to pay. You can see the shipping cost on this page.
![Payment processing](images/en/54-Front-Payment-Processing.png)


### Payment module
1. Go to your front store and add some product in the shopping cart. Go to the checkout page and complete `Delivery method` step and go to `Payment method`.  
![Payment method](images/en/31-Front-Reviews-and-Payments.png)  
You should see the **Novapay payment** radio button.
1. There are limitations for **Country**, **Telephone** and **Currency**. 
1. If all the information is correct you will be redirected to the payment processing.
![Payment processing](images/en/32-Front-Limitations.png)