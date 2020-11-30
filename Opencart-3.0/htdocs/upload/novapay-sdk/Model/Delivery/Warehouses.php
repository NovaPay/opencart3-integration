<?php
/**
 * Delivery Warehouses model class.
 * Retrieves the information about warehouses.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d8211a0fe4f08e8f7ce45
 */

namespace Novapay\Payment\SDK\Model\Delivery;

use Novapay\Payment\SDK\Model\Model;
use Novapay\Payment\SDK\Schema\Response\Delivery\Response;
use Novapay\Payment\SDK\Schema\Request\Delivery\WarehousesGetRequest;
use Novapay\Payment\SDK\Schema\Response\Delivery\WarehousesGetResponse;
use Novapay\Payment\SDK\Schema\Response\Delivery\Warehouse;
use Novapay\Payment\SDK\Schema\Response\Delivery\WarehouseType;

/**
 * Delivery Warehouses model class.
 * Retrieves the information about warehouses.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d8211a0fe4f08e8f7ce45
 */
class Warehouses extends Model
{
    /**
     * Payment processing URL to redirect user there.
     * 
     * @var string Processing URL.
     */
    public $items = [];

    private static $_types = [];

    /**
     * Search for the warehouses by city reference id.
     * 
     * @param string $cityRef City reference id.
     * 
     * @return bool           TRUE on success, FALSE on failure.
     */
    public function getByCityRef($cityRef)
    {
        $request = new WarehousesGetRequest(Model::getMerchantId(), $cityRef);
        $response = $this->send($request, 'POST', '/delivery-info');
        $res = Response::create(
            $response[1],
            $response[0],
            WarehousesGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof WarehousesGetResponse) {
            return false;
        }

        $this->items = $res->items;
        foreach ($this->items as $i => $item) {
            if (!$item instanceof Warehouse) {
                continue;
            }
            $item->type = static::getType($item->CityRef, $item->TypeOfWarehouse);
        }

        return true;
    }

    /**
     * Sets warehouse types to get them as object in responses.
     * 
     * @param string $cityRef City reference id.
     * @param array  $types   Warehouse types for current city.
     * 
     * @return void
     */
    public static function setTypes($cityRef, $types = [])
    {
        if (!array_key_exists($cityRef, static::$_types)) {
            static::$_types[$cityRef] = [];
        }
        foreach ($types as $item) {
            if (!array_key_exists($item->Ref, static::$_types[$cityRef])) {
                static::$_types[$cityRef][$item->Ref] = [];
            }
            static::$_types[$cityRef][$item->Ref] = $item;
        }
    }

    /**
     * Returns type of warehouse object for current city and type id.
     * 
     * @param string $cityRef City reference id.
     * @param string $typeRef Warehouse type reference id.
     * 
     * @return WarehouseType Type of warehouse instance
     */
    public static function getType($cityRef, $typeRef)
    {
        return isset(static::$_types[$cityRef][$typeRef]) 
            ? static::$_types[$cityRef][$typeRef] : new WarehouseType();
    }
}
