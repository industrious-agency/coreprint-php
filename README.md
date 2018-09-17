# Coreprint PHP

A PHP wrapper for the Coreprint API. The wrapper uses a dynamic `__call` method, so you should be able to call any of CorePrint's methods, some examples can be seen below.

## Getting started

### Install the package:

```bash
$ composer require industrious/coreprint-php
```

### Usage

##### Add to Basket

```php
$product_id = 1;

$basket = (new Industrious\CorePrint\CorePrint)
    ->addBasketEntry('PUT', [
        [
            'productid' => $product_id,
            'quantity' => 1,
            'itemurl' => 'https://placehold.it/100x100',
        ]
    ]);
```

##### Add an Address

```php
$address = (new Industrious\CorePrint\CorePrint)
    ->addAddress('PUT', [
        [
            'type' => 1,
            'label' => 'Delivery Address',
            'deliverto' => 'FULL_NAME',
            'streets' => (object) [
                'Address Line 1',
                'Address Line 2',
            ],
            'city' => 'City',
            'state' => 'County',
            'postcode' => 'Post Code',
            'country' => 'United Kingdom'
        ]
    ]);
```

##### Create an Order

```php
$basket_id = 1;
$invoice_address_id = 1;
$address_id = 1;

$order = (new Industrious\CorePrint\CorePrint)
    ->createOrder('PUT', [
        'entries' => (object) [$basket_id],
        'invoice' => $invoice_address_id,
        'delivery' => $address_id,
    ]);
```
