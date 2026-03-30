<?php
/**
 * Cargo types get list response schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/55702571a0fe4f0b64838909
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

/**
 * Cargo types get list response schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/55702570a0fe4f0cf4fc53ed/operations/55702571a0fe4f0b64838909
 */
class CargoTypesGetResponse extends Response
{
    public $success;
    public $data;
    public $errors;
    public $warnings;
    public $info;
    public $messageCodes;
    public $errorCodes;
    public $warningCodes;
    public $infoCodes;

    public $items = [];

    /**
     * Function called right after body is parsed and values are set.
     * 
     * @return void
     */
    protected function afterUpdate()
    {
        parent::afterUpdate();
        if (!is_array($this->data)) {
            return;
        }
        $this->items = [];
        foreach ($this->data as $item) {
            $model = new CargoType();
            $model->setValues($item);
            $this->items[] = $model;
        }
    }
}