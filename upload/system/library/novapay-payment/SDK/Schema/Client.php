<?php
/**
 * Client schema class.
 * The client structure used in payment and session methods.
 * 
 * PHP version 7.X
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */

namespace Novapay\Payment\SDK\Schema;

/**
 * Client schema class.
 * The client structure used in payment and session methods.
 * 
 * @category SDK
 * @package  NovaPay
 * @author   NovaPay <acquiring@novapay.ua>
 * @license  https://github.com/NovaPay/prestashop-integration/blob/master/LICENSE MIT
 * @link     https://novapay.ua/
 */
class Client extends Schema
{
    /**
     * The first name of the Client.
     * 
     * @var string $first_name The first name.
     */
    public $first_name;

    /**
     * The last name of the Client.
     * 
     * @var string $last_name The last name.
     */
    public $last_name;

    /**
     * The middle name of the Client.
     * 
     * @var string $middle_name The middle name (patronymic).
     */
    public $middle_name;

    /**
     * The phone number of the Client.
     * 
     * @var string $phone The phone number.
     */
    public $client_phone;

    /**
     * The email address of the Client.
     *
     * @var string $email The email address.
     */
    public $client_email;

    /**
     * The Client class constructor.
     * 
     * @param mixed $first_name  First name.
     * @param mixed $last_name   Last name.
     * @param mixed $middle_name Middle name (patronymic).
     * @param mixed $client_phone       Phone number.
     * @param mixed $email       Email address.
     */
    public function __construct(
        $first_name = '',
        $last_name = '',
        $middle_name = '',
        $client_phone = '',
        $client_email = ''
    ) {
        $this->first_name  = $first_name;
        $this->last_name   = $last_name;
        $this->middle_name = $middle_name;
        $this->client_phone       = '+' . preg_replace('/[^0-9]/', '', $client_phone);
        $this->client_email       = $client_email;
    }
}