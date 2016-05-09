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

Sort data array (exclude all `text` and `array` paragrams) with keys and turn it into http_query string

#### POST `/api/order`
Submit or modify an order

**Paragrams**

| name | type | optional | can modified |
|:----:|:----:|:--------:|:------------:|
|merchandiser_id|int|false|false|
|trade_no|string|false|false|
|subject|string|false|true|
|amount|float|false|true|
|items|array|true|true|
|returnUrl|string|false|false|
|notifyUrl|string|false|false|

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
