Quick E-cash
===

[![Build Status](https://img.shields.io/travis/labs7in0/E-cash.svg)](https://travis-ci.org/labs7in0/E-cash)
[![Coverage Status](https://img.shields.io/codecov/c/github/labs7in0/E-cash.svg)](https://codecov.io/github/labs7in0/E-cash)
[![Releases Downloads](https://img.shields.io/github/downloads/labs7in0/E-cash/total.svg)](https://github.com/labs7in0/E-cash/releases)
[![Releases](https://img.shields.io/github/release/labs7in0/E-cash.svg)](https://github.com/labs7in0/E-cash/releases/latest)
[![Releases Downloads](https://img.shields.io/github/downloads/labs7in0/E-cash/latest/total.svg)](https://github.com/labs7in0/E-cash/releases/latest)

## API Reference

#### Common Paragrams

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|timestamp|int|false|
|sign|string|false|

##### timestamp

Unix timestamp

##### sign

RSA SHA256 signature

Sort data array (exclude all `text` paragrams) with keys and turn it into http_query string

#### POST `/api/order`
Submit an order to E-cash

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|merchandiser_id|int|false|
|trade_no|string|false|
|subject|string|false|
|amount|float|false|
|description|text|true|
|returnUrl|string|false|
|notifyUrl|string|false|

**Return**

Order object or error info

#### Redirect `/order/:order_id`
Process an order

#### GET `/api/order/:order_id`
Query an order

**Paragrams**

none

**Return**

Order object or error info

#### PUT `/api/order/:order_id`
Modify an order

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|subject|string|true|
|amount|float|true|
|description|text|true|
|returnUrl|string|true|
|notifyUrl|string|true|

**Return**

Order object or error info

#### POST `/api/order/:order_id`
Complete an order

**Paragrams**

| name | type | optional |
|:----:|:----:|:--------:|
|trade_no|string|false|

**Return**

Order object or error info

#### DELETE `/api/order/:order_id`
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

## Donate us

[Donate us](https://7in0.me/#donate)

## License
 The MIT License (MIT)

 More info see [LICENSE](LICENSE)
