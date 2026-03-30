<?php

namespace Novapay\Payment\SDK\Schema\Request;

use Novapay\Payment\SDK\Schema\Schema;
use Novapay\Payment\SDK\Schema\Callback;
use Novapay\Payment\SDK\Schema\Client;
use Novapay\Payment\SDK\Schema\Metadata;


class ExportOperationsGetRequest extends Request
{
    public $merchant_id;

    public $fromDate;
    public $toDate;
    public $format;
    public $email;

    public function __construct(
        $merchant_id,
        $fromData,
        $toDate,
        $format,
        $email
    ) {
        $this->merchant_id = $merchant_id;
        $this->fromDate  = $fromData;
        $this->toDate  = $toDate;
        $this->format  = $format;
        $this->email  = $email;
    }
}