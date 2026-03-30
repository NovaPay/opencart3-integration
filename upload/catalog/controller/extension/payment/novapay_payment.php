<?php

require_once(DIR_SYSTEM . 'library/novapay-payment/vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/novapay-payment/SDK/bootstrap.php');

class ControllerExtensionPaymentNovapayPayment extends Controller {

    public function index() {
        $this->load->language('extension/payment/novapay_payment');

        $this->document->addStyle('catalog/view/theme/default/stylesheet/novapay_payment.css');
        $this->document->addScript('catalog/view/javascript/novapay_payment.js');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_content'] = $this->language->get('text_content');

        $data['action'] = $this->url->link(
            'extension/payment/novapay_payment/createOrder', '', true
        );

        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['token_error'] = $this->language->get('token_error');
        $data['order_id'] = $this->session->data['order_id'];

        return $this->load->view('extension/payment/novapay_payment', $data);
    }

    public function createOrder()
    {
        $this->load->language('extension/payment/novapay_payment');

        $orderId = $this->session->data['order_id'];

        $this->load->model('checkout/order');

        if ($this->config->get('payment_novapay_payment_is_test_mode')) {
            Novapay\Payment\SDK\Model\Model::disableLiveMode();
        } else {
            Novapay\Payment\SDK\Model\Model::enableLiveMode();
        }

        Novapay\Payment\SDK\Model\Model::setPrivateKey($this->config->get('payment_novapay_payment_private_key'));
        Novapay\Payment\SDK\Model\Model::setPublicKey($this->config->get('payment_novapay_payment_public_key'));
        Novapay\Payment\SDK\Model\Model::setMerchantId($this->config->get('payment_novapay_payment_merchant_id'));

        $postback_url = $this->url->link(
            'extension/payment/novapay_payment/postBack', '', true
        );

        $queryArgs = [
            'language' => $this->config->get('config_language'),
        ];

        $success_url = $this->config->get('payment_novapay_payment_success_url') !== ""
            ? $this->config->get('payment_novapay_payment_success_url')
            : $this->url->link('extension/payment/novapay_payment/success', $queryArgs, true);

        $fail_url = $this->config->get('payment_novapay_payment_fail_url')  !== ""
            ? $this->config->get('payment_novapay_payment_fail_url')
            : $this->url->link('extension/payment/novapay_payment/fail', $queryArgs, true);

        $callback = new Novapay\Payment\SDK\Schema\Callback($postback_url, $success_url, $fail_url);

        $order = $this->model_checkout_order->getOrder($orderId);

        $ukrNumberStr = $order['telephone'] ?? null;

        $phoneError = 'NovaPay: not valid phone number. Phone number format is +380XXXXXXXXX';
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $ukrNumberProto = $phoneUtil->parse($ukrNumberStr, 'UA');
        } catch (\libphonenumber\NumberParseException $e) {
            $this->session->data['error'] = $phoneError;
            header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=phone');
            return;
        }

        if (!$phoneUtil->isValidNumber($ukrNumberProto)) {
            $this->session->data['error'] = $phoneError;
            header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=country');
            return;
        }

        $session = new Novapay\Payment\SDK\Model\Session();
        $ok = $session->create(
            Novapay\Payment\SDK\Model\Model::getMerchantId(),
            new Novapay\Payment\SDK\Schema\Client(
                $order['payment_firstname'] ?? null,
                $order['payment_lastname'] ?? null,
                '', //patronymic
                $phoneUtil->format($ukrNumberProto, \libphonenumber\PhoneNumberFormat::E164),
                $order['email'] ?? null,
            ),
            new Novapay\Payment\SDK\Schema\Metadata(['order_id' => $orderId,]),
            $callback
        );

        $this->customLog($session);

        if (!$ok) {
            $res = $session->getResponse();

            $error = $res instanceof Novapay\Payment\SDK\Schema\Response\Error\Error
                ? $res->getMessage()
                : 'Cannot create payment session.';

            $note = sprintf(
                'NovaPay Error: "%s"',
                $error
            );

            $this->session->data['error'] = $note;
            header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=api-error');
            return;
        }

        $this->load->model('account/order');
        $products = $this->model_account_order->getOrderProducts($orderId);

        $items = [];
        foreach ($products as $item) {
            $items[] = new Novapay\Payment\SDK\Schema\Product(
                $item['name'], $item['price'], $item['total'] / $item['price']);
        }

        if (isset($order['shipping_method']) && $order['shipping_method'] !== "" ) {
            $items[] = new \Novapay\Payment\SDK\Schema\Product(
                $order['shipping_method']['name'] ?? 'Delivery',
                $order['shipping_method']['cost'] ?? 0.0, 1);
        }

        $isHold = 1 === (int)$this->config->get('payment_novapay_payment_is_hold');

        $this->db->query("INSERT INTO "
            . DB_PREFIX . "novapay_payment SET order_id = '" . $orderId
            . "', session_id = '" . $session->id
            . "', is_hold = '" . $isHold . "'");


        $this->cart->clear();

        $payment = new Novapay\Payment\SDK\Model\Payment();
        $ok = $payment->create(
            Novapay\Payment\SDK\Model\Model::getMerchantId(),
            $session->id,
            $items,
            round($order['total'], 2),
            $isHold,
            $orderId,
            null
        );

        $this->customLog($payment);

        if ($ok) {

            if ($isHold) {
                $this->model_checkout_order->addOrderHistory(
                    $orderId,
                    $this->config->get('payment_novapay_payment_Payment_Holded_status_id')
                );
            } else {
                $this->model_checkout_order->addOrderHistory(
                    $orderId,
                    $this->config->get('payment_novapay_payment_Payment_Created_status_id')
                );
            }

            return $this->response->redirect($payment->url);
        } else {
            $this->session->data['error'] = $payment->getResponse()->message
                ?: $this->language->get['cancel_payment'];
            header("Location: " . $_SERVER["HTTP_REFERER"]);
            return;
        }
    }

    public function postBack()
    {

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $this->response->redirect($this->url->link('error/not_found', ), 404);
            return ;
        }

        Novapay\Payment\SDK\Model\Model::setPrivateKey($this->config->get('payment_novapay_payment_private_key'));
        Novapay\Payment\SDK\Model\Model::setPublicKey($this->config->get('payment_novapay_payment_public_key'));
        Novapay\Payment\SDK\Model\Model::setMerchantId($this->config->get('payment_novapay_payment_merchant_id'));

        $postback = new Novapay\Payment\SDK\Model\Postback(
            file_get_contents('php://input'), // data
            apache_request_headers(),         // headers
            isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
            $_SERVER['REDIRECT_STATUS'] ?? 200,
            $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1'
        );

        $postbackPostRequest = $postback->getRequest();

        $this->customLog($postbackPostRequest);

        $this->load->model('checkout/order');

        if (!$postback->verify()) {
            $this->response->redirect($this->url->link('error/not_found', ), 404);
        }

        $sessionId = $postbackPostRequest->id;
        $orderId = $postbackPostRequest->metadata->order_id;

        $orderNovaPay = $this->db->query("SELECT * FROM " . DB_PREFIX . "novapay_payment WHERE order_id = '" . $orderId . "' AND session_id = '" . $sessionId . "' LIMIT 1");
        if ($orderNovaPay->num_rows === 0) {
            $this->response->redirect($this->url->link('error/not_found', ), 404);
            return ;
        }

        $this->load->model('checkout/order');
        $this->model_checkout_order
            ->addOrderHistory(
                $orderId,
                $this->config->get('payment_novapay_payment_Payment_' . ucfirst(strtolower($postback->getSession()->status)) . '_status_id',
                ),
                'NovaPay: PostBack: ' . $postback->getSession()->status
            );
    }

    private function customLog(string|Novapay\Payment\SDK\Model\Model $model)
    {
        if (1 !== (int)$this->config->get('payment_novapay_payment_is_log')) {
            return;
        }

        $log = new Log('novapay_payment.log');

        $timestamp = date('Y-m-d H:i:s');

        if (is_string($model)) {
            $log_message = "[{$timestamp}] {$model}\n\n";
            $log->write($log_message);
            return;
        }

        $className = get_class($model);
        $message = json_encode($model::getLog());
        $log_message = "[{$timestamp}] {$className} : {$message}\n";
        $log->write($log_message);
        $message = json_encode($model->getRequest()->jsonSerialize());
        $log_message = "[{$timestamp}] {$className} request : {$message}\n";
        $log->write($log_message);
        $message = json_encode($model->getResponse()->jsonSerialize());
        $log_message = "[{$timestamp}] {$className} response : {$message}\n\n";
        $log->write($log_message);
    }

    public function success()
    {
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('extension/payment/novapay_success', $data));
    }

    public function failed()
    {
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('extension/payment/novapay_failed', $data));
    }

}