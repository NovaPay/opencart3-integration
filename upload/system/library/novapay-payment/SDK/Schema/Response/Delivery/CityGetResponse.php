<?php
/**
 * City GET response schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */

namespace Novapay\Payment\SDK\Schema\Response\Delivery;

/**
 * City GET response schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class CityGetResponse extends Response
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

    public $city = [];

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
        $model = new City();
        $model->setValues($item);
        $this->city = CityShort::fromCity($model);
    }
}