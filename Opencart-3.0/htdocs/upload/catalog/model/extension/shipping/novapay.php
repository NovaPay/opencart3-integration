<?php

use Novapay\Payment\SDK\Model\Delivery;

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER["DOCUMENT_ROOT"] . '/novapay-sdk/bootstrap.php';

class ModelExtensionShippingNovapay extends Model
{
    private $_error;
    private $_all_units;
    private $_weight_units;
    private $_length_units;
    private $_system_units = [];
    private $_products;

    /**
     * @param mixed $address
     *
     * @return [type]
     */
    function getQuote($address)
    {
        $this->load->language('extension/shipping/novapay');

        // retun empty method if it is not allowed
        // if TRUE getProducts() is cached and it has [].
        if (!$this->isAllowed($address)) {
            return [];
        }

        $info = $this->getShippingInfo();

        if(strpos($_SERVER['HTTP_REFERER'], 'checkout/checkout') !== FALSE) {
            echo "<script src='/catalog/view/javascript/shipping-novapay.js'></script>";
            echo "<link href='/catalog/view/css/shipping-novapay.css' rel='stylesheet'>";
        }

        $html = '<span data-absent="' . $this->language->get("text_attr") .
                '" data-city="' . $this->language->get('text_city') .
                '" data-depart="' . $this->language->get('text_depart') .
                '" data-volume="' . $info['volume'] .
                '" data-weight="' . $info['weight'] .
                '" class="novapay-shipping-price">' .
                $this->currency->format(0.00, $this->session->data['currency']) .
                '</span>';
        return [
            'code'       => 'novapay',
            'title'      => $this->language->get('text_title'),
            'quote'      => [
                'novapay' => [
                    'code'         => 'novapay.novapay',
                    'title'        => $this->language->get('text_description'),
                    'cost'         => 0.00,
                    'tax_class_id' => 0,
                    'text'         => $html
                ]
            ],
            'sort_order' => $this->config->get('shipping_novapay_sort_order'),
            'error'      => false
        ];
    }

    /**
     * Is shipping allowed based on shopping cart data.
     * Denied for
     * Products without required attributes: dimensions:
     *  - width
     *  - height
     *  - depth
     *  - weight
     * Country <> UA
     *
     * @return bool TRUE if allowed, FALSE otherwise.
     */
    protected function isAllowed($address)
    {
        if (empty($address)) {
            // shipping address is empty
            $this->setError('shipping_address');
            return false;
        }
        if ('UA' != $address['iso_code_2']) {
            // only Ukraine is allowed
            $this->setError('shipping_address.iso_code_2');
            return false;
        }

        if (false === $this->getProducts()) {
            // not all of the products have required attributes:
            // widths, height, depth, weight
            $this->setError('false === getProducts()');
            return false;
        }

        $products = $this->getProducts();
        if (empty($products)) {
            $this->setError('empty getProducts()');
            return false;
        }

        return true;
    }

    /**
     * Returns array of products in the shopping cart.
     * If there are products without required attributes: dimensions:
     *  - width
     *  - height
     *  - depth
     *  - weight
     *
     * @return array|false Products list on success FALSE on failure.
     */
    public function getProducts()
    {
        if (null !== $this->_products) {
            return $this->_products;
        }
        $this->load->model('catalog/product');

        $delivery = new Delivery();

        $this->_products = false;
        $products = [];
        $items = $this->cart->getProducts();
        foreach ($items as $product) {
            $w = $this->getLengthUnitValue($product['width'], $product);
            $h = $this->getLengthUnitValue($product['height'], $product);
            $l = $this->getLengthUnitValue($product['length'], $product);
            $m = $this->getWeightUnitValue($product['weight'], $product);
            if ($w <= 0 || $h <= 0 || $l <= 0 || $m <= 0) {
                // if at least one dimension
                // in at least in one item in the shopping cart
                // is empty/zero
                // shipping method is not available
                return $this->_products;
            }
            $qty = floatval($product['quantity']);
            $product['converted'] = [
                'width'  => $w,
                'height' => $h,
                'length' => $l,
                'weight' => $m,
                'volume' => $qty * $delivery->calcVolumetricWeight($w, $h, $l)
            ];
            $products[] = $product;
        }
        $this->_products = $products;
        return $this->_products;
    }

    /**
     * Returns all units combined in one indexed array.
     *
     * @return array
     */
    protected function getAllUnits()
    {
        if (null === $this->_all_units) {
            $all = array_merge(
                $this->getLengthUnits(),
                $this->getWeightUnits()
            );
            $this->_all_units = [];
            foreach ($all as $unit) {
                $this->_all_units[$unit['attribute_id']] = $unit;
            }
        }

        return $this->_all_units;
    }

    /**
     * Returns multiplied length unit value depending on current shop settings.
     *
     * @param float $value Length value.
     *
     * @return float       Multiplied length value.
     */
    public function getLengthUnitValue($value, array $product)
    {
        if (empty($this->_system_units['length_class_description'])) {
            $this->getLengthUnits();
        }
        $units = $this->_system_units['length_class_description'];
        if (!isset($units[$product['length_class_id']])
        ) {
            return 0;
        }
        $all = $this->getAllUnits();
        $unit = $units[$product['length_class_id']]['unit'];
        return $value / $all[$unit]['divider'];
    }

    /**
     * Returns multiplied weight unit value depending on current shop settings.
     *
     * @param float $value Weight value.
     *
     * @return float       Multiplied weight value.
     */
    public function getWeightUnitValue($value, array $product)
    {
        if (empty($this->_system_units['weight_class_description'])) {
            $this->getWeightUnits();
        }
        $units = $this->_system_units['weight_class_description'];
        if (!isset($units[$product['weight_class_id']])
        ) {
            return 0;
        }
        $all = $this->getAllUnits();
        $unit = $units[$product['weight_class_id']]['unit'];
        return $value / $all[$unit]['divider'];
    }

    /**
     * Returns length/weight units from the database.
     *
     * @param string $table The table name.
     * @param array  $mult  Associated array with multipliers
     *                      ['kg' => 1, 'g' => 1000]
     *
     * @return array
     */
    protected function queryUnits($table, $primary_key, array $mult)
    {
        $this->_system_units[$table] = [];

        $units = [];
        $sql = sprintf(
            "SELECT * FROM %s%s WHERE language_id = %s",
            DB_PREFIX,
            $table,
            $this->config->get('config_language_id')
        );
        $query = $this->db->query($sql);
        foreach ($query->rows as $unit) {
            // store to get units by {weight|length}_class_id later.
            $this->_system_units[$table][$unit[$primary_key]] = $unit;
            $units[] = [
                'attribute_id' => $unit['unit'],
                'name'         => trim($unit['title']),
                'divider'      => isset($mult[$unit['unit']])
                                ? $mult[$unit['unit']] : 1
            ];
        }
        return $units;
    }

    /**
     * Returns array of length units.
     *
     * @return array [][]{attribute_id, name, divider}
     */
    public function getLengthUnits()
    {
        if (null !== $this->_length_units) {
            return $this->_length_units;
        }
        $this->_length_units = $this->queryUnits(
            'length_class_description',
            'length_class_id',
            [
                'cm' => 100,
                'mm' => 1000,
                'm'  => 1,
                'in' => 39.3701
            ]
        );
        return $this->_length_units;
    }

    /**
     * Returns array of weight units.
     *
     * @return array [][]{attribute_id, name, divider}
     */
    public function getWeightUnits()
    {
        if (null !== $this->_weight_units) {
            return $this->_weight_units;
        }
        $this->_weight_units = $this->queryUnits(
            'weight_class_description',
            'weight_class_id',
            [
                'lb' => 2.20462,
                'oz' => 35.274,
                'mg' => 1000 * 1000,
                'g'  => 1000
            ]
        );
        return $this->_weight_units;
    }

    /**
     * Returns shipping delivery info {weight, volume, total}
     *
     * @return array
     */
    public function getShippingInfo()
    {
        $result = [
            'weight' => 0,
            // 'width'  => '',
            'volume' => 0,
            'total'  => $this->cart->getTotal()
        ];

        foreach ($this->getProducts() as $product) {
            $result['volume'] += $product['converted']['volume'];
            $result['weight'] += $product['converted']['weight'];
        }

        return $result;
    }

    /**
     * Sets error for the shipping model to trach why is not allowed.
     *
     * @param string $error Error message.
     *
     * @return void
     */
    protected function setError($error)
    {
        $this->_error = $error;
    }

    /**
     * Return error message.
     *
     * @return string|null
     */
    protected function getError()
    {
        return $this->_error;
    }
}
