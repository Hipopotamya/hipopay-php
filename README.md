### Hipopay - PHP Class

This is a PHP class for Hipopay payment gateway. It is a simple class that allows you to make payments using Hipopay payment gateway.

### Installation

You can install the class via download the class file and include it in your project.


### API Documents

https://dev.hipopotamya.com

### Commission Types

```md
- 1: Assume Dealer Commission.
- 2: Assume All Commission.
- 3: Reflect Commission Fully
```

## Usage

### Create Store Session
```php
require_once 'hipopay.php';

$hipopay = new HipopayIntegration();
$hipopay->setApiKey('your-api-key');
$hipopay->setApiSecret('your-api-secret');
$hipopay->setUserId(1); // Customer ID
$hipopay->setUserEmail('john@doe.com'); // Customer Email
$hipopay->setUsername('johndoe'); // Customer Username

$response = $hipopay->createStoreSession();
```

### Create Payment Session
```php
require_once 'hipopay.php';

$hipopay = new HipopayIntegration();
$hipopay->setApiKey('your-api-key');
$hipopay->setApiSecret('your-api-secret');
$hipopay->setUserId(1); // Customer ID
$hipopay->setUserEmail('john@doe.com'); // Customer Email
$hipopay->setUsername('johndoe'); // Customer Username
$hipopay->setProduct([
    'name' => 'Product Name', // Product Name
    'price' => 100, // Product Price
    'reference_id' => 'payment-123', // Payment Reference ID
    'commission_type' => 1, // Commission Type = 1, 2, 3
]);

$response = $hipopay->createPaymentSession();
```

### Response Example
```json
{
  "success": true,
  "message": "Successful",
  "data": {
    "token": "A86500SG5EY6796A3L60G1D615CDZJ585KD550NXFH5TD159",
    "payment_url": "https://www.hipopotamya.com/api/v1/merchants/products/A86500SG5EY6796A3L60G1D615CDZJ585KD550NXFH5TD159"
  }
}
```


### Check IPN Status is Payment Success
##### You should use this when you receive an ipn request. If this method returns true, the payment is successful.

#### Please see https://documenter.getpostman.com/view/11907402/UV5RkfWH#7df7766a-fa1a-4929-ba52-a4ef451b29c2

```php
require_once 'hipopay.php';

$hipopay = new HipopayIntegration();
$hipopay->setApiKey('your-api-key');
$hipopay->setApiSecret('your-api-secret');

$result = $hipopay->getIpnStatusIsSuccess(); // Return true or false

if($result) {
    // Payment is successful
} else {
    // Payment is not successful
}

echo "OK";
```