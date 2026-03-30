<?php
/**
 * Warehouse short schema class.
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
 * Warehouse short schema class.
 * Used to save bandwidth for long lists.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class WarehouseShort extends Schema
{
    /**
     * Идентификатор адреса string[36]
     *
     * @var string
     */
    public $ref;
    /**
     * Название отделения на Украинском string[99]
     *
     * @var string
     */
    public $title;
    /**
     * Номер отделения int[99999]
     *
     * @var int
     */
    public $no;

    /**
     * Converts Warehouse model to WarehouseShort and returns it's instance.
     * 
     * @param Warehouse $house Original model.
     * 
     * @return WarehouseShort  Short model.
     */
    public static function fromWarehouse(Warehouse $house)
    {
        $me = new self();
        $me->ref        = $house->Ref;
        $me->title      = $house->Description;
        $me->no         = intval($house->Number);
        return $me;
    }
}