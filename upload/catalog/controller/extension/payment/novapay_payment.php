<?php

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
            'extension/payment/novapay_payment/postBack', [], true
        );

        $queryArgs = [
            'language' => $this->config->get('config_language'),
//            'orderId' => $orderId,
        ];

        $success_url = $this->config->get('payment_novapay_payment_success_url') !== ""
            ? $this->config->get('payment_novapay_payment_success_url') . '?' . http_build_query($queryArgs)
            : $this->url->link('extension/payment/novapay_payment/success', $queryArgs, true);

        $fail_url = $this->config->get('payment_novapay_payment_fail_url')  !== ""
            ? $this->config->get('payment_novapay_payment_fail_url') . '?' . http_build_query($queryArgs)
            : $this->url->link('extension/payment/novapay_payment/fail', $queryArgs, true);

        $callback = new Novapay\Payment\SDK\Schema\Callback($postback_url, $success_url, $fail_url);

        $order = $this->model_checkout_order->getOrder($orderId);

        $ukrNumberStr = isset($order['telephone']) ? $order['telephone'] : null;
        $ukrNumberStr = preg_replace('/[^0-9]/', '', $ukrNumberStr);

        $validPhoneNumber = false;

        if (strpos($ukrNumberStr, '380') !== 0 && strlen($ukrNumberStr) === 9) {
            $validPhoneNumber = true;
            $ukrNumberStr = "380" . $ukrNumberStr;
        }
        if (!$validPhoneNumber && strpos($ukrNumberStr, '38') !== 0 && strlen($ukrNumberStr) === 10) {
            $validPhoneNumber = true;
            $ukrNumberStr = "38" . $ukrNumberStr;
        }
        if (!$validPhoneNumber && strpos($ukrNumberStr, '3') !== 0 && strlen($ukrNumberStr) === 11) {
            $validPhoneNumber = true;
            $ukrNumberStr = "3" . $ukrNumberStr;
        }
        if(!$validPhoneNumber && strlen($ukrNumberStr) === 12 && strpos($ukrNumberStr, '380') === 0) {
            $validPhoneNumber = true;
        }

        if(!$validPhoneNumber) {
            $this->session->data['error'] = 'NovaPay: not valid phone number. Phone number format is +380XXXXXXXXX';
            header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=phone');
        }
        $ukrNumberStr = "+" . $ukrNumberStr;

        $session = new Novapay\Payment\SDK\Model\Session();

        $success_redirect_timeout = null;
        if (!empty($this->config->get('success_redirect_timeout'))) {
            $success_redirect_timeout = $this->config->get('success_redirect_timeout');
        }

        $version = explode('.', VERSION);
        $version = isset($version[0]) ? $version[0] : 'undefined';

        $ok = $session->create(
            Novapay\Payment\SDK\Model\Model::getMerchantId(),
            new Novapay\Payment\SDK\Schema\Client(
                $order['payment_firstname'],
                $order['payment_lastname'],
                '', //patronymic
                $ukrNumberStr,
                $order['email']
            ),
            new Novapay\Payment\SDK\Schema\Metadata([
                'order_id' => $orderId,
                'source_name' => 'OpenCart',
                'version' => $version,
                'build' => VERSION
            ]),
            $callback,
            $success_redirect_timeout
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
            header("Location: " . $_SERVER["HTTP_REFERER"] /*. '&error=api-error'*/);
            return;
        }

        $this->load->model('account/order');
        $products = $this->model_account_order->getOrderProducts($orderId);

        $items = [];
        foreach ($products as $item) {
            $items[] = new Novapay\Payment\SDK\Schema\Product(
                $item['name'], $item['price'], $item['total'] / $item['price']);
        }

        if (isset($order['shipping_method']) && $order['shipping_method'] !== "" && isset($order['shipping_method']['cost']) && $order['shipping_method']['cost'] > 0.1 ) {
            $items[] = new \Novapay\Payment\SDK\Schema\Product(
                isset($order['shipping_method']['name']) ? $order['shipping_method']['name'] : 'Delivery',
                isset($order['shipping_method']['cost']) ? $order['shipping_method']['cost'] :  0.0, 1);
        }

        $isHold = 1 === (int)$this->config->get('payment_novapay_payment_is_hold');

        $this->db->query("INSERT INTO "
            . DB_PREFIX . "novapay_payment SET order_id = '" . $orderId
            . "', session_id = '" . $session->id
            . "', is_hold = '" . $isHold . "'");

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

            $this->cart->clear();

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
            $this->response->redirect($this->url->link('error/not_found'), 404);
            return ;
        }

        Novapay\Payment\SDK\Model\Model::setPrivateKey($this->config->get('payment_novapay_payment_private_key'));
        Novapay\Payment\SDK\Model\Model::setPublicKey($this->config->get('payment_novapay_payment_public_key'));
        Novapay\Payment\SDK\Model\Model::setMerchantId($this->config->get('payment_novapay_payment_merchant_id'));

        $postback = new Novapay\Payment\SDK\Model\Postback(
            file_get_contents('php://input'), // data
            apache_request_headers(),         // headers
            isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
            isset($_SERVER['REDIRECT_STATUS']) ? $_SERVER['REDIRECT_STATUS'] : 200,
            isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1'
        );

        $postbackPostRequest = $postback->getRequest();

        $this->customLog(json_encode($postbackPostRequest->jsonSerialize()));

        $this->load->model('checkout/order');

        if (!$postback->verify(OPENSSL_ALGO_SHA1)) {
            $this->customLog('Sign verification failed.');
            $this->response->redirect($this->url->link('error/not_found'), 404);
        }

        $sessionId = $postbackPostRequest->id;
        $orderId = $postbackPostRequest->metadata->order_id;

        $orderNovaPay = $this->db->query("SELECT * FROM " . DB_PREFIX . "novapay_payment WHERE order_id = '" . $orderId . "' AND session_id = '" . $sessionId . "' LIMIT 1");
        if ($orderNovaPay->num_rows === 0) {
            $this->response->redirect($this->url->link('error/not_found'), 404);
            return ;
        }
        $status = $postback->getSession()->status;
        if ($status === 'processing_hold_completion') {
            $status = 'holded';
        }

        $this->load->model('checkout/order');
        $this->model_checkout_order
            ->addOrderHistory(
                $orderId,
                $this->config->get('payment_novapay_payment_Payment_' . ucfirst(strtolower($status)) . '_status_id'),
                'NovaPay: PostBack: ' . $postback->getSession()->status
            );
    }

    private function customLog($model)
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

        if (get_class($model) != 'Novapay\Payment\SDK\Model\Model') {
            return;
        }

        $className = get_class($model);

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

    public function fail()
    {
        $orderId = /*$this->request->get['orderId'] ??*/ $this->session->data['order_id'];
        if ($orderId) {
            $this->load->model('checkout/order');
            $this->model_checkout_order
                ->addOrderHistory(
                    $orderId,
                    $this->config->get('payment_novapay_payment_Payment_Failed_status_id'),
                    'NovaPay: Payment failed'
                );
        }

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('extension/payment/novapay_fail', $data));
    }

}