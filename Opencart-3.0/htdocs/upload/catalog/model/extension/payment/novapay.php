<?php
class ModelExtensionPaymentNovapay extends Model {

  public function getMethod($address, $total) {
    $this->load->language('extension/payment/novapay');

    $status = false;

    if($this->session->data['currency'] == 'UAH') {
      $status = true;
    }

    $method_data = array();

    if ($status) {
      $method_data = array(
        'code'       => 'novapay',
        'title'      => $this->language->get('text_title'),
        'terms'      => '',
        'sort_order' => $this->config->get('payment_novapay_sort_order')
      );
    }

    return $method_data;
  }
}

?>
