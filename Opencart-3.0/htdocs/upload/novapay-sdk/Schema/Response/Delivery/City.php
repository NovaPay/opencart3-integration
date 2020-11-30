<?php
/**
 * City schema class.
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
 * City schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46
 */
class City extends Schema
{
    /**
     * Код населенного пункта
     * 
     * @var string Int
     */
    public $CityID;
    /**
     * Идентификатор города
     * 
     * @var string Maximum 36 characters
     */
    public $Ref;
    /**
     * Город на Украинском языке
     * 
     * @var string
     */
    public $Description;
    /**
     * Город на русском языке
     * 
     * @var string
     */
    public $DescriptionRu;
    /**
     * Наличие доставки отправления в Понедельник
     * 
     * @var string
     */
    public $Delivery1;
    /**
     * Наличие доставки отправления во Вторник
     * 
     * @var string
     */
    public $Delivery2;
    /**
     * Наличие доставки отправления в Среду
     * 
     * @var string
     */
    public $Delivery3;
    /**
     * Наличие доставки отправления в Четверг
     * 
     * @var string
     */
    public $Delivery4;
    /**
     * Наличие доставки отправления в Пятницу
     * 
     * @var string
     */
    public $Delivery5;
    /**
     * Наличие доставки отправления в Субботу
     * 
     * @var string
     */
    public $Delivery6;
    /**
     * Наличие доставки отправления в Воскресенье
     * 
     * @var string
     */
    public $Delivery7;
    /**
     * Область
     * 
     * @var string Maximum 36 characters
     */
    public $Area;
    /**
     * Идентификатор (REF) типа населенного пункта
     * 
     * @var string Maximum 36 characters
     */
    public $SettlementType;
    /**
     * Конгломерат
     * 
     * @var null
     */
    public $Conglomerates;
    /**
     * Описание типа населенного пункта на Украинском языке
     * 
     * @var string Maximum 36 characters
     */
    public $SettlementTypeDescription;
    /**
     * Описание типа населенного пункта на Русском языке
     * 
     * @var string Maximum 36 characters
     */
    public $SettlementTypeDescriptionRu;

    public $IsBranch;
    public $PreventEntryNewStreetsUser;
}