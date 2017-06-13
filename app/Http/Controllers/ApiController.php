<?php

namespace App\Http\Controllers;

use App\Merchandiser;
use App\Order;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private function verify($data, $public_key)
    {
        if (empty($data['sign'])) {
            return false;
        } else {
            $signature = $data['sign'];
        }

        if (empty($data['nonce'])) {
            return false;
        }

        if (!empty($data['timestamp']) && time() - 60 <= $data['timestamp']) {
            unset($data['items']);
            unset($data['sign']);

            reset($data);
            ksort($data);

            try {
                return openssl_verify(
                    urldecode(http_build_query($data)),
                    base64_decode($signature),
                    $public_key,
                    OPENSSL_ALGO_SHA256
                );
            } catch (Exception $e) {
                return false;
            }
        } else {
            return false;
        }
    }

    private function jsonFormat($data, $message = '', $status = 0)
    {
        return (json_encode([
            'data'    => $data,
            'message' => $message,
            'status'  => $status,
        ]));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'merchandiser_id' => 'required|integer',
            'trade_no'        => 'required|max:255',
            'subject'         => 'required|max:255',
            'amount'          => 'required|numeric',
            'returnUrl'       => 'required|url',
            'notifyUrl'       => 'required|url',
            'timestamp'       => 'required',
            'nonce'           => 'required',
            'sign'            => 'required',
            'items'           => 'array',
        ]);

        $merch = Merchandiser::where('status', 'alive')->findOrFail($request->input('merchandiser_id'));

        if ($this->verify($request->all(), $merch->pubkey)) {
            $order = Order::where('merchandiser_id', $merch->id)->where('trade_no', $request->input('trade_no'))->first();
            if (empty($order)) {
                if (parse_url($request->input('returnUrl'), PHP_URL_HOST) != $merch->domain ||
                    parse_url($request->input('notifyUrl'), PHP_URL_HOST) != $merch->domain) {
                    return $this->jsonFormat(null, 'Your URL must belongs to domain "' . $merch->domain . '"', '400');
                }

                $order = Order::create([
                    'merchandiser_id' => $request->input('merchandiser_id'),
                    'trade_no'        => $request->input('trade_no'),
                    'subject'         => $request->input('subject'),
                    'amount'          => $request->input('amount'),
                    'items'           => serialize($request->input('items')),
                    'returnUrl'       => $request->input('returnUrl'),
                    'notifyUrl'       => $request->input('notifyUrl'),
                ]);

                $order->items = unserialize($order->items);

                return $this->jsonFormat($order);
            } else {
                return $this->jsonFormat(null, 'trade_no already exsits', '409');
            }
        } else {
            return $this->jsonFormat(null, 'Signature Invalid or timestamp expired', '403');
        }
    }

    public function show(Request $request, $id)
    {
        $this->validate($request, [
            'timestamp' => 'required',
            'nonce'     => 'required',
            'sign'      => 'required',
        ]);

        $order = Order::findOrFail($id);
        $merch = Merchandiser::findOrFail($order->merchandiser_id);

        if ($this->verify($request->all(), $merch->pubkey)) {
            return $this->jsonFormat($order);
        } else {
            return $this->jsonFormat(null, 'Signature Invalid or timestamp expired', '403');
        }
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'subject'   => 'max:255',
            'amount'    => 'numeric',
            'timestamp' => 'required',
            'nonce'     => 'required',
            'sign'      => 'required',
            'items'     => 'array',
        ]);

        $order = Order::findOrFail($id);
        $merch = Merchandiser::where('status', 'alive')->findOrFail($order->merchandiser_id);

        if ($this->verify($request->all(), $merch->pubkey)) {
            if ($order->status == 'pending') {
                if ($request->has('subject')) {
                    $order->subject = $request->input('subject');
                }
                if ($request->has('amount')) {
                    $order->amount = $request->input('amount');
                }
                if ($request->has('items')) {
                    $order->items = serialize($request->input('items'));
                }
                
                $order->save();

                $order->items = unserialize($order->items);

                return $this->jsonFormat($order);
            }
        } else {
            return $this->jsonFormat(null, 'Signature Invalid or timestamp expired', '403');
        }
    }

    public function complete(Request $request, $id)
    {
        $this->validate($request, [
            'trade_no'  => 'required|exists:orders,trade_no',
            'timestamp' => 'required',
            'nonce'     => 'required',
            'sign'      => 'required',
        ]);

        $order = Order::findOrFail($id);
        $merch = Merchandiser::findOrFail($order->merchandiser_id);

        if ($request->input('trade_no') === $order->trade_no) {
            if ($this->verify($request->all(), $merch->pubkey)) {
                if ($order->status === 'processing') {
                    $order->status = 'done';
                    $order->save();
                }

                return $this->jsonFormat($order);
            } else {
                return $this->jsonFormat(null, 'Signature Invalid or timestamp expired', '403');
            }
        } else {
            return $this->jsonFormat(null, 'trade_no not matched', '404');
        }
    }

    public function destroy(Request $request, $id)
    {
        $this->validate($request, [
            'trade_no'  => 'required|exists:orders,trade_no',
            'timestamp' => 'required',
            'nonce'     => 'required',
            'sign'      => 'required',
        ]);

        $order = Order::findOrFail($id);
        $merch = Merchandiser::findOrFail($order->merchandiser_id);

        if ($request->input('trade_no') === $order->trade_no) {
            if ($this->verify($request->all(), $merch->pubkey)) {
                if (in_array($order->status, ['refunded', 'cancelled'])) {
                    $order->delete();

                    return $this->jsonFormat(null);
                } else {
                    return $this->jsonFormat(null, 'Cannot delete this order', '405');
                }
            } else {
                return $this->jsonFormat(null, 'Signature Invalid or timestamp expired', '403');
            }
        } else {
            return $this->jsonFormat(null, 'trade_no not matched', '404');
        }
    }
}
