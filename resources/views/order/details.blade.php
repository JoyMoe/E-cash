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
            <h2>Order details</h2>
            <p class="lead"></p>
            <div class="panel panel-default">
                <div class="panel-heading">***REMOVED******REMOVED***$order['subject']***REMOVED******REMOVED***</div>
                <table class="table table-hover">
                    <tbody>
                        <tr>
                            <th>Merchandiser</th>
                            <td>***REMOVED******REMOVED***$order['merchandiser']['name']***REMOVED******REMOVED***</td>
                        </tr>
                        <tr>
                            <th>Order ID</th>
                            <td>***REMOVED******REMOVED***$order['id']***REMOVED******REMOVED***</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>***REMOVED******REMOVED***$order['amount']***REMOVED******REMOVED***</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>***REMOVED******REMOVED***$order['status']***REMOVED******REMOVED***</td>
                        </tr>
                    </tbody>
                </table>
                <div class="panel-footer" style="text-align:left;">***REMOVED******REMOVED***$order['description']***REMOVED******REMOVED***</div>
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
                <button type="submit" class="btn btn-success btn-block">Pay now</button>
            </form>
            @else
            <a href="***REMOVED******REMOVED***$order['returnUrl']***REMOVED******REMOVED***?id=***REMOVED******REMOVED***$order['id']***REMOVED******REMOVED***&trade_no=***REMOVED******REMOVED***$order['trade_no']***REMOVED******REMOVED***" class="btn btn-default btn-block" role="button">Redirecting...</a>
            @endif
        </div>
        <div class="footer">
            <p>&hearts; <a href="https://github.com/labs7in0/E-cash">E-cash</a> is a payment gateway system developed by <a href="https://7in0.me/">7IN0's Labs</a>, it provides a serial APIs upon <a href="https://github.com/thephpleague/omnipay">omnipay</a>.</p>
            <p>&copy; <a href="https://7in0.me/">7IN0's Labs</a>.</p>
        </div>
    </div>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/payment.js"></script>
    @if ($order['status'] != 'pending')
    <script>
        setTimeout(function () ***REMOVED***
            window.location = '***REMOVED******REMOVED***$order['returnUrl']***REMOVED******REMOVED***?id=***REMOVED******REMOVED***$order['id']***REMOVED******REMOVED***&trade_no=***REMOVED******REMOVED***$order['trade_no']***REMOVED******REMOVED***';
        ***REMOVED***, 5000***REMOVED***
    </script>
    @endif
</body>

</html>
