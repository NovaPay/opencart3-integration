<?php

require_once(DIR_SYSTEM . 'library/novapay-payment/SDK/bootstrap.php');

class ControllerExtensionPaymentNovapayPayment extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/payment/novapay_payment');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_novapay_payment', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/payment/novapay_payment', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
        $data['entry_merchant_id_help'] = $this->language->get('entry_merchant_id_help');
        $data['entry_public_key'] = $this->language->get('entry_public_key');
        $data['entry_private_key'] = $this->language->get('entry_private_key');
        $data['entry_success_url'] = $this->language->get('entry_success_url');
        $data['entry_success_url_help'] = $this->language->get('entry_success_url_help');
        $data['entry_fail_url'] = $this->language->get('entry_fail_url');
        $data['entry_fail_url_help'] = $this->language->get('entry_fail_url_help');

        $data['text_test_mode'] = $this->language->get('text_test_mode');
        $data['entry_is_test_mode'] = $this->language->get('is_test_mode');
        $data['entry_is_test_mode_help'] = $this->language->get('entry_is_test_mode_help');
        $data['entry_is_log'] = $this->language->get('is_log');
        $data['entry_is_log_help'] = $this->language->get('entry_is_log_help');
        $data['entry_is_hold'] = $this->language->get('entry_is_hold');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        $data['entry_order_status_Payment_Created'] = $this->language->get('entry_order_status_Payment_Create');
        $data['entry_order_status_Payment_Expired'] = $this->language->get('entry_order_status_Payment_Expired');
        $data['entry_order_status_Payment_Processing'] = $this->language->get('entry_order_status_Payment_Processing');
        $data['entry_order_status_Payment_Holded'] = $this->language->get('entry_order_status_Payment_Holded');
        $data['entry_order_status_Payment_Paid'] = $this->language->get('entry_order_status_Payment_Paid');
        $data['entry_order_status_Payment_Failed'] = $this->language->get('entry_order_status_Payment_Failed');
        $data['entry_order_status_Payment_Voided'] = $this->language->get('entry_order_status_Payment_Voided');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchant_id'])) {
            $data['error_merchant_id'] = $this->error['merchant_id'];
        } else {
            $data['error_merchant_id'] = '';
        }

        if (isset($this->error['public_key'])) {
            $data['error_public_key'] = $this->error['public_key'];
        } else {
            $data['error_public_key'] = '';
        }

        if (isset($this->error['private_key'])) {
            $data['error_private_key'] = $this->error['private_key'];
        } else {
            $data['error_private_key'] = '';
        }

        if (isset($this->error['success_url'])) {
            $data['error_success_url'] = $this->error['success_url'];
        } else {
            $data['error_success_url'] = '';
        }

        if (isset($this->error['fail_url'])) {
            $data['error_fail_url'] = $this->error['fail_url'];
        } else {
            $data['error_fail_url'] = '';
        }

        if (isset($this->error['is_hold'])) {
            $data['error_is_hold'] = $this->error['is_hold'];
        } else {
            $data['error_is_hold'] = '';
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/novapay_payment', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/novapay_payment', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

        if (isset($this->request->post['payment_novapay_payment_status'])) {
            $data['payment_novapay_payment_status'] = $this->request->post['payment_novapay_payment_status'];
        } else {
            $data['payment_novapay_payment_status'] = $this->config->get('payment_novapay_payment_status');
        }

        if (isset($this->request->post['payment_novapay_payment_checkout_sort_order'])) {
            $data['payment_novapay_payment_checkout_sort_order'] = $this->request->post['payment_novapay_payment_checkout_sort_order'];
        } else {
            $data['payment_novapay_payment_checkout_sort_order'] = $this->config->get('payment_novapay_payment_checkout_sort_order');
        }

        if (isset($this->request->post['payment_novapay_payment_merchant_id'])) {
            $data['payment_novapay_payment_merchant_id'] = $this->request->post['payment_novapay_payment_merchant_id'];
        } else {
            $data['payment_novapay_payment_merchant_id'] = $this->config->get('payment_novapay_payment_merchant_id');
        }

        if (isset($this->request->post['payment_novapay_payment_public_key'])) {
            $data['payment_novapay_payment_public_key'] = $this->request->post['payment_novapay_payment_public_key'];
        } else {
            $data['payment_novapay_payment_public_key'] = $this->config->get('payment_novapay_payment_public_key');
        }

        if (isset($this->request->post['payment_novapay_payment_private_key'])) {
            $data['payment_novapay_payment_private_key'] = $this->request->post['payment_novapay_payment_private_key'];
        } else {
            $data['payment_novapay_payment_private_key'] = $this->config->get('payment_novapay_payment_private_key');
        }

        if (isset($this->request->post['payment_novapay_payment_success_url'])) {
            $data['payment_novapay_payment_success_url'] = $this->request->post['payment_novapay_payment_success_url'];
        } else {
            $data['payment_novapay_payment_success_url'] = $this->config->get('payment_novapay_payment_success_url') ?? 'extension/payment/novapay_payment/success';
        }

        if (isset($this->request->post['payment_novapay_payment_fail_url'])) {
            $data['payment_novapay_payment_fail_url'] = $this->request->post['payment_novapay_payment_fail_url'];
        } else {
            $data['payment_novapay_payment_fail_url'] = $this->config->get('payment_novapay_payment_fail_url') ?? 'extension/payment/novapay_payment/failed';
        }

        if (isset($this->request->post['payment_novapay_payment_is_hold'])) {
            $data['payment_novapay_payment_is_hold'] = $this->request->post['payment_novapay_payment_is_hold'];
        } else {
            $data['payment_novapay_payment_is_hold'] = $this->config->get('payment_novapay_payment_is_hold');
        }

        if (isset($this->request->post['payment_novapay_payment_is_test_mode'])) {
            $data['payment_novapay_payment_is_test_mode'] = $this->request->post['payment_novapay_payment_is_test_mode'];
        } else {
            $data['payment_novapay_payment_is_test_mode'] = $this->config->get('payment_novapay_payment_is_test_mode');
        }

        if (isset($this->request->post['payment_novapay_payment_is_log'])) {
            $data['payment_novapay_payment_is_log'] = $this->request->post['payment_novapay_payment_is_log'];
        } else {
            $data['payment_novapay_payment_is_log'] = $this->config->get('payment_novapay_payment_is_log');
        }

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_novapay_payment_Payment_Created_status_id'])) {
            $data['payment_novapay_payment_Payment_Created_status_id'] = $this->request->post['payment_novapay_payment_Payment_Created_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Created_status_id'] = $this->config->get('payment_novapay_payment_Payment_Created_status_id') ?? 1;
        }

        if (isset($this->request->post['payment_novapay_payment_Payment_Expired_status_id'])) {
            $data['payment_novapay_payment_Payment_Expired_status_id'] = $this->request->post['payment_novapay_payment_Payment_Expired_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Expired_status_id'] = $this->config->get('payment_novapay_payment_Payment_Expired_status_id') ?? 7;
        }

        if (isset($this->request->post['payment_novapay_payment_Payment_Processing_status_id'])) {
            $data['payment_novapay_payment_Payment_Processing_status_id'] = $this->request->post['payment_novapay_payment_Payment_Processing_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Processing_status_id'] = $this->config->get('payment_novapay_payment_Payment_Processing_status_id') ?? 2;
        }

        if (isset($this->request->post['payment_novapay_payment_Payment_Holded_status_id'])) {
            $data['payment_novapay_payment_Payment_Holded_status_id'] = $this->request->post['payment_novapay_payment_Payment_Holded_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Holded_status_id'] = $this->config->get('payment_novapay_payment_Payment_Holded_status_id') ?? 2;
        }

        if (isset($this->request->post['payment_novapay_payment_Hold_Confirmed_status_id'])) {
            $data['payment_novapay_payment_Hold_Confirmed_status_id'] = $this->request->post['payment_novapay_payment_Hold_Confirmed_status_id'];
        } else {
            $data['payment_novapay_payment_Hold_Confirmed_status_id'] = $this->config->get('payment_novapay_payment_Hold_Confirmed_status_id') ?? 2;
        }

        if (isset($this->request->post['payment_novapay_payment_Hold_Completed_status_id'])) {
            $data['payment_novapay_payment_Hold_Completed_status_id'] = $this->request->post['payment_novapay_payment_Hold_Completed_status_id'];
        } else {
            $data['payment_novapay_payment_Hold_Completed_status_id'] = $this->config->get('payment_novapay_payment_Hold_Completed_status_id') ?? 5;
        }

        if (isset($this->request->post['payment_novapay_payment_Payment_Paid_status_id'])) {
            $data['payment_novapay_payment_Payment_Paid_status_id'] = $this->request->post['payment_novapay_payment_Payment_Paid_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Paid_status_id'] = $this->config->get('payment_novapay_payment_Payment_Paid_status_id') ?? 5;
        }

        if (isset($this->request->post['payment_novapay_payment_Payment_Failed_status_id'])) {
            $data['payment_novapay_payment_Payment_Failed_status_id'] = $this->request->post['payment_novapay_payment_Payment_Failed_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Failed_status_id'] = $this->config->get('payment_novapay_payment_Payment_Failed_status_id') ?? 10;
        }

        if (isset($this->request->post['payment_novapay_payment_Payment_Voided_status_id'])) {
            $data['payment_novapay_Payment_Voided_status_id'] = $this->request->post['payment_novapay_payment_Payment_Voided_status_id'];
        } else {
            $data['payment_novapay_payment_Payment_Voided_status_id'] = $this->config->get('payment_novapay_payment_Payment_Voided_status_id') ?? 16;
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/novapay_payment', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/payment/novapay_payment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_novapay_payment_merchant_id']) {
            $this->error['merchant_id'] = $this->language->get('error_merchant_id');
        }

        if (!$this->request->post['payment_novapay_payment_public_key']) {
            $this->error['public_key'] = $this->language->get('error_public_key');
        }

        if (!$this->request->post['payment_novapay_payment_private_key']) {
            $this->error['private_key'] = $this->language->get('error_private_key');
        }

        if ($this->request->post['payment_novapay_payment_public_key'] && $this->request->post['payment_novapay_payment_private_key']) {
            try {
                $rsa = new \Novapay\Payment\SDK\Security\RSA(
                    'semple data',
                    $this->request->post['payment_novapay_payment_private_key'],
                    $this->request->post['payment_novapay_payment_public_key']
                );

            } catch (\Throwable $e) {
                $this->error['private_key'] = $this->language->get('error_key');
            }
        }

        return !$this->error;
    }

    public function order()
    {
        $this->load->language('extension/payment/novapay_payment');

        if (isset($this->request->get['error'])) {
            $this->session->data['error_warning'] = $this->request->get['error'];
        }

        $data['cancel_name'] = $this->language->get('cancel_name');
        $data['complete_name'] = $this->language->get('complete_name');
//        $data['refund_name'] = $this->language->get('refund_name');
        $data['export_operations_name'] = $this->language->get('export_operations_name');

        $data['order_id'] = $this->request->get['order_id'];
        $data['user_token'] = $this->request->get['user_token'];

        $order = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $data['order_id'] . "'");

        $orderNov = $this->db->query("SELECT * FROM " . DB_PREFIX . "novapay_payment WHERE order_id = '" . $data['order_id'] . "' LIMIT 1");
        if ($orderNov->num_rows == 0) {
            return;
        }
        $data['isHold'] = 1 === (int)$orderNov->row['is_hold'];
        $data['novapay_status'] = 0;
        $data['novapay_id'] = $orderNov->row['session_id'] ?? null;

        $data['total'] = round($order->row['total'], 2);

        $data['action_url'] = $this->url->link(
            'extension/payment/novapay_payment/updateOrder',
            [
                'user_token' =>  $this->session->data['user_token'],
                'order_id' => $this->request->get['order_id'],
            ],
            true
        );

        $data['user_token'] = $this->session->data['user_token'];

        return $this->load->view('extension/payment/novapay_order', $data);
    }

    public function updateOrder()
    {
        $link = $this->url->link('sales/order', 'user_token=' . $this->session->data['user_token']);
        if (isset($this->request->get['order_id'])) {
            $link = $this->url->link(
                'sale/order/info',
                [
                    'user_token' => $this->session->data['user_token'],
                    'order_id' => (int)$this->request->get['order_id'],

                ], true
            );
        }
        if($this->request->server['REQUEST_METHOD'] === 'POST') {
            $this->load->language('extension/payment/novapay');
            $butt = $this->request->post['button_name'];
            $orderId = (int)$this->request->post['order_id'];
            $orderNovaPay = $this->db->query("SELECT * FROM " . DB_PREFIX . "novapay_payment WHERE order_id = '" . $orderId . "' LIMIT 1");

            if (!isset($orderNovaPay)) {
                $this->session->data['error_warning'] = 'Order not found';
                $this->response->redirect(
                    $this->url->link(
                        $link, '', true
                    )
                );
            }

            $this->load->model('sale/order');
            $error = '';

            if ($this->config->get('payment_novapay_payment_is_test_mode')) {
                Novapay\Payment\SDK\Model\Model::disableLiveMode();
            } else {
                Novapay\Payment\SDK\Model\Model::enableLiveMode();
            }

            Novapay\Payment\SDK\Model\Model::setPrivateKey($this->config->get('payment_novapay_payment_private_key'));
            Novapay\Payment\SDK\Model\Model::setPublicKey($this->config->get('payment_novapay_payment_public_key'));
            Novapay\Payment\SDK\Model\Model::setMerchantId($this->config->get('payment_novapay_payment_merchant_id'));

            $session = new Novapay\Payment\SDK\Model\Session();
            $session->id = $orderNovaPay->row['session_id'];

            $order_oc = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $orderId . "' LIMIT 1");

            $prefix = DB_PREFIX;
            $date_added = (new DateTime())->format('Y-m-d H:i:s');

            if ($butt == 'novapay-cancel-payment') {

                $payment = new Novapay\Payment\SDK\Model\Payment();
                $ok = $payment->cancel(Novapay\Payment\SDK\Model\Model::getMerchantId(), $session->id);

                $this->customLog($payment);

                if (!$ok) {
                    $error = $payment->getResponse()->message ?: $this->language->get['cancel_payment'];
                    $error = 'NovaPay: ' . addslashes($error);
                    $sql = <<<SQL
INSERT INTO {$prefix}order_history (order_id, order_status_id, notify, comment, date_added) 
VALUES ($orderId, {$order_oc->row['order_status_id']}, 0, '{$error}', '{$date_added}')
SQL;
                    $this->db->query($sql);
                } else {
                    $this->db->query(
                        "UPDATE " . DB_PREFIX . "order SET order_status_id = '"
                        . $this->config->get('payment_novapay_payment_Payment_Voided_status_id')
                        . "' WHERE order_id = '" . $orderId . "'"
                    );
                    $sql = <<<SQL
INSERT INTO {$prefix}order_history (order_id, order_status_id, notify, comment, date_added) 
VALUES ($orderId, {$this->config->get('payment_novapay_payment_Payment_Voided_status_id')}, 0, 'Success', '{$date_added}')
SQL;
                    $this->db->query($sql);
                }
            }

            if ($butt == 'novapay-confirm-hold') {
                $payment = new Novapay\Payment\SDK\Model\Payment();
                $ok = $payment->complete(Novapay\Payment\SDK\Model\Model::getMerchantId(), $session->id, $this->request->post['amount']);

                $this->customLog($payment);

                if (!$ok) {
                    $error = $payment->getResponse()->message ?: $this->language->get['cancel_payment'];
                    $error = 'NovaPay: ' . addslashes($error);
                    $sql = <<<SQL
INSERT INTO {$prefix}order_history (order_id, order_status_id, notify, comment, date_added) 
VALUES ($orderId, {$order_oc->row['order_status_id']}, 0, '{$error}', '{$date_added}')
SQL;
                    $this->db->query($sql);
                } else {
                    $this->db->query(
                        "UPDATE " . DB_PREFIX . "order SET order_status_id = '"
                        . $this->config->get('payment_novapay_payment_Payment_Holded_status_id')
                        . "' WHERE order_id = '" . $orderId . "'"
                    );
                    $sql = <<<SQL
INSERT INTO {$prefix}order_history (order_id, order_status_id, notify, comment, date_added) 
VALUES ($orderId, {$this->config->get('payment_novapay_payment_Payment_Holded_status_id')}, 0, 'Success: ' . $this->request->post['amount'], '{$date_added}')
SQL;
                    $this->db->query($sql);
                }
            }

            if ($butt == 'novapay-export-operations') {

            }

            if ($error !== '') {
                $link = $this->url->link(
                    'sale/order/info',
                    [
                        'user_token' => $this->session->data['user_token'],
                        'order_id' => $orderId,
                        'error' => $error,

                    ], true
                );
                $this->session->data['error_warning'] = $error;
                $this->response->redirect($link);
            } else {
                $this->response->redirect($link);
            }
        } else {
            $this->response->redirect(
                $this->url->link(
                    $link, '', true
                )
            );
        }
    }

    public function install() {
        $this->load->model('extension/payment/novapay_payment');
        $this->model_extension_payment_novapay_payment->install();
    }

    public function uninstall() {
        $this->load->model('extension/payment/novapay_payment');
        $this->model_extension_payment_novapay_payment->uninstall();
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
}