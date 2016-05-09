<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class OrderController extends Controller
{
    private function xfee($amount, $percent, $addup = 0)
    {
        $amount = $amount / (1 - $percent) + $addup;

        return ceil($amount * 100) / 100;
    }

    private function xchange($value, $pair = 'CNYUSD')
    {
        $url = 'https://query.yahooapis.com/v1/public/yql?q='
        . urlencode('select * from yahoo.finance.xchange where pair = "'
            . $pair . '"&format=json&diagnostics=true&env=store://datatables.org/alltableswithkeys');

        $ch = curl_init();
        curl_setopt(CURLOPT_TIMEOUT, 30);
        curl_setopt(CURLOPT_URL, $url);
        curl_setopt(CURLOPT_RETURNTRANSFER, true);
        curl_setopt(CURLOPT_USERAGENT, 'xChange/0.9');
        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        $ask = $result->query->results->rate->Ask;

        $value = ceil($ask * $value * 100) / 100;

        return $value;
    }

    private function js($url)
    {
        echo '<script>window.location="' . $url . '";</script><a href="' . $url . '">Click</a>';
    }

    public function showOrder($order_id)
    {
        $order = Order::find($order_id);

        if ($order) {
            $order->items = unserialize($order->items);

            return view('order.details', ['order' => $order]);
        } else {
            return ['status' => '404', 'message' => 'Order not found'];
        }
    }

    public function doOrder(Request $request, $order_id)
    {
        $order   = Order::findOrFail($order_id);
        $gateway = $request->input('gateway');

        $returnUrl = route('back', [
            'order_id' => $order_id,
            'gateway'  => $gateway,
        ]);

        $order_id = str_pad($order_id, 8, '0', STR_PAD_LEFT);

        $amount      = $order->amount;
        $description = implode(',', $order->items);

        switch ($gateway) {
            case 'alipay':
                $omnipay = Omnipay::create('Alipay_Express');

                $params = [
                    'out_trade_no' => $order_id,
                    'returnUrl'    => $returnUrl,
                    'notifyUrl'    => $returnUrl,
                    'subject'      => $description,
                    'total_fee'    => $amount,
                ];

                $omnipay->setPartner(getenv('payment.alipay.partner'));
                $omnipay->setKey(getenv('payment.alipay.key'));
                $omnipay->setSellerEmail(getenv('payment.alipay.seller_email'));

                $response = $omnipay->purchase($params)->send();

                if ($response->isRedirect()) {
                    $order->gateway = $gateway;
                    $this->js($response->getRedirectUrl());
                } else {
                    exit(trans('message.faild_processing'));
                }
                break;
            case 'paypal':
                $omnipay = Omnipay::create('PayPal_Express');

                $amount = $this->xfee($this->xchange($amount), 0.044, 0.3);

                $params = [
                    'transactionId' => $order_id,
                    'cancelUrl'     => $returnUrl,
                    'returnUrl'     => $returnUrl,
                    'notifyUrl'     => $returnUrl,
                    'description'   => $description,
                    'amount'        => $amount,
                    'currency'      => 'USD',
                ];

                $omnipay->setUsername(getenv('payment.paypal.user'));
                $omnipay->setPassword(getenv('payment.paypal.pass'));
                $omnipay->setSignature(getenv('payment.paypal.sign'));
                $omnipay->setTestMode(getenv('payment.paypal.test'));

                $response = $omnipay->purchase($params)->send();

                $paypalResponse = $response->getData();

                if ($response->isRedirect()) {
                    $order->gateway = $gateway;
                    $this->js($response->getRedirectUrl());
                } else {
                    exit(trans('message.faild_processing'));
                }
                break;
            case 'unionpay':
                $omnipay = Omnipay::create('UnionPay_Express');

                $params = [
                    'orderId'   => $order_id,
                    'txnTime'   => date('YmdHis'),
                    'orderDesc' => $description,
                    'txnAmt'    => $amount * 100,
                    'returnUrl' => $returnUrl,
                    'notifyUrl' => $returnUrl,
                ];

                $omnipay->setMerId(getenv('payment.unionpay.partner'));
                $omnipay->setCertPath(getenv('payment.unionpay.cert'));
                $omnipay->setCertPassword(getenv('payment.unionpay.certpass'));

                $response = $omnipay->purchase($params)->send();

                if ($response->isRedirect()) {
                    $order->gateway = $gateway;
                    $response->redirect();
                } else {
                    exit(trans('message.faild_processing'));
                }
                break;
            case 'wechat':
                $omnipay = Omnipay::create('WeChat_Express');

                $params = [
                    'out_trade_no' => $order_id,
                    'notify_url'   => $returnUrl,
                    'body'         => $description,
                    'total_fee'    => $amount,
                    'fee_type'     => 'CNY',
                ];

                $omnipay->setAppId(getenv('payment.wechat.app_id'));
                $omnipay->setAppKey(getenv('payment.wechat.pay_sign_key'));
                $omnipay->setMchId(getenv('payment.wechat.partner'));

                $response = $omnipay->purchase($params)->send();

                if ($response->isRedirect()) {
                    $order->gateway = $gateway;
                    $qrCode         = new QrCode();
                    $image          = $qrCode
                        ->setText($response->getRedirectUrl())
                        ->setSize(120)
                        ->setPadding(0)
                        ->getDataUri();
                } else {
                    exit(trans('message.faild_processing'));
                }
                break;
            default:
                exit(trans('message.unsupported_gateway'));
        }

        $order->save();
    }

    public function doBack(Request $request, $order_id, $gateway)
    {
        $order = Order::findOrFail($order_id);
        $data  = $request->all();

        $order_id = str_pad($order_id, 8, '0', STR_PAD_LEFT);

        $amount = $order->amount;

        switch ($gateway) {
            case 'alipay':
                $omnipay = Omnipay::create('Alipay_Express');

                if ($request->isMethod('post')) {
                    $params = [
                        'request_params' => $_POST,
                    ];
                } else {
                    $params = [
                        'request_params' => $_GET,
                    ];
                }

                $omnipay->setPartner(getenv('payment.alipay.partner'));
                $omnipay->setKey(getenv('payment.alipay.key'));
                $omnipay->setSellerEmail(getenv('payment.alipay.seller_email'));

                $response = $omnipay->completePurchase($params)->send();

                if ($response->isSuccessful() && $response->isTradeStatusOk()) {
                    $responseData = $response->getData();
                    if ($order['status'] === 'pending') {
                        $order->gateway        = $gateway;
                        $order->transaction_id = $responseData['request_params']['trade_no'];
                        $order->received       = $responseData['request_params']['total_fee'];
                        $order->status         = 'processing';
                    }

                    if ($request->isMethod('post')) {
                        echo 'success';
                    }
                } else {
                    if ($request->isMethod('post')) {
                        exit('fail');
                    }
                }
                break;
            case 'paypal':
                $omnipay = Omnipay::create('PayPal_Express');

                $amount = $this->xfee($this->xchange($amount), 0.044, 0.3);

                $params = [
                    'transactionId' => $order['id'],
                    'amount'        => $amount,
                    'currency'      => 'USD',
                ];

                $omnipay->setUsername(getenv('payment.paypal.user'));
                $omnipay->setPassword(getenv('payment.paypal.pass'));
                $omnipay->setSignature(getenv('payment.paypal.sign'));
                $omnipay->setTestMode(getenv('payment.paypal.test'));

                $response = $omnipay->completePurchase($params)->send();

                $responseData = $response->getData();

                if ($response->isSuccessful() && $responseData['PAYMENTINFO_0_ACK'] === 'Success') {
                    if ($order['status'] === 'pending') {
                        $order->gateway        = $gateway;
                        $order->transaction_id = $responseData['PAYMENTINFO_0_TRANSACTIONID'];
                        $order->received       = $this->xchange(
                            $responseData['PAYMENTINFO_0_AMT'] - $responseData['PAYMENTINFO_0_FEEAMT'],
                            'USDCNY'
                        );
                        $order->status = 'processing';
                    }
                }
                break;
            case 'unionpay':
                $omnipay = Omnipay::create('UnionPay_Express');

                $params = [
                    'request_params' => $_POST,
                ];

                $omnipay->setMerId(getenv('payment.unionpay.partner'));
                $omnipay->setCertDir(getenv('payment.unionpay.certdir'));

                $response = $omnipay->completePurchase($params)->send();

                $responseData = $response->getData();

                if ($response->isSuccessful() && $responseData['respMsg'] === 'success') {
                    if ($order['status'] === 'pending') {
                        $order->gateway        = $gateway;
                        $order->transaction_id = $responseData['queryId'];
                        $order->received       = $responseData['settleAmt'] / 100;
                        $order->status         = 'processing';
                    }
                }
                break;
            case 'wechat':
                $omnipay = Omnipay::create('WeChat_Express');

                $params = [
                    'out_trade_no' => $order_id,
                ];

                $omnipay->setAppId(getenv('payment.wechat.app_id'));
                $omnipay->setAppKey(getenv('payment.wechat.pay_sign_key'));
                $omnipay->setMchId(getenv('payment.wechat.partner'));

                $response = $omnipay->completePurchase($params)->send();

                if ($response->isSuccessful() && $response->isTradeStatusOk()) {
                    $responseData = $response->getData();
                    if ($order['status'] === 'pending') {
                        $order->gateway        = $gateway;
                        $order->transaction_id = $responseData['transaction_id'];
                        $order->received       = $responseData['total_fee'];
                        $order->status         = 'processing';
                    }

                    if ($request->isMethod('post')) {
                        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>';
                    }
                } else {
                    if ($request->isMethod('post')) {
                        exit('<xml><return_code><![CDATA[FAIL]]></return_code></xml>');
                    }
                }
                break;
            default:
                exit(trans('message.unsupported_gateway'));
        }

        $order->save();

        if ($request->isMethod('post') && $gateway != 'unionpay') {
            exit();
        }

        return view('order.details', ['order' => $order]);
    }
}
