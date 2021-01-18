<?php
/**
 * City short schema class.
 * Used to save bandwidth for long lists.
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

use Novapay\Payment\SDK\Model\Delivery\Warehouses;
use Novapay\Payment\SDK\Schema\Schema;

/**
 * City short schema class.
 * Used to save bandwidth for long lists.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class CityShort extends Schema
{
    /**
     * Идентификатор адреса string[36]
     *
     * @var string
     */
    public $Ref;
    /**
     * Название отделения на Украинском string[99]
     *
     * @var string
     */
    public $Description;

    /**
     * Converts City model to CityShort and returns it's instance.
     * 
     * @param City $city Original model.
     * 
     * @return CityShort Short model.
     */
    public static function fromCity(City $city)
    {
        $me = new self();
        $me->Ref         = $city->Ref;
        $me->Description = $city->Description;
        return $me;
    }
}