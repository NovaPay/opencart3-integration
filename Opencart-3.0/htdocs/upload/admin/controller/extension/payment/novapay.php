<?php

class ControllerExtensionPaymentNovapay extends Controller
{
    const PAYMENT_TYPE_HOLD = '2';

    private $error = array();


    public function install()
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "novapay` (
          `novapay_id` int(11) NOT NULL AUTO_INCREMENT,
          `session_id` varchar(255) COLLATE utf8_bin NOT NULL,
          `order_id` varchar(255) COLLATE utf8_bin NOT NULL,
          `payment_type` varchar(255) COLLATE utf8_bin NOT NULL,
          PRIMARY KEY (`novapay_id`)
        )");
    }

    public function index()
    {
        $this->install();

        $this->document->addScript('../catalog/view/javascript/cookie.js');
        $this->document->addScript('../catalog/view/javascript/admin-novapay.js');

        $this->load->language('extension/payment/novapay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('payment_novapay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['text_live_mode'] = $this->language->get('text_live_mode');
        $data['text_test_mode'] = $this->language->get('text_test_mode');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_all_zones'] = $this->language->get('text_all_zones');

        $data['entry_email'] = $this->language->get('entry_email');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_order_status_completed_text'] = $this->language->get('entry_order_status_completed_text');
        $data['entry_order_status_pending'] = $this->language->get('entry_order_status_pending');
        $data['entry_order_status_canceled'] = $this->language->get('entry_order_status_canceled');
        $data['entry_order_status_failed'] = $this->language->get('entry_order_status_failed');
        $data['entry_order_status_failed_text'] = $this->language->get('entry_order_status_failed_text');
        $data['entry_order_status_processing'] = $this->language->get('entry_order_status_processing');
        $data['entry_status'] = $this->language->get('entry_status');

        $data['entry_merchantid'] = $this->language->get('entry_merchantid');
        $data['entry_merchantid_help'] = $this->language->get('entry_merchantid_help');
        $data['entry_passprivate'] = $this->language->get('entry_passprivate');
        $data['entry_publickey'] = $this->language->get('entry_publickey');
        $data['entry_privatekey'] = $this->language->get('entry_privatekey');
        $data['entry_successurl'] = $this->language->get('entry_successurl');
        $data['entry_successurl_help'] = $this->language->get('entry_successurl_help');
        $data['entry_failurl'] = $this->language->get('entry_failurl');
        $data['entry_failurl_help'] = $this->language->get('entry_failurl_help');

        $data['entry_test_mode'] = $this->language->get('entry_test_mode');
        $data['entry_test_mode_help'] = $this->language->get('entry_test_mode_help');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['tab_general'] = $this->language->get('tab_general');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['merchantid'])) {
            $data['error_merchantid'] = $this->error['merchantid'];
        } else {
            $data['error_merchantid'] = '';
        }

        if (isset($this->error['passprivate'])) {
            $data['error_passprivate'] = $this->error['passprivate'];
        } else {
            $data['error_passprivate'] = '';
        }

        if (isset($this->error['publickey'])) {
            $data['error_publickey'] = $this->error['publickey'];
        } else {
            $data['error_publickey'] = '';
        }

        if (isset($this->error['privatekey'])) {
            $data['error_privatekey'] = $this->error['privatekey'];
        } else {
            $data['error_privatekey'] = '';
        }

        if (isset($this->error['successurl'])) {
            $data['error_successurl'] = $this->error['successurl'];
        } else {
            $data['error_successurl'] = '';
        }

        if (isset($this->error['failurl'])) {
            $data['error_failurl'] = $this->error['failurl'];
        } else {
            $data['error_failurl'] = '';
        }

        if (isset($this->error['payment_type'])) {
            $data['error_payment_type'] = $this->error['payment_type'];
        } else {
            $data['error_payment_type'] = '';
        }


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/novapay', 'user_token=' . $this->session->data['user_token'], true),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/payment/novapay', 'user_token=' . $this->session->data['user_token'], true);

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);


        if (isset($this->request->post['payment_novapay_merchantid'])) {
            $data['payment_novapay_merchantid'] = $this->request->post['payment_novapay_merchantid'];
        } else {
            $data['payment_novapay_merchantid'] = $this->config->get('payment_novapay_merchantid');
        }

        if (isset($this->request->post['payment_novapay_passprivate'])) {
            $data['payment_novapay_passprivate'] = $this->request->post['payment_novapay_passprivate'];
        } else {
            $data['payment_novapay_passprivate'] = $this->config->get('payment_novapay_passprivate');
        }

        if (isset($this->request->post['payment_novapay_publickey'])) {
            $data['payment_novapay_publickey'] = $this->request->post['payment_novapay_publickey'];
        } else {
            $data['payment_novapay_publickey'] = $this->config->get('payment_novapay_publickey');
        }

        if (isset($this->request->post['payment_novapay_privatekey'])) {
            $data['payment_novapay_privatekey'] = $this->request->post['payment_novapay_privatekey'];
        } else {
            $data['payment_novapay_privatekey'] = $this->config->get('payment_novapay_privatekey');
        }

        if (isset($this->request->post['payment_novapay_successurl'])) {
            $data['payment_novapay_successurl'] = $this->request->post['payment_novapay_successurl'];
        } else {
            $data['payment_novapay_successurl'] = $this->config->get('payment_novapay_successurl');
        }

        if (isset($this->request->post['payment_novapay_failurl'])) {
            $data['payment_novapay_failurl'] = $this->request->post['payment_novapay_failurl'];
        } else {
            $data['payment_novapay_failurl'] = $this->config->get('payment_novapay_failurl');
        }

        if (isset($this->request->post['payment_novapay_payment_type_value'])) {
            $data['payment_novapay_payment_type_value'] = $this->request->post['payment_novapay_payment_type_value'];
        } else {
            $data['payment_novapay_payment_type_value'] = $this->config->get('payment_novapay_payment_type_value');
        }

        if (isset($this->request->post['payment_novapay_created_status_id'])) {
            $data['payment_novapay_created_status_id'] = $this->request->post['payment_novapay_created_status_id'];
        } else {
            $data['payment_novapay_created_status_id'] = $this->config->get('payment_novapay_created_status_id');
        }

        if (isset($this->request->post['payment_novapay_expired_status_id'])) {
            $data['payment_novapay_expired_status_id'] = $this->request->post['payment_novapay_expired_status_id'];
        } else {
            $data['payment_novapay_expired_status_id'] = $this->config->get('payment_novapay_expired_status_id');
        }

        if (isset($this->request->post['payment_novapay_processing_status_id'])) {
            $data['payment_novapay_processing_status_id'] = $this->request->post['payment_novapay_processing_status_id'];
        } else {
            $data['payment_novapay_processing_status_id'] = $this->config->get('payment_novapay_processing_status_id');
        }

        if (isset($this->request->post['payment_novapay_holded_status_id'])) {
            $data['payment_novapay_holded_status_id'] = $this->request->post['payment_novapay_holded_status_id'];
        } else {
            $data['payment_novapay_holded_status_id'] = $this->config->get('payment_novapay_holded_status_id');
        }

        if (isset($this->request->post['payment_novapay_hold_confirmed_status_id'])) {
            $data['payment_novapay_hold_confirmed_status_id'] = $this->request->post['payment_novapay_hold_confirmed_status_id'];
        } else {
            $data['payment_novapay_hold_confirmed_status_id'] = $this->config->get('payment_novapay_hold_confirmed_status_id');
        }

        if (isset($this->request->post['payment_novapay_processing_hold_completion_status_id'])) {
            $data['payment_novapay_processing_hold_completion_status_id'] = $this->request->post['payment_novapay_processing_hold_completion_status_id'];
        } else {
            $data['payment_novapay_processing_hold_completion_status_id'] = $this->config->get('payment_novapay_processing_hold_completion_status_id');
        }

        if (isset($this->request->post['payment_novapay_paid_status_id'])) {
            $data['payment_novapay_paid_status_id'] = $this->request->post['payment_novapay_paid_status_id'];
        } else {
            $data['payment_novapay_paid_status_id'] = $this->config->get('payment_novapay_paid_status_id');
        }

        if (isset($this->request->post['payment_novapay_failed_status_id'])) {
            $data['payment_novapay_failed_status_id'] = $this->request->post['payment_novapay_failed_status_id'];
        } else {
            $data['payment_novapay_failed_status_id'] = $this->config->get('payment_novapay_failed_status_id');
        }

        if (isset($this->request->post['payment_novapay_processing_void_status_id'])) {
            $data['payment_novapay_processing_void_status_id'] = $this->request->post['payment_novapay_processing_void_status_id'];
        } else {
            $data['payment_novapay_processing_void_status_id'] = $this->config->get('payment_novapay_processing_void_status_id');
        }

        if (isset($this->request->post['payment_novapay_voided_status_id'])) {
            $data['payment_novapay_voided_status_id'] = $this->request->post['payment_novapay_voided_status_id'];
        } else {
            $data['payment_novapay_voided_status_id'] = $this->config->get('payment_novapay_voided_status_id');
        }

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        if (isset($this->request->post['payment_novapay_status'])) {
            $data['payment_novapay_status'] = $this->request->post['payment_novapay_status'];
        } else {
            $data['payment_novapay_status'] = $this->config->get('payment_novapay_status');
        }

        if (isset($this->request->post['payment_novapay_test_mode'])) {
            $data['payment_novapay_test_mode'] = $this->request->post['payment_novapay_test_mode'];
        } else {
            $data['payment_novapay_test_mode'] = $this->config->get('payment_novapay_test_mode');
        }

        $this->load->model('localisation/geo_zone');

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/novapay', $data));
    }

    private function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/novapay')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['payment_novapay_merchantid']) {
            $this->error['merchantid'] = $this->language->get('error_merchantid');
        }

        if (!$this->request->post['payment_novapay_publickey']) {
            $this->error['publickey'] = $this->language->get('error_publickey');
        }

        if (!$this->request->post['payment_novapay_privatekey']) {
            $this->error['privatekey'] = $this->language->get('error_privatekey');
        }

        if (!$this->request->post['payment_novapay_successurl']) {
            $this->error['successurl'] = $this->language->get('error_successurl');
        }

        if (!$this->request->post['payment_novapay_failurl']) {
            $this->error['failurl'] = $this->language->get('error_failurl');
        }

        if (!$this->request->post['payment_novapay_payment_type_value']) {
            $this->error['payment_type'] = $this->language->get('error_payment_type');
        }


        return !$this->error;
    }

    public function setChose($status) {
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

    public function order()
    {
        $data['order_id'] = intval($this->request->get['order_id']);
        $data['user_token'] = $this->request->get['user_token'];

        $order_statuses = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $data['order_id'] . "'");
        $order_status = $this->setChose($order_statuses->row['order_status_id']);

        $order_oc = $this->db->query("SELECT * FROM " . DB_PREFIX . "order WHERE order_id = '" . $data['order_id'] . "' LIMIT 1");

        $data['total'] = $order_statuses->row['total'];

        $data['hold'] = $this->isPaymentInHoldStatus($data['order_id'], $order_status);

        // if in ['paid', 'hold_confirmed', 'holded']
        //    && !['failed', 'processing_void', 'voided', 'expired']
        //     > can be canceled the same day
        if((((in_array($order_status, ['paid', 'processing_hold_completion'])) && date('m.d.y', strtotime($order_oc->row['date_added'])) == date('m.d.y')) || in_array($order_status, ['holded', 'hold_confirmed'])) && !in_array($order_status, ['failed', 'processing_void', 'voided', 'expired'])) {
            $data['cancel'] = true;
        } else $data['cancel'] = false;

        //$data['test'] = $this->setChose($order_statuses->row['order_status_id']);
        $data['action_url'] = $this->url->link('extension/payment/novapay/updateOrder&user_token=' . $this->request->get['user_token'], '', 'SSL');
        $data['action_url'] = (strpos($_SERVER['SERVER_PROTOCOL'], 'HTTP/1.0') === FALSE ?'https://' : 'http://'). $_SERVER['SERVER_NAME'] .'/index.php?route=extension/payment/novapay/updateOrder';
        return $this->load->view('extension/payment/novapay_order', $data);
    }

    /**
     * Check if payment is in hold status to show or hide
     *
     * @param string $order_id     The order id.
     * @param string $order_status The order status.
     *
     * @return bool           TRUE when button is shown, FALSE if button is hidden.
     */
    protected function isPaymentInHoldStatus($order_id, $order_status)
    {
        $orders = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "novapay WHERE order_id = '$order_id' LIMIT 1"
        );
        if (static::PAYMENT_TYPE_HOLD != $orders->row['payment_type']) {
            return false;
        }
        if ($order_status == 'holded') {
            return true;
        }
        return false;
    }

}
