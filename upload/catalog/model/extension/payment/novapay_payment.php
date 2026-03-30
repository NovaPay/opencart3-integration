<?php
class ModelExtensionPaymentNovapayPayment extends Model {

//    public function getData() {
//        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE code = 'payment_novapay_payment'");
//        return $query->rows;
//    }

    public function getMethod($address, $total) {
        $this->load->language('extension/payment/novapay_payment');

//        $status = false;

//        if($this->session->data['currency'] == 'UAH') {
            $status = true;
//        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'novapay_payment',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_novapay_payment_checkout_sort_order')
            );
        }

        return $method_data;
    }
}