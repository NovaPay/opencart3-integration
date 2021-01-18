<?php

require_once(__DIR__ . '/../../../../catalog/model/extension/shipping/novapay.php');
// require_once('catalog/model/extension/shipping/novapay.php');

class ControllerExtensionShippingNovapay extends Controller
{
	private $error = array();

	public function index()
	{
		$this->load->language('extension/shipping/novapay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('shipping_novapay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data = $this->fillInBreadcrumbs($data);

		$data['action'] = $this->url->link('extension/shipping/novapay', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);

		$attrs = $this->db->query("SELECT * FROM `" . DB_PREFIX . "attribute_description` WHERE language_id=" . $this->config->get('config_language_id'));
		$data['attrs_list'] = $attrs->rows;
		$modelNovapay = new ModelExtensionShippingNovapay($this->registry);
		$data['attrs_list_weight'] = $modelNovapay->getWeightUnits();
		$data['attrs_list_length'] = $modelNovapay->getLengthUnits();

		$data = $this->fillInData($data, 'shipping_novapay_weight_size');
		$data = $this->fillInData($data, 'shipping_novapay_length_size');
		$data = $this->fillInData($data, 'shipping_novapay_status');
		$data = $this->fillInData($data, 'shipping_novapay_sort_order');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/novapay', $data));
	}

	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/shipping/novapay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	/**
	 * Fills in the request.post variable if it exists or config variable if does not.
	 *
	 * @param array  $data View data to fill in..
	 * @param string $name The name of variable.
	 * 
	 * @return array
	 */
	protected function fillInData(array $data, $name)
	{
		$value = isset($this->request->post[$name]) 
			? $this->request->post[$name] 
			: $this->config->get($name);
		$data[$name] = $value;
		return $data;
	}

	/**
	 * Fills in the breadcrumbs array to display on the page.
	 *
	 * @param array $data View data to fill in.
	 * 
	 * @return array
	 */
	protected function fillInBreadcrumbs(array $data)
	{
		$token = 'user_token=' . $this->session->data['user_token'];
		$data['breadcrumbs'] = [
			[
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link(
					'common/dashboard',
					$token,
					true
				)
			],
			[
				'text' => $this->language->get('text_extension'),
				'href' => $this->url->link(
					'marketplace/extension',
					"$token&type=shipping",
					true
				)
			],
			[
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link(
					'extension/shipping/novapay',
					$token,
					true
				)
			]
		];
		return $data;
	}
}
