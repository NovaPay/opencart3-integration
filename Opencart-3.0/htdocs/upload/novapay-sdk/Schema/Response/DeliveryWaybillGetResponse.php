<?php
/**
 * Delivery waybill GET response schema class.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema\Response;

/**
 * Delivery waybill GET response schema class.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class DeliveryWaybillGetResponse extends Response
{
    /**
     * {@inheritDoc}
     */
    public function jsonSerialize()
    {
        $disposition = $this->getHeader('Content-Disposition');
        return [
            'fileName'      => $disposition ? $disposition->getOption('Filename') : null,
            'fileSize'      => strlen($this->getBody()),
            'contentLength' => (int) $this->getHeader('Content-Length', true),
            'mimeType'      => $this->getHeader('Content-Type', true)
        ];
    }
}