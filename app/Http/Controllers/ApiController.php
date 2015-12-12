***REMOVED***

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Merchandiser;
use App\Order;
use Illuminate\Http\Request;

class ApiController extends Controller
***REMOVED***
    private function sign($text, $private_key)
    ***REMOVED***
    ***REMOVED***

        openssl_sign($text, $signature, $private_key, OPENSSL_ALGO_SHA256***REMOVED***

    ***REMOVED***
    ***REMOVED***

    private function verify($data, $public_key)
    ***REMOVED***
        if (empty($data['sign'])) ***REMOVED***
            return false;
        ***REMOVED*** else ***REMOVED***
            $signature = $data['sign'];
        ***REMOVED***

        if (!empty($data['timestamp']) && time() - 60 <= $data['timestamp']) ***REMOVED***
        ***REMOVED***
            unset($data['sign']***REMOVED***

        ***REMOVED***
        ***REMOVED***

            try ***REMOVED***
                return openssl_verify(
                    http_build_query($data),
                    base64_decode($signature),
                    $public_key,
                    OPENSSL_ALGO_SHA256
                ***REMOVED***
            ***REMOVED*** catch (Exception $e) ***REMOVED***
                return false;
            ***REMOVED***
        ***REMOVED*** else ***REMOVED***
            return false;
        ***REMOVED***
    ***REMOVED***

    private function jsonOutput($data, $message = '', $status = 0)
    ***REMOVED***
        exit(json_encode([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ])***REMOVED***
    ***REMOVED***

    public function submitOrder(Request $request)
    ***REMOVED***
        $data = $request->all(***REMOVED***

        $merch = Merchandiser::findOrFail($data['merchandiser_id']***REMOVED***

        if ($this->verify($data, $merch['pubkey'])) ***REMOVED***
            $order = Order::where('trade_no', $data['trade_no'])->first(***REMOVED***
            if (empty($order)) ***REMOVED***
                if (parse_url($data['returnUrl'], PHP_URL_HOST) != $merch['domain'] ||
                    parse_url($data['notifyUrl'], PHP_URL_HOST) != $merch['domain']) ***REMOVED***
                    $this->jsonOutput(null, 'Your URL must belongs to domain "' . $merch['domain'] . '"', '400'***REMOVED***
                ***REMOVED***

                $order = new Order;

                $order->merchandiser_id = $data['merchandiser_id'];
                $order->trade_no = $data['trade_no'];
                $order->subject = $data['subject'];
                $order->amount = $data['amount'];
                $order->description = $data['description'];
                $order->returnUrl = $data['returnUrl'];
                $order->notifyUrl = $data['notifyUrl'];

                $order->save(***REMOVED***

                $this->jsonOutput($order***REMOVED***
            ***REMOVED*** else ***REMOVED***
                $this->jsonOutput(null, 'trade_no already exsits', '409'***REMOVED***
            ***REMOVED***
        ***REMOVED*** else ***REMOVED***
            $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403'***REMOVED***
        ***REMOVED***
    ***REMOVED***

    public function getOrder(Request $request, $id)
    ***REMOVED***
        $order = Order::findOrFail($id***REMOVED***

        $data = $request->all(***REMOVED***

        $merch = Merchandiser::findOrFail($order['merchandiser_id']***REMOVED***

        if ($this->verify($data, $merch['pubkey'])) ***REMOVED***
            $this->jsonOutput($order***REMOVED***
        ***REMOVED*** else ***REMOVED***
            $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403'***REMOVED***
        ***REMOVED***
    ***REMOVED***

    public function modifyOrder(Request $request, $id)
    ***REMOVED***
        $order = Order::findOrFail($id***REMOVED***

        $data = $request->all(***REMOVED***

        $merch = Merchandiser::findOrFail($order['merchandiser_id']***REMOVED***

        if ($this->verify($data, $merch['pubkey'])) ***REMOVED***
            if (!empty($data['subject'])) ***REMOVED***
                $order->subject = $data['subject'];
            ***REMOVED***

            if (!empty($data['amount'])) ***REMOVED***
                $order->amount = $data['amount'];
            ***REMOVED***

            if (!empty($data['description'])) ***REMOVED***
                $order->description = $data['description'];
            ***REMOVED***

            if (!empty($data['returnUrl']) && parse_url($data['returnUrl'], PHP_URL_HOST) === $merch['domain']) ***REMOVED***
                $order->returnUrl = $data['returnUrl'];
            ***REMOVED***

            if (!empty($data['notifyUrl']) && parse_url($data['notifyUrl'], PHP_URL_HOST) === $merch['domain']) ***REMOVED***
                $order->notifyUrl = $data['notifyUrl'];
            ***REMOVED***

            $order->save(***REMOVED***

            $this->jsonOutput($order***REMOVED***
        ***REMOVED*** else ***REMOVED***
            $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403'***REMOVED***
        ***REMOVED***
    ***REMOVED***

    public function completeOrder(Request $request, $id)
    ***REMOVED***
        $order = Order::findOrFail($id***REMOVED***

        $data = $request->all(***REMOVED***

        $merch = Merchandiser::findOrFail($order['merchandiser_id']***REMOVED***

        if (!empty($data['trade_no']) && $data['trade_no'] === $order->trade_no) ***REMOVED***
            if ($this->verify($data, $merch['pubkey'])) ***REMOVED***
                if ($order->status === 'processing') ***REMOVED***
                    $order->status = 'done';
                    $order->save(***REMOVED***
                ***REMOVED***

                $this->jsonOutput($order***REMOVED***
            ***REMOVED*** else ***REMOVED***
                $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403'***REMOVED***
            ***REMOVED***
        ***REMOVED*** else ***REMOVED***
            $this->jsonOutput(null, 'trade_no not matched', '404'***REMOVED***
        ***REMOVED***
    ***REMOVED***

    public function removeOrder(Request $request, $id)
    ***REMOVED***
        $order = Order::findOrFail($id***REMOVED***

        $data = $request->all(***REMOVED***

        $merch = Merchandiser::findOrFail($order['merchandiser_id']***REMOVED***

        if (!empty($data['trade_no']) && $data['trade_no'] === $order->trade_no) ***REMOVED***
            if ($this->verify($data, $merch['pubkey'])) ***REMOVED***
                if (in_array($order->status, ['refunded', 'cancelled'])) ***REMOVED***
                    $order->delete(***REMOVED***

                    $this->jsonOutput(null***REMOVED***
                ***REMOVED*** else ***REMOVED***
                    $this->jsonOutput(null, 'Cannot delete this order', '405'***REMOVED***
                ***REMOVED***
            ***REMOVED*** else ***REMOVED***
                $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403'***REMOVED***
            ***REMOVED***
        ***REMOVED*** else ***REMOVED***
            $this->jsonOutput(null, 'trade_no not matched', '404'***REMOVED***
        ***REMOVED***
    ***REMOVED***
***REMOVED***
