<?php
class ModelExtensionPaymentNovapayPayment extends Model {

    public function install() {
        // Create table
        // $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "novapay_payment` ...");

        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "novapay_payment` (
          `novapay_id` int(11) NOT NULL AUTO_INCREMENT,
          `session_id` varchar(255) COLLATE utf8_bin NOT NULL,
          `order_id` varchar(255) COLLATE utf8_bin NOT NULL,
          `is_hold` int(1),
          PRIMARY KEY (`novapay_id`)
        )");
    }

    public function uninstall() {
        // Delete settings
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE code = 'payment_novapay_payment'");

        // Delete table
//        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "novapay_payment`");
    }

    public function getData() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE code = 'payment_novapay_payment'");
        return $query->rows;
    }
}