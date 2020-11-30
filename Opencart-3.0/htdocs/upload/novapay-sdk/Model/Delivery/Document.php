<?php
/**
 * Delivery Document model class.
 * Calculate cost, create and update documents.
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

namespace Novapay\Payment\SDK\Model\Delivery;

use Novapay\Payment\SDK\Model\Model;
use Novapay\Payment\SDK\Schema\Request\Delivery\CalculationGetRequest;
use Novapay\Payment\SDK\Schema\Response\Delivery\Response;
use Novapay\Payment\SDK\Schema\Response\Delivery\CalculationGetResponse;

/**
 * Delivery Document model class.
 * Calculate cost, create and update documents.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 * @link     https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef
 */
class Document extends Model
{
    /**
     * Код города отправителя string[36]
     *
     * @var string
     */
    public $CitySender;
    /**
     * Код города получателя string[36]
     *
     * @var string
     */
    public $CityRecipient;
    /**
     * Min - 0,1 Вес фактический int[36]
     *
     * @var int
     */
    public $Weight;
    /**
     * Тип услуги int[36]
     *
     * @var int
     */
    public $ServiceType;
    /**
     * Целое число, объявленная стоимость (если объявленная стоимость не указана, 
     * API автоматически подставит минимальную объявленную цену - 300.00 int[36]
     *
     * @var int
     */
    public $Cost;
    /**
     * Значение из справочника Тип груза: Cargo, Documents, TiresWheels, 
     * Pallet string[36]
     *
     * @var string
     */
    public $CargoType;
    /**
     * Целое число, количество мест отправления string[36]
     *
     * @var string
     */
    public $SeatsAmount;
    /**
     * Обратная доставка string[36]
     *
     * @var Redelivery {string CargoType, float Amount}
     */
    public $RedeliveryCalculate;
    /**
     * @var PackCalculate {int PackCount, string PackRef}
     */
    public $PackCalculate;
    /**
     * Целое число int[36]
     *
     * @var int
     */
    public $Amount;
    /**
     * Массив string[36]
     *
     * @var array of string[36]
     */
    public $CargoDetails = [];

    /**
     * Calculate shipment cost.
     * All data for calculation is used from $this.
     * 
     * @return bool        TRUE on success, FALSE on failure.
     */
    public function calc()
    {
        $request = new CalculationGetRequest(Model::getMerchantId(), $this);
        $response = $this->send($request, 'POST', '/delivery-info');
        $res = Response::create(
            $response[1],
            $response[0],
            CalculationGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof CalculationGetResponse) {
            return false;
        }

        return true;
    }
}
