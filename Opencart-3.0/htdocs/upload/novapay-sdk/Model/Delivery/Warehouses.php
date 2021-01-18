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
use Novapay\Payment\SDK\Schema\Response\Delivery\WarehouseShort;
use Novapay\Payment\SDK\Schema\Response\Delivery\WarehouseType;
use Novapay\Payment\SDK\Schema\Response\Delivery\WarehouseTypesGetResponse;

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
    const BATCH_LIMIT = 1000;
    /**
     * Payment processing URL to redirect user there.
     * 
     * @var string Processing URL.
     */
    public $items = [];

    /**
     * Amount of the warehouses in the result.
     * Used for pagination.
     *
     * @var int
     */
    public $count;

    /**
     * Current page.
     *
     * @var int
     */
    public $page;

    /**
     * Limit of items on the page.
     *
     * @var int
     */
    public $limit;

    /**
     * Count of the pages if more items than limit provided in the result.
     *
     * @var int
     */
    public $pagesCount;

    private static $_types = [];

    /**
     * Search for the warehouses by city reference id.
     * 
     * @param string $cityRef City reference id.
     * @param int    $limit   Count of the items per page.
     * @param int    $page    Current page number.
     * 
     * @return bool           TRUE on success, FALSE on failure.
     */
    public function getByCityRef($cityRef, $limit = 50, $page = 1)
    {
        $request = new WarehousesGetRequest(Model::getMerchantId(), $cityRef, $limit, $page);
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

        $this->count = $res->info->totalCount;
        $this->limit = $limit;
        $this->page  = $page;
        $this->pagesCount = ceil($this->count / $limit);

        $this->items = $res->items;
        foreach ($this->items as $i => $item) {
            if ($item instanceof Warehouse) {
                $type = static::getType($cityRef, $item->TypeOfWarehouse);
                $item->type = $type ? $type->Description : null;
            }
            $this->items[$i] = $item;
        }

        return true;
    }

    /**
     * Retrieves all warehouses by city reference id.
     * 
     * @param string $cityRef City reference id.
     * 
     * @return bool           TRUE on success, FALSE on failure.
     */
    public function all($cityRef)
    {
        $items = [];
        $page = 1;
        do {
            $res = $this->getByCityRef($cityRef, static::BATCH_LIMIT, $page++);
            if (!$res) {
                return false;
            }
            $items = array_merge($items, $this->items);
        } while ($page <= $this->pagesCount);

        $this->items = $items;
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
