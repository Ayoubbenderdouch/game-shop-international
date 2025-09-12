get ballence:

<?php

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://taxes.like4app.com/online/check_balance",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"deviceId\"\r\n\r\n\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"email\"\r\n\r\n\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"securityCode\"\r\n\r\n\r\n-----011000010111000001101001\r\nContent-Disposition: form-data; name=\"langId\"\r\n\r\n\r\n-----011000010111000001101001--\r\n\r\n",
  CURLOPT_HTTPHEADER => [
    "Content-Type: multipart/form-data; boundary=---011000010111000001101001"
  ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo $response;
}
response :

200:
{
  "response": 1,
  "userId": "191004",
  "balance": "574.88",
  "currency": "usd"
}

//get categories:
Operation to get all categories available for this merchant.

All categories should be cached and fetching check availabilty every 5 hours.
curl --request POST \                                                     ─╯
  --url https://taxes.like4app.com/online/categories \
  --header 'Content-Type: multipart/form-data' \
  --form deviceId=9591e2ed2f217b9fd888e6a566d0699bbcefdc2600c23bc4092eaa5fee6d7a3b \
  --form email=sami@reloadx.co \
  --form securityCode=9a06a1cc91e934aa3fbac3b38ddd1acef543c11e286d2ff9a53c4f7d6612e286 \
  --form langId=1
  response :
  
response
integer
1 for success, 0 for failure

Example:
1
data
array[object]
Array of objects, each object represents a category and every category have a property childs

id
string
Category Identifier

Example:
59
categoryParentId
string
Parent Category Identifier

Example:
0
categoryName
string
Category Name

Example:
iTunes
amazonImage
string
Category Image

Example:
https://likecard-space.fra1.digitaloceanspaces.com/categories/f33b9-it.png
childs
array[object]
An array of subcategories.
In case childs was empty that means this category doesn't have any subcategories, and the merchant can get products of this category immediately using its id.

id
string
Subcategory Identifier

Example:
151
categoryParentId
string
Parent Category Identifier

Example:
59
categoryName
string
Subcategory Name

Example:
British iTunes
amazonImage
string
Subcategory Image

Example:
https://likecard-space.fra1.digitaloceanspaces.com/categories/bb24a-bcd74-netherland.jpg
childs
array[object]

//products of particular cetegory like pubg uc for example:
Operation to get all products available either by a selected category id, or by an array of products identifiers.

Synchronization of products:
Every 30 minutes, products API should be called and all products should be cached to your side.
To get products of some category you can use categoryId in the request paramters.

To get products list known by their identifiers you can send the ids[] paramter in request which represent the product id you want, and it can be repeated to select many products at once.
curl --request POST \
  --url https://taxes.like4app.com/online/products \
  --header 'Content-Type: multipart/form-data' \
  --form deviceId= \
  --form email= \
  --form securityCode= \
  --form langId= \
  --form categoryId= \
  --form 'ids[]='
response 200:
response
integer
1 for success, 0 for failure

Example:
1
data
array[object]
array of objects, each object represents a product

productId
string
Product ID

Example:
693
categoryId
string
Category ID

Example:
267
productName
string
Product Name

Example:
mobilyTest
productPrice
number
Product Price including vat that the merchant paid for product

Example:
0.02
productImage
string
The price which the customer pays for the product to the merchant

Example:
https://likecard-space.fra1.digitaloceanspaces.com/products/066ce-x50.jpg
productCurrency
string
Product Currency

Example:
SAR
optionalFieldsExist
integer
1 optional fields required, 0 there are no required optional fields

Example:
1
productOptionalFields
array[object]
Array of optional fields

id
integer
Identifier of the optional field

Example:
332
required
string
'1' means it's required, '0' means it's optional and not required

Example:
1
defaultValue
string
Default Value

hint
string
Placeholder for this field

Example:
USER ID
label
string
Label displayed on top of this field on UI

Example:
USER ID
fieldTypeId
string
1 plaintext ,7 email address, 10 phone number,other number plaintext

Example:
10
fieldCode
string
Field Code

Example:
userid
optionalFieldID
string
Example:
14
options
array[object]
Array of choices in case option is multichoice field

sellPrice
string
Sell Price

Example:
0.02
available
boolean
Default:
true
vatPercentage
integer
Vat Percentage

Example:
0
//Operation to get all orders made by this merchant.

curl --request POST \
  --url https://taxes.like4app.com/online/orders \
  --header 'Content-Type: multipart/form-data' \
  --form deviceId= \
  --form email= \
  --form langId= \
  --form page= \
  --form orderType= \
  --form fromUnixTime= \
  --form toUnixTime=

  response:
  response
boolean
1 for success, 0 for failure

data
array[object]
Array of objects (each one represent an order)

orderNumber
string
Order Number

Example:
12637610
orderFinalTotal
string
The Price which merchant will pay for LikeCard for this order

Example:
1.05
currencySymbol
string
Currency Symbol

Example:
usd
orderCreateDate
string
Order Create Date

Example:
2020/01/06 12:07
orderCurrentStatus
string
Order Current Status

Example:
completed
orderPaymentMethod
string
Order Payment Method

Example:
Pocket
//Operation to get one order details by its id.

To get an order details you can use one of two order identifier, the first one is orderId from LikeCard, and the other is referenceId for order in merchant system, we recommend to use referenceId.
curl --request POST \
  --url https://taxes.like4app.com/online/orders/details \
  --header 'Content-Type: multipart/form-data' \
  --form deviceId= \
  --form email= \
  --form langId= \
  --form securityCode= \
  --form orderId= \
  --form referenceId=
  response:
  {
  "response": 1,
  "orderNumber": "12319604",
  "orderFinalTotal": "100",
  "currencySymbol": "SAR",
  "orderCreateDate": "2019-12-25 06:57",
  "orderPaymentMethod": "Pocket",
  "orderCurrentStatus": "completed",
  "serials": [
    {
      "productId": "376",
      "productName": "test-itunes1",
      "productImage": "https://likecard-space.fra1.digitaloceanspaces.com/products/4b09d-5656b-buy-one-get-one-2.png",
      "serialId": "11562121",
      "serialCode": "U0IycUdUWktsL25UaGhOc2JBMmtTUT09",
      "serialNumber": "",
      "validTo": "25/03/2020"
    }
  ]
}

formal example:
1 for success, 0 for failure

response
boolean
orderNumber
string
Order Number

Example:
12319604
orderFinalTotal
string
Example:
100
currencySymbol
string
Order Currency Symbol

Example:
SAR
orderCreateDate
string
Order Creation Date

Example:
2019-12-25 06:57
orderPaymentMethod
string
Order Payment Method

Example:
Pocket
orderCurrentStatus
string
Order Current Status

Example:
completed
serials
array[object]
Array of objects, each object represent a purchased product details.

productId
string
Product ID

Example:
376
productName
string
Product Name

Example:
test-itunes1
productImage
string
Product Image

Example:
https://likecard-space.fra1.digitaloceanspaces.com/products/4b09d-5656b-buy-one-get-one-2.png
serialId
string
serial ID

Example:
11562121
serialCode
string
The encrypted serial given to customer to be used.

Example:
U0IycUdUWktsL25UaGhOc2JBMmtTUT09
serialNumber
string
The card manufacturing No

validTo
string
The validation time for card

Example:
25/03/2020
deviceId*
:
string
email*
:
string
langId*
:
string
securityCode*
:
string
orderId
:
string

Omit orderId
referenceId
:
string

Omit referenceId
Send API Request
curl --request POST \
  --url https://taxes.like4app.com/online/orders/details \
  --header 'Content-Type: multipart/form-data' \
  --form deviceId= \
  --form email= \
  --form langId= \
  --form securityCode= \
  --form orderId= \
  --form referenceId=
{
  "response": 1,
  "orderNumber": "12319604",
  "orderFinalTotal": "100",
  "currencySymbol": "SAR",
  "orderCreateDate": "2019-12-25 06:57",
  "orderPaymentMethod": "Pocket",
  "orderCurrentStatus": "completed",
  "serials": [
    {
      "productId": "376",
      "productName": "test-itunes1",
      "productImage": "https://likecard-space.fra1.digitaloceanspaces.com/products/4b09d-5656b-buy-one-get-one-2.png",
      "serialId": "11562121",
      "serialCode": "U0IycUdUWktsL25UaGhOc2JBMmtTUT09",
      "serialNumber": "",
      "validTo": "25/03/2020"
    }
  ]
}
below we have a PHP function for decrypting serial codes using AES-256-CBC encryption.

// decrypting the `serialCode` in php

function decryptSerial($encrypted_txt){    
  $secret_key = '******';    
  $secret_iv = '******';
  $encrypt_method = 'AES-256-CBC';                
  $key = hash('sha256', $secret_key);        

  //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
  $iv = substr(hash('sha256', $secret_iv), 0, 16);        

  return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);        
}

echo decryptSerial('bnY0UEc2NFcySHgwRTIyNFU1NU5pUT09');  //output is MXeaSFSUj4az
//Operation to create order by this merchant.


Create order in two steps for the specific SKU before money deduction:
Check the product availability on checkout page before deducting money from user card. So, it's two API calls, first to check the availability for this SKU and if it's available deduct the money and create the order.

General Notes:

Clients are strictly advised not to sell on zero Price for any product.
Clients are strictly advised not to sell under the product cost price - provided via the API.
If for any reasons, the API parameters returned with Null values, the client advised to stop the transaction.
curl --request POST \
  --url https://taxes.like4app.com/online/create_order \
  --header 'Content-Type: multipart/form-data' \
  --form deviceId= \
  --form email= \
  --form securityCode= \
  --form langId= \
  --form productId= \
  --form referenceId= \
  --form time= \
  --form hash= \
  --form quantity= \
  --form optionalFields=

  Create Order Timeout Error & How to Solve
In case create order api return Timeout error Merchant have to implement the following advised solution to solve the timeout issue:

Merchant shall retry to send order details request for the same order for 6 times (10 seconds between each attempt) using the referenceId for created order.

If Merchant still get timeout, we advised to hold all market and navigate to the second step.

In the second step, Merchant shall stop all orders to LikeCard and continue attempting for he same order as health check every 60 seconds till LikeCard responds, at this moment merchant can resume send create orders.

below we have a PHP function for generating Hash

// Example to generate sha256 hash based on a given timestamp in php
function generateHash($time){
  $email = strtolower('merchant-email@domain.com');
  $phone = '**************';
  $key = '******';
  return hash('sha256',$time.$email.$phone.$key);
}

echo generateHash('1576704145');

OK below we have a PHP function for decrypting serial codes using AES-256-CBC encryption.

// decrypting the `serialCode` in php

function decryptSerial($encrypted_txt){    
  $secret_key = '******';    
  $secret_iv = '******';
  $encrypt_method = 'AES-256-CBC';                
  $key = hash('sha256', $secret_key);        

  //iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning          
  $iv = substr(hash('sha256', $secret_iv), 0, 16);        

  return openssl_decrypt(base64_decode($encrypted_txt), $encrypt_method, $key, 0, $iv);        
}

echo decryptSerial('bnY0UEc2NFcySHgwRTIyNFU1NU5pUT09');  //output is MXeaSFSUj4az

response
boolean
1 for success, 0 for failure

orderId
string
LikeCard order identifier

Example:
12319604
orderPrice
string
Order price with vat

Example:
100
orderPriceWithoutVat
string
order price without vat

Example:
100
vatAmount
string
Vat Amount

Example:
0
vatPercentage
string
Vat Percentage of order price

Example:
0%
serials
object
Array of objects, each object represent a purchased product details

serialCode
string
The encrypted serial given to customer to be used

Example:
11562121
serialNumber
string
The Card manufacturing No

Example:
U0IycUdUWktsL25UaGhOc2JBMmtTUT09
validTo
string
The validation time for card

Example:
25/03/2020
example:
{
  "response": 1,
  "orderId": "12319604",
  "orderPrice": "100",
  "orderPriceWithoutVat": "100",
  "orderDate": "2019-12-25 06:57",
  "vatAmount": "0",
  "vatPercentage": "0%",
  "productName": "test-itunes1",
  "productImage": "https://likecard-space.fra1.digitaloceanspaces.com/products/4b09d-5656b-buy-one-get-one-2.png",
  "serials": [
    {
      "serialId": "11562121",
      "serialCode": "U0IycUdUWktsL25UaGhOc2JBMmtTUT09",
      "serialNumber": "",
      "validTo": "25/03/2020"
    }
  ]
}
general response:
1020: Blocked IP
please contact your account manager
408:Request Timeout
