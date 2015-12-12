***REMOVED***

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class OrderController extends Controller
***REMOVED***
    private function xfee($amount, $percent, $addup = 0)
    ***REMOVED***
        $amount = $amount / (1 - $percent) + $addup;

        return ceil($amount * 100) / 100;
    ***REMOVED***

    private function xchange($value, $pair = 'CNYUSD')
    ***REMOVED***
        $url = 'https://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20yahoo.finance.xchange%20where%20pair%20%3D%20%22' . $pair . '%22&format=json&diagnostics=true&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys';

        $ch = curl_init(***REMOVED***
        curl_setopt(CURLOPT_TIMEOUT, 30***REMOVED***
        curl_setopt(CURLOPT_URL, $url***REMOVED***
        curl_setopt(CURLOPT_RETURNTRANSFER, true***REMOVED***
        curl_setopt(CURLOPT_USERAGENT, 'xChange/0.9'***REMOVED***
        $result = json_decode(curl_exec($ch)***REMOVED***
        curl_close($ch***REMOVED***

        $ask = $result->query->results->rate->Ask;

        $value = ceil($ask * $value * 100) / 100;

        return $value;
    ***REMOVED***

    private function js($url)
    ***REMOVED***
        echo '<script>window.location="' . $url . '";</script><a href="' . $url . '">Click</a>';
    ***REMOVED***

    public function showOrder($order_id)
    ***REMOVED***
        return view('order.details', ['order' => Order::findOrFail($order_id)]***REMOVED***
    ***REMOVED***

    public function doOrder(Request $request, $order_id)
    ***REMOVED***
        $order = Order::findOrFail($order_id***REMOVED***
        $gateway = $request->input('gateway'***REMOVED***

        $returnUrl = route('back', [
            'order_id' => $order_id,
            'gateway' => $gateway,
        ]***REMOVED***

        $order_id = str_pad($order_id, 8, '0', STR_PAD_LEFT***REMOVED***

        $amount = $order->amount;

        switch ($gateway) ***REMOVED***
            case 'alipay':
                $omnipay = Omnipay::create('Alipay_Express'***REMOVED***

                $params = [
                    'out_trade_no' => $order_id,
                    'returnUrl' => $returnUrl,
                    'notifyUrl' => $returnUrl,
                    'subject' => $order->description,
                    'total_fee' => $amount,
                ];

                $omnipay->setPartner(getenv('payment.alipay.partner')***REMOVED***
                $omnipay->setKey(getenv('payment.alipay.key')***REMOVED***
                $omnipay->setSellerEmail(getenv('payment.alipay.seller_email')***REMOVED***

                $response = $omnipay->purchase($params)->send(***REMOVED***

                if ($response->isRedirect()) ***REMOVED***
                    $order->gateway = $gateway;
                    $this->js($response->getRedirectUrl()***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    exit('Faild when processing your order.'***REMOVED***
                ***REMOVED***
                break;
            case 'paypal':
                $omnipay = Omnipay::create('PayPal_Express'***REMOVED***

                $amount = $this->xfee($this->xchange($amount), 0.044, 0.3***REMOVED***

                $params = [
                    'transactionId' => $order_id,
                    'cancelUrl' => $returnUrl,
                    'returnUrl' => $returnUrl,
                    'notifyUrl' => $returnUrl,
                    'description' => $order->description,
                    'amount' => $amount,
                    'currency' => 'USD',
                ];

                $omnipay->setUsername(getenv('payment.paypal.user')***REMOVED***
                $omnipay->setPassword(getenv('payment.paypal.pass')***REMOVED***
                $omnipay->setSignature(getenv('payment.paypal.sign')***REMOVED***
                $omnipay->setTestMode(getenv('payment.paypal.test')***REMOVED***

                $response = $omnipay->purchase($params)->send(***REMOVED***

                $paypalResponse = $response->getData(***REMOVED***

                if ($response->isRedirect()) ***REMOVED***
                    $order->gateway = $gateway;
                    $this->js($response->getRedirectUrl()***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    exit('Faild when processing your order.'***REMOVED***
                ***REMOVED***
                break;
            case 'unionpay':
                $omnipay = Omnipay::create('UnionPay_Express'***REMOVED***

                $params = [
                    'orderId' => $order_id,
                    'txnTime' => date('YmdHis'),
                    'orderDesc' => $order->description,
                    'txnAmt' => $amount * 100,
                    'returnUrl' => $returnUrl,
                    'notifyUrl' => $returnUrl,
                ];

                $omnipay->setMerId(getenv('payment.unionpay.partner')***REMOVED***
                $omnipay->setCertPath(getenv('payment.unionpay.cert')***REMOVED***
                $omnipay->setCertPassword(getenv('payment.unionpay.certpass')***REMOVED***

                $response = $omnipay->purchase($params)->send(***REMOVED***

                if ($response->isRedirect()) ***REMOVED***
                    $order->gateway = $gateway;
                    $response->redirect(***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    exit('Faild when processing your order.'***REMOVED***
                ***REMOVED***
                break;
            case 'wechat':
                $omnipay = Omnipay::create('WeChat_Express'***REMOVED***

                $params = [
                    'out_trade_no' => $order_id,
                    'notify_url' => $returnUrl,
                    'body' => $order->description,
                    'total_fee' => $amount,
                    'fee_type' => 'CNY',
                ];

                $omnipay->setAppId(getenv('payment.wechat.app_id')***REMOVED***
                $omnipay->setAppKey(getenv('payment.wechat.pay_sign_key')***REMOVED***
                $omnipay->setMchId(getenv('payment.wechat.partner')***REMOVED***

                $response = $omnipay->purchase($params)->send(***REMOVED***

                if ($response->isRedirect()) ***REMOVED***
                    $order->gateway = $gateway;
                    $qrCode = new QrCode(***REMOVED***
                    $image = $qrCode
                        ->setText($response->getRedirectUrl())
                        ->setSize(120)
                        ->setPadding(0)
                        ->getDataUri(***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    exit('Faild when processing your order.'***REMOVED***
                ***REMOVED***
                break;
            default:
                exit('Unsupported gateway!'***REMOVED***
        ***REMOVED***

        $order->save(***REMOVED***
    ***REMOVED***

    public function doBack(Request $request, $order_id, $gateway)
    ***REMOVED***
        $order = Order::findOrFail($order_id***REMOVED***
        $data = $request->all(***REMOVED***

        $order_id = str_pad($order_id, 8, '0', STR_PAD_LEFT***REMOVED***

        $amount = $order->amount;

        switch ($gateway) ***REMOVED***
            case 'alipay':
                $omnipay = Omnipay::create('Alipay_Express'***REMOVED***

                if ($request->isMethod('post')) ***REMOVED***
                    $params = [
                        'request_params' => $_POST,
                    ];
                ***REMOVED*** else ***REMOVED***
                    $params = [
                        'request_params' => $_GET,
                    ];
                ***REMOVED***

                $omnipay->setPartner(getenv('payment.alipay.partner')***REMOVED***
                $omnipay->setKey(getenv('payment.alipay.key')***REMOVED***
                $omnipay->setSellerEmail(getenv('payment.alipay.seller_email')***REMOVED***

                $response = $omnipay->completePurchase($params)->send(***REMOVED***

                if ($response->isSuccessful() && $response->isTradeStatusOk()) ***REMOVED***
                    $responseData = $response->getData(***REMOVED***
                    if ($order['status'] === 'pending') ***REMOVED***
                        $order->gateway = $gateway;
                        $order->transaction_id = $responseData['request_params']['trade_no'];
                        $order->received = $responseData['request_params']['total_fee'];
                        $order->status = 'processing';
                    ***REMOVED***

                    if ($request->isMethod('post')) ***REMOVED***
                        echo 'success';
                    ***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    if ($request->isMethod('post')) ***REMOVED***
                        exit('fail'***REMOVED***
                    ***REMOVED***
                ***REMOVED***
                break;
            case 'paypal':
                $omnipay = Omnipay::create('PayPal_Express'***REMOVED***

                $amount = $this->xfee($this->xchange($amount), 0.044, 0.3***REMOVED***

                $params = [
                    'transactionId' => $order['id'],
                    'amount' => $amount,
                    'currency' => 'USD',
                ];

                $omnipay->setUsername(getenv('payment.paypal.user')***REMOVED***
                $omnipay->setPassword(getenv('payment.paypal.pass')***REMOVED***
                $omnipay->setSignature(getenv('payment.paypal.sign')***REMOVED***
                $omnipay->setTestMode(getenv('payment.paypal.test')***REMOVED***

                $response = $omnipay->completePurchase($params)->send(***REMOVED***

                $responseData = $response->getData(***REMOVED***

                if ($response->isSuccessful() && $responseData['PAYMENTINFO_0_ACK'] === 'Success') ***REMOVED***
                    if ($order['status'] === 'pending') ***REMOVED***
                        $order->gateway = $gateway;
                        $order->transaction_id = $responseData['PAYMENTINFO_0_TRANSACTIONID'];
                        $order->received = $this->xchange($responseData['PAYMENTINFO_0_AMT'] - $responseData['PAYMENTINFO_0_FEEAMT'], 'USDCNY'***REMOVED***
                        $order->status = 'processing';
                    ***REMOVED***
                ***REMOVED***
                break;
            case 'unionpay':
                $omnipay = Omnipay::create('UnionPay_Express'***REMOVED***

                $params = [
                    'request_params' => $_POST,
                ];

                $omnipay->setMerId(getenv('payment.unionpay.partner')***REMOVED***
                $omnipay->setCertDir(getenv('payment.unionpay.certdir')***REMOVED***

                $response = $omnipay->completePurchase($params)->send(***REMOVED***

                $responseData = $response->getData(***REMOVED***

                if ($response->isSuccessful() && $responseData['respMsg'] === 'success') ***REMOVED***
                    if ($order['status'] === 'pending') ***REMOVED***
                        $order->gateway = $gateway;
                        $order->transaction_id = $responseData['queryId'];
                        $order->received = $responseData['settleAmt'] / 100;
                        $order->status = 'processing';
                    ***REMOVED***
                ***REMOVED***
                break;
            case 'wechat':
                $omnipay = Omnipay::create('WeChat_Express'***REMOVED***

                $params = [
                    'out_trade_no' => $order_id,
                ];

                $omnipay->setAppId(getenv('payment.wechat.app_id')***REMOVED***
                $omnipay->setAppKey(getenv('payment.wechat.pay_sign_key')***REMOVED***
                $omnipay->setMchId(getenv('payment.wechat.partner')***REMOVED***

                $response = $omnipay->completePurchase($params)->send(***REMOVED***

                if ($response->isSuccessful() && $response->isTradeStatusOk()) ***REMOVED***
                    $responseData = $response->getData(***REMOVED***
                    if ($order['status'] === 'pending') ***REMOVED***
                        $order->gateway = $gateway;
                        $order->transaction_id = $responseData['transaction_id'];
                        $order->received = $responseData['total_fee'];
                        $order->status = 'processing';
                    ***REMOVED***

                    if ($request->isMethod('post')) ***REMOVED***
                        echo '<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>';
                    ***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    if ($request->isMethod('post')) ***REMOVED***
                        exit('<xml><return_code><![CDATA[FAIL]]></return_code></xml>'***REMOVED***
                    ***REMOVED***
                ***REMOVED***
                break;
            default:
                exit('Unsupported gateway!'***REMOVED***
        ***REMOVED***

        $order->save(***REMOVED***

        if ($request->isMethod('post') && $gateway != 'unionpay') ***REMOVED***
            exit(***REMOVED***
        ***REMOVED***

        return view('order.details', ['order' => $order]***REMOVED***
    ***REMOVED***
***REMOVED***
