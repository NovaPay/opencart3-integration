<?php
/**
 * Warehouse schema class.
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

use Novapay\Payment\SDK\Schema\Schema;

/**
 * Warehouse schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class Warehouse extends Schema
{
    /**
     * Идентификатор адреса string[36]
     *
     * @var string
     */
    public $Ref;
    /**
     * Код отделения decimal[9999999999]
     *
     * @var int
     */
    public $SiteKey;
    /**
     * Название отделения на Украинском string[99]
     *
     * @var string
     */
    public $Description;
    /**
     * Название отделения на русском string[99]
     *
     * @var string
     */
    public $DescriptionRu;
    /**
     * Тип отделения string[36]
     *
     * @var string
     */
    public $TypeOfWarehouse;
    /**
     * Номер отделения int[99999]
     *
     * @var int
     */
    public $Number;
    /**
     * Идентификатор населенного пункта string[36]
     *
     * @var string
     */
    public $CityRef;
    /**
     * Название населенного пункта на Украинском string[50]
     *
     * @var string
     */
    public $CityDescription;
    /**
     * Название населенного пункта на русском string[50]
     *
     * @var string
     */
    public $CityDescriptionRu;
    /**
     * Долгота int[50]
     *
     * @var int
     */
    public $Longitude;
    /**
     * Широта int[50]
     *
     * @var int
     */
    public $Latitude;
    /**
     * (1/0) Наличие кассы Пост-Финанс int[1]
     *
     * @var int
     */
    public $PostFinance;
    /**
     * (1/0) Наличие пос-терминала на отделении int[1]
     *
     * @var int
     */
    public $POSTerminal;
    /**
     * (1/0) Возможность оформления Международного отправления int[1]
     *
     * @var int
     */
    public $InternationalShipping;
    /**
     * Максимальный вес отправления int[9999999999]
     *
     * @var int
     */
    public $TotalMaxWeightAllowed;
    /**
     * Максимальный вес одного места отправления int[9999999999]
     *
     * @var int
     */
    public $PlaceMaxWeightAllowed;
    /**
     * График приема отправлений array[7]
     *
     * @var array
     */
    public $Reception;
    /**
     * График отправки день в день array[7]
     *
     * @var array
     */
    public $Delivery;
    /**
     * График работы array[7]
     *
     * @var array
     */
    public $Schedule;

    /**
     * Type of Warehouse
     *
     * @var WarehouseType
     */
    public $type; 
}