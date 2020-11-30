<?php

use Novapay\Payment\SDK\Schema\Callback;
use Novapay\Payment\SDK\Schema\Client;
use Novapay\Payment\SDK\Schema\Metadata;
use Novapay\Payment\SDK\Schema\Product;
use Novapay\Payment\SDK\Model\Model;
use Novapay\Payment\SDK\Model\Session;
use Novapay\Payment\SDK\Model\Payment;
use Novapay\Payment\SDK\Logger;
use Novapay\Payment\SDK\Model\Postback;
use Novapay\Payment\SDK\Schema\Delivery;

require_once $_SERVER["DOCUMENT_ROOT"] . '/novapay-sdk/bootstrap.php';


class ControllerExtensionPaymentNovapay extends Controller
{
  const API_VERSION = 2;

    public function updateOrder()
    {
        $this->load->language('extension/payment/novapay');
        $butt = strval($_POST['button_name']);
        $orderId = intval($_POST['order_id']);
        $orders = $this->db->query("SELECT * FROM " . DB_PREFIX . "novapay WHERE order_id = '" . $orderId . "' LIMIT 1");

        $order_statuses = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $orderId . "'");
        $order_status = $order_statuses->row['order_status_id'];

        $merchant_id = $this->config->get('payment_novapay_merchantid');

        $this->load->model('checkout/order');
        $error = '';

        if (intval($this->config->get('payment_novapay_test_mode')) == 1) {
          Model::disableLiveMode();
        } else {
            Model::enableLiveMode();
            Model::setPassword($this->config->get('payment_novapay_passprivate'));
        }
        Model::setPrivateKey($this->config->get('payment_novapay_privatekey'));
        Model::setPublicKey($this->config->get('payment_novapay_publickey'));
        $session = new Session();
        $session->id = $orders->row['session_id'];

        if ($butt == 'cancel') {
            $order_oc = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $orderId . "' LIMIT 1");
            $dt = new DateTime($order_oc->row['date_added']);
            $dt2 = new DateTime();
            $this->model_checkout_order->addOrderHistory($orderId, $this->config->get('payment_novapay_processing_void_status_id'));
            $payment = new Payment();
            $ok = $payment->cancel($merchant_id, $session->id);
            if(!$ok) {
                $error = $payment->getResponse()->message ? $payment->getResponse()->message : $this->language->get['cancel_payment'];
            }
        } else if ($butt == 'update') {
            $ok = $session->status($merchant_id);
            $status = $this->setChose($session->getResponse()->status);
            if($order_status != $status) {
                $this->model_checkout_order->addOrderHistory($orderId, $status);
            }
        } else {
            if($orders->row['payment_type'] == '2') {
                $payment = new Payment();
                $ok = $payment->complete(
                    $merchant_id,
                    $session->id,
                    floatval($_POST['amount'])
                );
                if(!$ok) {
                    $error = $payment->getResponse()->message ? $payment->getResponse()->message : $this->language->get['confirm_hold_failed'];
                } else if($order_status != 5) {
                    $this->model_checkout_order->addOrderHistory($orderId, $this->config->get('payment_novapay_processing_hold_completion_status_id'));
                }
            } else {
                $error = $this->language->get('payment_type_error');
            }
        }
        header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=' . urlencode($error));
    }

    public function setChose($status) {
        $statuses = array(
            'created' => $this->config->get('payment_novapay_created_status_id'),
            'expired' => $this->config->get('payment_novapay_expired_status_id'),
            'processing' => $this->config->get('payment_novapay_processing_status_id'),
            'holded' => $this->config->get('payment_novapay_holded_status_id'),
            'hold_confirmed' => $this->config->get('payment_novapay_hold_confirmed_status_id'),
            'processing_hold_completion' => $this->config->get('payment_novapay_processing_hold_completion_status_id'),
            'paid' => $this->config->get('payment_novapay_paid_status_id'),
            'failed' => $this->config->get('payment_novapay_failed_status_id'),
            'processing_void' => $this->config->get('payment_novapay_processing_void_status_id'),
            'voided' => $this->config->get('payment_novapay_voided_status_id'),
        );
        return $statuses[$status];
    }
    public function setChoseRev($status) {
        $statuses = array(
            $this->config->get('payment_novapay_created_status_id') => 'created',
            $this->config->get('payment_novapay_expired_status_id') => 'expired',
            $this->config->get('payment_novapay_processing_status_id') => 'processing',
            $this->config->get('payment_novapay_holded_status_id') => 'holded',
            $this->config->get('payment_novapay_hold_confirmed_status_id') => 'hold_confirmed',
            $this->config->get('payment_novapay_processing_hold_completion_status_id') => 'processing_hold_completion',
            $this->config->get('payment_novapay_paid_status_id') => 'paid',
            $this->config->get('payment_novapay_failed_status_id') => 'failed',
            $this->config->get('payment_novapay_processing_void_status_id') => 'processing_void',
            $this->config->get('payment_novapay_voided_status_id') => 'voided',
        );
        return $statuses[$status];
    }

  public function index()
  {
    $this->language->load('extension/payment/novapay');

    //$data['action'] = $this->url->link('checkout/success', '', 'SSL');
    $data['action'] = $this->url->link('extension/payment/novapay/createOrder', '', 'SSL');
    $data['token'] = 'test';

    $data['button_confirm'] = $this->language->get('button_confirm');
    $data['token_error'] = $this->language->get('token_error');
    $data['order_id'] = $this->session->data['order_id'];

    return $this->load->view('extension/payment/novapay', $data);
  }

  public function createOrder()
  {
    $this->load->language('extension/payment/novapay');

    ini_set('display_errors', 1);
    $orderId = $this->session->data['order_id'];

    $this->load->model('checkout/order');

    $meta = new Metadata(
      [
        'order_id' => $orderId,
      ]
    );
    $postback = $this->url->link('extension/payment/novapay/postBack', '', 'SSL');
    $success_url = $this->config->get('payment_novapay_successurl') ? $this->config->get('payment_novapay_successurl') : (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTP/1.0') === FALSE ?'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/index.php?route=extension/payment/novapay/success';
    $fail_url = $this->config->get('payment_novapay_failurl') ? $this->config->get('payment_novapay_failurl') : (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTP/1.0') === FALSE ?'https://' : 'http://') . $_SERVER['SERVER_NAME'] .  '/index.php?route=extension/payment/novapay/failed';
    $merchant_id = $this->config->get('payment_novapay_merchantid');
    $payment_type = $this->config->get('payment_novapay_payment_type_value');

    $order_info = $this->model_checkout_order->getOrder($orderId);

    if(strpos($order_info['telephone'], '+380') === false) {
        $this->session->data['error'] = $this->language->get('phone_error');
        header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=phone');
        return;
    }
    if($order_info['payment_iso_code_2'] !== 'UA') {
      $this->session->data['error'] = $this->language->get('country_error');
      header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=country');
      return;
    }

    /*if($order_info['shipping_iso_code_2'] !== 'UA') {
      $this->session->data['error'] = $this->language->get('country_error');
      header("Location: " . $_SERVER["HTTP_REFERER"] . '&error=country');
      return;
    }*/

    $delivery = array();
    $delivery['address'] = array(
      'street' => strlen($order_info['payment_address_1']) > 0 ? $order_info['payment_address_1'] : null,
      'first_name' => strlen($order_info['payment_firstname']) > 0 ? $order_info['payment_firstname'] : null,
      'last_name' => strlen($order_info['payment_lastname']) > 0 ? $order_info['payment_lastname'] : null,
      'country' => strlen($order_info['payment_iso_code_2']) > 0 ? $order_info['payment_iso_code_2'] : null,
      'city' => strlen($order_info['payment_city']) > 0 ? $order_info['payment_city'] : null,
      'phone' => strlen($order_info['telephone']) > 0 ? $order_info['telephone'] : null,
      'email' => strlen($order_info['email']) > 0 ? $order_info['email'] : null,
      'zip' => strlen($order_info['payment_postcode']) > 0 ? $order_info['payment_postcode'] : null,
    );
    $this->load->model('account/order');
    $products = $this->model_account_order->getOrderProducts($orderId);

    // Totals
    $this->load->model('setting/extension');

    $totals = array();
    $taxes = $this->cart->getTaxes();
    $total = 0;

    // Because __call can not keep var references so we put them into an array.
    $total_data = array(
        'totals' => &$totals,
        'taxes'  => &$taxes,
        'total'  => &$total
    );

    $results = $this->model_setting_extension->getExtensions('total');

    foreach ($results as $key => $value) {
        $sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
    }

    array_multisort($sort_order, SORT_ASC, $results);

    foreach ($results as $result) {
        if ($this->config->get('total_' . $result['code'] . '_status')) {
            $this->load->model('extension/total/' . $result['code']);

            // We have to put the totals in an array so that they pass by reference.
            $this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
        }
    }

    $items = [];
    $weight = 0;
    $amount = 0;
    foreach ($products as $item) {
      $items[] = new Product($item['name'], $item['price'], intval($item['total']) / intval($item['price']));
      $amount += $item['total'];
      $weight += 1;
    }

    foreach ($total_data['totals'] as $total) {
        if(floatval($total['value']) > 0 && stripos($total['title'], 'Total') === false) {
            $items[] = new Product($total['title'], floatval($total['value']), 1);
        }
    }


    $delivery = new Delivery(
      $weight * 1.1,
      30 * 20 * 5 / 5000,
      $order_info['payment_city'] . ', ' . $order_info['payment_address_2'],
      $order_info['payment_address_1']
    );

    $client = new Client(
      strlen($order_info['payment_firstname']) > 0 ? $order_info['payment_firstname'] : null,
      strlen($order_info['payment_lastname']) > 0 ? $order_info['payment_lastname'] : null,
      '',
      strlen($order_info['telephone']) > 0 ? $order_info['telephone'] : null,
      strlen($order_info['email']) > 0 ? $order_info['email'] : null
    );

    if (intval($this->config->get('payment_novapay_test_mode')) == 1) {
      Model::disableLiveMode();
    } else {
        Model::enableLiveMode();
        Model::setPassword($this->config->get('payment_novapay_passprivate'));
    }
    Model::setPrivateKey($this->config->get('payment_novapay_privatekey'));
    Model::setPublicKey($this->config->get('payment_novapay_publickey'));

    $callback = new Callback($postback, $success_url, $fail_url);

    $session = new Session();

    $ok = $session->create($merchant_id, $client, $meta, $callback);
    if (!$ok) {
        $this->session->data['error'] = $session->getResponse()->message;
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return;
    }

    $this->model_checkout_order->addOrderHistory($orderId, $this->config->get('payment_novapay_created_status_id'));
    $this->db->query("INSERT INTO " . DB_PREFIX . "novapay SET order_id = '" . $orderId . "', session_id = '" . $session->id . "', payment_type = '". $payment_type ."'");
    $this->cart->clear();

    $payment = new Payment();
    $ok = $payment->create(
      $merchant_id,
      $session->id,
      $items,
      round($order_info['total'], 2),
      $payment_type == 2,
      $session->id
    );

    if ($ok) {
        header("Location: " . $payment->url);
    } else {
        $this->session->data['error'] = $payment->getResponse()->message ? $payment->getResponse()->message : $this->language->get['cancel_payment'];
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return;
    }
  }

  public function postBack()
  {
    if (intval($this->config->get('payment_novapay_test_mode')) == 1) {
        Model::disableLiveMode();
    } else {
        Model::enableLiveMode();
        Model::setPassword($this->config->get('payment_novapay_passprivate'));
    }
    Model::setPrivateKey($this->config->get('payment_novapay_privatekey'));
    Model::setPublicKey($this->config->get('payment_novapay_publickey'));

    $postback = new Postback(
      file_get_contents('php://input'), // data
      apache_request_headers(),         // headers
      isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET',
      isset($_SERVER['REDIRECT_STATUS']) ? $_SERVER['REDIRECT_STATUS'] : 200,
      isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1',
    );

    $postbackPostRequest = $postback->getRequest();


  /*$content = json_encode($postbackPostRequest);
  $fp = fopen($_SERVER['DOCUMENT_ROOT'] . "/amyfile.txt","wb");
  fwrite($fp,$content);
  fclose($fp);*/

    if (!$postback->verify()) {
      $this->load->model('checkout/order');
      $this->model_checkout_order->addOrderHistory($postbackPostRequest->metadata->order_id, $this->config->get('payment_novapay_failed_status_id'));
      return false;
    }

    $orderId = $postbackPostRequest->metadata->order_id;

    $order_statuses = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $orderId . "'");
    $order_status = $order_statuses->row['order_status_id'];
    $status = $this->setChoseRev($order_status);

    if($postbackPostRequest->status != $status) {
        $this->load->model('checkout/order');
        $this->model_checkout_order->addOrderHistory($orderId, $this->setChose($postbackPostRequest->status));
    }

  }

  private function _language($lang_id)
  {
    $lang = substr($lang_id, 0, 2);
    $lang = strtolower($lang);
    return $lang;
  }

  public function success() {
      $data['column_left'] = $this->load->controller('common/column_left');
      $data['column_right'] = $this->load->controller('common/column_right');
      $data['content_top'] = $this->load->controller('common/content_top');
      $data['content_bottom'] = $this->load->controller('common/content_bottom');
      $data['footer'] = $this->load->controller('common/footer');
      $data['header'] = $this->load->controller('common/header');
      $this->response->setOutput($this->load->view('extension/payment/novapay_success', $data));
  }

    public function failed() {
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');
        $this->response->setOutput($this->load->view('extension/payment/novapay_failed', $data));
    }
}
