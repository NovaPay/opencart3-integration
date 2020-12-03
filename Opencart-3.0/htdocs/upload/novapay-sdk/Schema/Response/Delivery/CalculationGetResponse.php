<?php
/**
 * Calculation GET response schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

/**
 * Calculation GET response schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef
 */
class CalculationGetResponse extends Response
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

    // public $items = [];

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
        // $this->items = [];
        // foreach ($this->data as $item) {
        //     $model = new CargoType();
        //     $model->setValues($item);
        //     $this->items[] = $model;
        // }
    }
}