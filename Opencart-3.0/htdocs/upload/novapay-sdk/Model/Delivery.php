<?php
/**
 * Delivery model class.
 * Processes delivery methods through API.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Model;

use Novapay\Payment\SDK\Schema\Delivery as DeliverySchema;
use Novapay\Payment\SDK\Schema\Response\Response;
use Novapay\Payment\SDK\Schema\Request\DeliveryConfirmPostRequest;
use Novapay\Payment\SDK\Schema\Request\DeliveryPriceGetRequest;
use Novapay\Payment\SDK\Schema\Request\DeliveryWaybillGetRequest;
use Novapay\Payment\SDK\Schema\Response\DeliveryConfirmPostResponse;
use Novapay\Payment\SDK\Schema\Response\DeliveryPriceGetResponse;
use Novapay\Payment\SDK\Schema\Response\DeliveryWaybillGetResponse;

/**
 * Delivery model class.
 * Processes delivery methods through API.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Delivery extends Model
{
    const UNIT_MILLIMETER = 'mm';
    const UNIT_CENTIMETER = 'cm';
    const UNIT_METER      = 'm';

    const UNIT_INCH       = 'in';
    const UNIT_FOOT       = 'ft';

    const UNIT_GRAM     = 'g';
    const UNIT_KILOGRAM = 'kg';

    const UNIT_POUND    = 'lbs';
    const UNIT_OUNCE    = 'oz';

    public $price;
    public $metadata;
    /**
     * Express waybill number.
     *
     * @var long
     */
    public $express_waybill;
    /**
     * @var string
     */
    public $ref_id;
    /**
     * Content of the PDF document
     *
     * @var binary
     */
    public $pdfContent;

    /**
     * Calculates the price of the delivery.
     * 
     * @param float  $total            Package price (total amount).
     * @param float  $volumetricWeight Volume of the package W*H*D.
     * @param float  $weight           Weight of the package.
     * @param string $city             City reference ID.
     * @param string $warehouse        Warehouse reference ID.
     * 
     * @return bool                    TRUE on success, FALSE on failure.
     */
    public function price($total, $volumetricWeight, $weight, $city, $warehouse)
    {
        $info = new DeliverySchema($weight, $volumetricWeight, $city, $warehouse);
        $request = new DeliveryPriceGetRequest(
            Model::getMerchantId(),
            $total,
            $info->volume_weight,
            $info->weight,
            $info->recipient_city,
            $info->recipient_warehouse
        );

        $response = $this->send($request, 'POST', '/delivery-price');
        $res = Response::create(
            $response[1], 
            $response[0], 
            DeliveryPriceGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof DeliveryPriceGetResponse) {
            return false;
        }

        $this->price = $res->delivery_price;

        return true;
    }

    /**
     * Confirms delivery hold.
     * 
     * @param string $session_id Session ID.
     * 
     * @return bool              TRUE on success, FALSE on failure.
     */
    public function confirm($session_id)
    {
        $request = new DeliveryConfirmPostRequest(
            Model::getMerchantId(), 
            $session_id
        );

        $response = $this->send($request, 'POST', '/confirm-delivery-hold');
        $res = Response::create(
            $response[1], 
            $response[0], 
            DeliveryConfirmPostResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof DeliveryConfirmPostResponse) {
            return false;
        }

        $this->metadata        = $res->metadata;
        $this->express_waybill = $res->express_waybill;
        $this->ref_id          = $res->ref_id;

        return true;
    }

    /**
     * Returns PDF file content of the printed waybill.
     * 
     * @param string $session_id Session ID.
     * 
     * @return bool              TRUE on success, FALSE on failure.
     */
    public function waybill($session_id)
    {
        $request = new DeliveryWaybillGetRequest(
            Model::getMerchantId(), 
            $session_id
        );

        $response = $this->send($request, 'POST', '/print-express-waybill');
        $res = Response::create(
            $response[1], 
            $response[0], 
            DeliveryWaybillGetResponse::class
        );
        $this->setResponse($res);

        if (!$res instanceof DeliveryWaybillGetResponse) {
            return false;
        }

        $this->pdfContent = $res->getBody();

        return true;
    }

    /**
     * Calculates volumetrict weight for the API, in fact it is just a volume.
     * 
     * @param float  $width  Width value.
     * @param float  $height Height value.
     * @param float  $depth  Depth value.
     * @param string $unit   The unit of values.
     * 
     * @return float        
     */
    public function calcVolumetricWeight($width, $height, $depth, $unit = self::UNIT_METER)
    {
        return static::convertFrom($width, $unit) 
                * static::convertFrom($height, $unit) 
                * static::convertFrom($depth, $unit);
    }

    /**
     * Saves the PDF invoice generatd by Novapay API.
     *
     * @param string $file Path to the PDF file.
     * 
     * @return bool        TRUE on success, FALSE on failure.
     */
    public function savePdf($file)
    {
        if (!$this->pdfContent) {
            return false;
        }
        return file_put_contents($file, $this->pdfContent) > 0;
    }

    public static function convertFrom($value, $unit)
    {
        $target = static::UNIT_METER;
        return $value;
    }
}
