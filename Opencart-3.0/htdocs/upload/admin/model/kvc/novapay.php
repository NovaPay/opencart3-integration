<?php
class ModelExtensionPaymentNovapay extends Model {
	public function addNovapay($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "novapay SET order_id = '". $data['order_id'] ."', session_id = '" . $data['session_id'] . "', payment_type='". $data['payment_type'] ."'");
	}

	public function editNovapay($id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "novapay SET status = '" . (int)$data['status'] . "' WHERE novapay_id = '" . (int)$id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "novapay_description WHERE novapay_id = '" . (int)$id. "'");

		foreach ($data['novapay'] as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX ."novapay_description SET novapay_id = '" . (int)$id . "', language_id = '" . (int)$key . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'novapay_id=" . (int)$id. "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'novapay_id=" . (int)$id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
	}

	public function getNovapay($id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'novapay_id=" . (int)$id . "') AS keyword FROM " . DB_PREFIX . "novapay WHERE novapay_id = '" . (int)$id . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function deleteNovapay($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "novapay WHERE novapay_id = '" . (int)$id . "'");
	}
}
?>
