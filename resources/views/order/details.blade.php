<!doctype html>
<html class="no-js">

<head>
    <meta charset="utf-8">
    <title>E-cash</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/css/main.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <h3 class="text-muted">E-cash</h3>
        </div>
        <div class="jumbotron" id="order">
            <h2>{{trans('message.order_details')}}</h2>
            <p class="lead"></p>
            <div class="panel panel-default">
                <div class="panel-heading">{{$order['subject']}}</div>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>{{trans('message.merchandiser')}}</th>
                            <td>{{$order['merchandiser']['name']}}</td>
                        </tr>
                        <tr>
                            <th>{{trans('message.order_id')}}</th>
                            <td>{{$order['id']}}</td>
                        </tr>
                        <tr>
                            <th>{{trans('message.amount')}}</th>
                            <td>{{$order['amount']}}</td>
                        </tr>
                        <tr>
                            <th>{{trans('message.status')}}</th>
                            <td>{{trans('message.status_' . $order['status'])}}</td>
                        </tr>
                    </tbody>
                </table>
                <div class="panel-footer" style="text-align:left;">
                    <ul>
                        @foreach ($order['items'] as $item)
                        <li>{{$item['name']}}<span class="pull-right">x {{$item['quantity']}}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @if ($order['status'] == 'pending')
            <form method="post" id="payment" role="form">
                <div class="btn-group btn-group-justified" data-toggle="buttons">
                    <label class="btn btn-primary active">
                        <input type="radio" name="gateway" value="alipay" id="alipay" autocomplete="off" checked> Alipay
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" name="gateway" value="unionpay" id="unionpay" autocomplete="off"> Unionpay
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" name="gateway" value="wechat" id="wechat" autocomplete="off"> Wechat
                    </label>
                    <label class="btn btn-primary">
                        <input type="radio" name="gateway" value="paypal" id="paypal" autocomplete="off"> Paypal
                    </label>
                </div>
                <button type="submit" class="btn btn-success btn-block">{{trans('message.pay_now')}}</button>
            </form>
            @else
            <a href="{{$order['returnUrl']}}?id={{$order['id']}}&trade_no={{$order['trade_no']}}" class="btn btn-default btn-block" role="button">{{trans('message.redirecting')}}</a>
            @endif
        </div>
        <div class="footer">
            <p>&hearts; {!! trans('message.hearts') !!}.</p>
            <p>&copy; <a href="https://7in0.me/">7IN0's Labs</a>.</p>
        </div>
    </div>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/payment.js"></script>
    @if ($order['status'] != 'pending')
    <script>
        setTimeout(function () {
            window.location = '{{$order['returnUrl']}}?id={{$order['id']}}&trade_no={{$order['trade_no']}}';
        }, 5000);
    </script>
    @endif
</body>

</html>
