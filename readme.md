Quick E-cash
===

[![Build Status](https://img.shields.io/travis/JoyMoe/E-cash.svg)](https://travis-ci.org/JoyMoe/E-cash)
[![Coverage Status](https://img.shields.io/codecov/c/github/JoyMoe/E-cash.svg)](https://codecov.io/github/JoyMoe/E-cash)
[![Releases Downloads](https://img.shields.io/github/downloads/JoyMoe/E-cash/total.svg)](https://github.com/JoyMoe/E-cash/releases)
[![Releases](https://img.shields.io/github/release/JoyMoe/E-cash.svg)](https://github.com/JoyMoe/E-cash/releases/latest)
[![Releases Downloads](https://img.shields.io/github/downloads/JoyMoe/E-cash/latest/total.svg)](https://github.com/JoyMoe/E-cash/releases/latest)

## API Reference

#### Common Paragrams

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|timestamp|int|false|
|nonce|string|false|
|sign|string|false|

##### timestamp

Unix timestamp

##### sign

RSA SHA256 signature

1. Sort all the parameters (exclude `text` and `array` paragrams) with keys and turn it into http_query string (no url-encoding).
1. Sign the string with private key by SHA256.
1. Encode the signature by BASE64.

#### POST `/api/orders`
Submit an order

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|merchandiser_id|int|false|
|trade_no|string|false|
|subject|string|false|
|amount|float|false|
|items|array|true|
|returnUrl|string|false|
|notifyUrl|string|false|

**Return**

Order object or error info

#### Redirect `/orders/:order_id`
Process an order

#### GET `/api/orders/:order_id`
Query an order

**Paragrams**

none

**Return**

Order object or error info

#### PUT `/api/orders/:order_id`
#### PATCH `/api/orders/:order_id`
Modify an order

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|subject|string|true|
|amount|float|true|
|items|array|true|

**Return**

Order object or error info

#### POST `/api/orders/:order_id`
Complete an order

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|trade_no|string|false|

**Return**

Order object or error info

#### DELETE `/api/orders/:order_id`
Delete an order

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|trade_no|string|false|

**Return**

Null or error info

## TODO
- [x] i18n
- [ ] Admin dashboard
- [x] Frontend redirecting
- [ ] Backenden notification
- [ ] Plugable gateway via plugins or extensions

## License
 The MIT License (MIT)

 More info see [LICENSE](LICENSE)
