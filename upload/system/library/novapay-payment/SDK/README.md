## Authentication
This API use JSON format for receiving request bodies and passing responses.

This API use `RSA signatures` (passed in `x-sign` header) to verify the origin of request. Sign is generated on request body, passed in `base64` encoding and verified in the same way. Contact support with your RSA public key to obtain access to this API.

## Usage flow

#### Payments

- Payment session creation. Session lifetime is 30 days
- Manually expire created session if needed
- Adding payment to a session. It can use `secure payment` mechanism (using hold before package is delivered) or just general payment
- Confirming secure payment by a seller
- Voiding paid / holded payments

#### Payments via precoded frames

- Init payment

#### Informational endpoints

- get list of available cities for delivery
- get list of warehouses
- calculate delivery price based on package dimensions and cities

#### Postbacks

- after payment session changes its status postback is send to a client (server2server). Postback url is passed on session creation. Postback payload is described below

## Endpoints

Testing environment: `http://api-qecom.novapay.ua/v1`

Merchant_id: `1`

Merchant private key is located in `../../../keys/merchant.key.pem`

Server public key is located in `../../../keys/public.key.pem`

### Payments 

#### `POST /session`

Creates payment session

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| client_first_name | string | |
| client_last_name | string | |
| client_patronymic | string | |
| client_phone | string | phone in international format |
| callback_url | string | url for receiving session status postbacks (server-server) |
| metadata | object | any data one needs to be returned in postbacks |
| success_url | string | optional url for button "return to the shop" on payment status page |
| fail_url | string | optional url for button "return to the shop" on payment status page |

**successful response**

| field_name | type | description
|-|- |-
| id | string | unique payment session id
| metadata | object | ||

**example**

```
curl -X POST -H 'Content-type:application/json' -H 'x-sign: JUmrQmn92kwzsZG1zwTDtYW7RGBC/4ftrfl7e9ik6vksq9qK03ifZEXpQl8+uygAWSos5fAGUqSvqtHR/JxRicaaz9+cnKbwddkm1v/0IeyRcYU9oUI46ut2phaP1br40xlofgSvjbs1Z77x6wlNh2QPke/oHT/LfZKtCCjo/AOIVpPD+jQ14DhRSgepd20JYJHBTyAGkIO6oDeAjgtczcTyARJwYQn4vR9cpedfJx+hvwUD8RZgUYvlKU1ojNNjVmwojklfvxOJVD0G29tNh+I8M5K7qdh/+Ewo5zaLlbB5WLVppwBNIqRsE3PS1xAZyWqVt3tkO2iy6T3DZ08+vQ==' -d '{"merchant_id":1,"client_first_name":"Иванов","client_last_name":"Иван","client_patronymic":"Иванович","client_phone":"+380982850654","metadata":{"lol":"kek"},"callback_url":"http://test.com"}' http://api-qecom.novapay.ua/v1/session -v
Note: Unnecessary use of -X or --request, POST is already inferred.
*   Trying 10.18.16.101...
* TCP_NODELAY set
* Connected to api-qecom.novapay.ua (10.18.16.101) port 80 (#0)
> POST /v1/session HTTP/1.1
> Host: api-qecom.novapay.ua
> User-Agent: curl/7.61.0
> Accept: */*
> Content-type:application/json
> x-sign: JUmrQmn92kwzsZG1zwTDtYW7RGBC/4ftrfl7e9ik6vksq9qK03ifZEXpQl8+uygAWSos5fAGUqSvqtHR/JxRicaaz9+cnKbwddkm1v/0IeyRcYU9oUI46ut2phaP1br40xlofgSvjbs1Z77x6wlNh2QPke/oHT/LfZKtCCjo/AOIVpPD+jQ14DhRSgepd20JYJHBTyAGkIO6oDeAjgtczcTyARJwYQn4vR9cpedfJx+hvwUD8RZgUYvlKU1ojNNjVmwojklfvxOJVD0G29tNh+I8M5K7qdh/+Ewo5zaLlbB5WLVppwBNIqRsE3PS1xAZyWqVt3tkO2iy6T3DZ08+vQ==
> Content-Length: 210
>
* upload completely sent off: 210 out of 210 bytes
< HTTP/1.1 200 OK
< Content-Length: 70
< Content-Type: application/json; charset=utf-8
< Date: Fri, 14 Jun 2019 11:01:40 GMT
< Etag: W/"46-HVjkl/f/vngsaRaKqTRHZ4purpY"
< Strict-Transport-Security: max-age=15552000; includeSubDomains
< X-Content-Type-Options: nosniff
< X-Dns-Prefetch-Control: off
< X-Download-Options: noopen
< X-Frame-Options: SAMEORIGIN
< X-Sign: gzD5ICXRWsG/i2ctOIQUVnhYKUNKJiwgKS5IDyQD7X9JqOWwy9qwZPgZNHVSmVL7IibBocfMNvlaw4tNvnZLNHQ3hRSezEBHGb1RWqEsj6d8FlaQqR+NtGmjA02YvMH9MEIlsw6u1v7WrTB9RdIbkr08R9ISRSM5rkpEQOxGZq9AWNI6bxik9OHL2fM8+xUOy5C1xY/8/RvKUO6U6XNK7NVDPu8ZM3lqQCyUmcDxTFG2wjnp8wM9417I+STnsxskSEbQ2xxOl0QX8K2m3kH7pOGSRt6rEdY+ZRdDbER9xqOAQekGHpm0rvVlL16XzKa93i7w9HCbhUtLdHuhelR6fQ==
< X-Xss-Protection: 1; mode=block
<
* Connection #0 to host api-qecom.novapay.ua left intact
{"id":"28c72df1-6bcc-4fda-9119-d89928de74c5","metadata":{"lol":"kek"}}
```

#### `POST /expire`

Manually expire created session

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| session_id | string | payment session id

**successful response**

`empty`

#### `POST /payment`

Add payment to created session and optionaly initialize its processing

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| session_id | string | payment session id
| external_id | string | optional parameter indicating order id in merchant system (for registries)
| amount | number | |
| products | array | optional payment purpose description
| products[].description | string | payment position title
| products[].count | string | payment position count
| products[].price | string | payment position total price
| use_hold | boolean | optional parameter indicating two-steps payment (hold and then confirm). Default to false, always true if delivery params are used
| delivery | object | optional object holding data about delivered package
| delivery.volume_weight | number | minimum 0.0004
| delivery.weight | number | minimum 0.1
| delivery.recipient_city | string | ref id of recipient city (see `Informational endpoints`)
| delivery.recipient_warehouse | string | ref id of recipient warehouse (see `Informational endpoints`)

**successful response**

| field_name | type | description
|-|- |-
| url | string | url to redirect user to process payment (if start_process parameter is true)

#### `POST /void`

Void paid or holded payments (paid ones can be voided only till 23:59)

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| session_id | string | payment session id

**successful response**

`empty`. Postback with status update will be sent via postbacks (see `Postback schema`)

#### `POST /complete-hold`

Complete holded payments (created with `use_hold: true` parameter)

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| session_id | string | payment session id
| amount | number | optional parameter for hold partial completion

**successful response**

`empty`. Postback with status update will be sent via postbacks (see `Postback schema`)

#### `POST /confirm-delivery-hold`

Confirm holded secure delivery session by seller, results in express waybill number return

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| session_id | string | payment session id

**successful response**

| field_name | type | description
|-|- |-
| id | string | unique payment session id
| metadata | object | ||
| express_waybill | string |

#### `POST /get-status`

Return current session status

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| session_id | string | payment session id

**successful response**

| field_name | type | description
|-|- |-
| id | string | unique payment session id
| metadata | object | ||
| status | string | See `Session statuses`

### Payments via precoded frames

#### `POST /frames/init`

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| client_first_name | string | optional |
| client_last_name | string | optional |
| client_patronymic | string | optional |
| client_phone | string | optional phone in international format |
| callback_url | string | url for receiving session status postbacks (server-server) |
| metadata | object | any data one needs to be returned in postbacks |
| success_url | string | optional url for button "return to the shop" on payment status page |
| fail_url | string | optional url for button "return to the shop" on payment status page |
| external_id | string | optional parameter indicating order id in merchant system (for registries)
| amount | number | |
| products | array | optional payment purpose description
| products[].description | string | payment position title
| products[].count | string | payment position count
| products[].price | string | payment position total price
| delivery | object | optional object holding data about delivered package
| delivery.volume_weight | number | minimum 0.0004
| delivery.weight | number | minimum 0.01

**successful response**

| field_name | type | description
|-|- |-
| session_id | string | session id for tracking payment status
| url | string | url to redirect user to process payment

### Informational endpoints

#### `POST /delivery-info`

**request body**

| field_name | type | description
|-|- |-
| merchant_id | string | |
| modelName | string | novaposhta api model name
| calledMethod | string | novaposhta api method name|
| methodProperties | object | novaposhta api method properties


**list cities**

Detailed reference: https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d885da0fe4f08e8f7ce46

**list warehouses**

Detailed reference: https://devcenter.novaposhta.ua/docs/services/556d7ccaa0fe4f08e8f7ce43/operations/556d8211a0fe4f08e8f7ce45

**delivery price calculation**

Detailed reference: https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702ee2a0fe4f0cf4fc53ef

additional parameter `MarketPlaceSecurePayment: 1` is passed for calculate price with secure payment charge included

**example**

```
curl -X POST -H 'Content-type:application/json' -H 'x-sign: IZfzX4zQEniwANKACIeNaBe4eUiXaQBhZCi/eH9sb7IMtnOYn8DqSvIza5muRLHg7ocyncrxXI+ifV0fJP8M5Z6HOC2EEjaunH5LC7/JOLmVfFsFPPStmtDEHcMFjEICOE2ujNhS2g4BUWb4IygygNjn1LRfi6uib7Sj41S3LTI+kUo2MdBdnDhfBHZo7cbjEqfUoWX0d/7HUJg2UrboHNQClC+DW8Qo+sF3WRyiDqb6G2Tbu1iqlFJBXZStfmEncDb0fybIa0DkJG9g2eLM2LfEDolAZs6St7IlJWXDiSyh9ntyLwBaGWHyz1nH1A7LGL+Pj8QEdmRnpzYU6bx8yA==' -d '{"merchant_id":1,"modelName":"Address","calledMethod":"getCities","methodProperties":{"Ref":"ebc0eda9-93ec-11e3-b441-0050568002cf"}}' http://api-qecom.novapay.ua/v1/delivery-info -v
Note: Unnecessary use of -X or --request, POST is already inferred.
*   Trying 10.18.16.101...
* TCP_NODELAY set
* Connected to api-qecom.novapay.ua (10.18.16.101) port 80 (#0)
> POST /v1/delivery-info HTTP/1.1
> Host: api-qecom.novapay.ua
> User-Agent: curl/7.61.0
> Accept: */*
> Content-type:application/json
> x-sign: IZfzX4zQEniwANKACIeNaBe4eUiXaQBhZCi/eH9sb7IMtnOYn8DqSvIza5muRLHg7ocyncrxXI+ifV0fJP8M5Z6HOC2EEjaunH5LC7/JOLmVfFsFPPStmtDEHcMFjEICOE2ujNhS2g4BUWb4IygygNjn1LRfi6uib7Sj41S3LTI+kUo2MdBdnDhfBHZo7cbjEqfUoWX0d/7HUJg2UrboHNQClC+DW8Qo+sF3WRyiDqb6G2Tbu1iqlFJBXZStfmEncDb0fybIa0DkJG9g2eLM2LfEDolAZs6St7IlJWXDiSyh9ntyLwBaGWHyz1nH1A7LGL+Pj8QEdmRnpzYU6bx8yA==
> Content-Length: 132
>
* upload completely sent off: 132 out of 132 bytes
< HTTP/1.1 200 OK
< Content-Length: 671
< Content-Type: application/json; charset=utf-8
< Date: Fri, 14 Jun 2019 11:15:36 GMT
< Etag: W/"29f-HHAXTQJ7Iy5Xvlxpc4wjokmeBrw"
< Strict-Transport-Security: max-age=15552000; includeSubDomains
< X-Content-Type-Options: nosniff
< X-Dns-Prefetch-Control: off
< X-Download-Options: noopen
< X-Frame-Options: SAMEORIGIN
< X-Sign: TUeZJIdAlRvJCZAvxuTPROD1TM0db0c3dzaJhce7cNTPEeXOMoJt3MyaraMRDX4poMthn41UQRznQ1VLwEdFqLY2dyS1Z4OWk0YzHS4H7VTCvTe4HEjzObcYx0Fcmqkc+ouPobeKMlHEq1JYv/7R4DAP6graXEbwLpHDgKeF+/QBJnERVR/jkrwp+abu7Rv6jsTAuCmZIA7CA8ZSPbohfQnj4q2JVZwX0Vr3+hbfyAq2RlkFAarE7kRiyBqq+CXU6NYFS3KafcV/4Kl2Am7zbIDmhKWxyE/9AdC2Jr9uEE+AGOxkBP+c+g9b32+/sjlmZ1ksoPWcVo51PzDQXNddXA==
< X-Xss-Protection: 1; mode=block
<
* Connection #0 to host api-qecom.novapay.ua left intact
{"success":true,"data":[{"Description":"Агрономічне","DescriptionRu":"Агрономичное","Ref":"ebc0eda9-93ec-11e3-b441-0050568002cf","Delivery1":"1","Delivery2":"1","Delivery3":"1","Delivery4":"1","Delivery5":"1","Delivery6":"1","Delivery7":"0","Area":"71508129-9b87-11de-822f-000c2965ae0e","SettlementType":"563ced13-f210-11e3-8c4a-0050568002cf","IsBranch":"0","PreventEntryNewStreetsUser":null,"Conglomerates":null,"CityID":"890","SettlementTypeDescriptionRu":"село","SettlementTypeDescription":"село","SpecialCashCheck":1}],"errors":[],"warnings":[],"info":{"totalCount":1},"messageCodes":[],"errorCodes":[],"warningCodes":[],"infoCodes":[]}
```

## Postback schema

```
{
	id, --session id
	status, -- see Session statuses chapter
	metadata, --additional data passed to server on session creation
	client_first_name,
	client_last_name,
	client_patronymic,
	client_phone,
	external_id,
	delivery: { -- used if secure payment used
		recipient_city,
		recipient_warehouse
	},
	products, -- products array, passed to the system on operation adding
	delivery_amount, -- delivery price
	delivery_status_code, -- used if secure payment used. Statuses is taken from https://devcenter.novaposhta.ua/docs/services/556eef34a0fe4f02049c664e/operations/55702cbba0fe4f0cf4fc53ee
	delivery_status_text
}

```

## Session Statuses

| status | description
|-|-
| created | created session
| expired | session expired, no further actions available
| processing | session is processing, payer is entering his payment data
| holded | session amount is holded on payer account
| hold_confirmed | hold is confirmed by seller for secure payment
| processing_hold_completion | hold completition is in process
| paid | session is fully paid
| failed | session payment failed
| processing_void | session amount voiding is in process
| voided | sesion payment voided

## Errors

Errors are divided into to sections `validation` and `processing`.

### Validation Errors

**HTTP status** - `400`

**Structure**

```
{
	type: 'validation',
	errors - array of json schema validation errors with dataPath and message properties
}
```

### Processing Errors

**HTTP status** - `400`

**Structure**

```
{
	type: 'processing',
	error
}
```

#### Available error messages
| code | ua localized name |
| - | -
| request sign is invalid | невiрний пiдпис запиту
| session not found | сесiя не знайдена
| delivery price calculation failed | помилка при розрахунку вартостi доставки