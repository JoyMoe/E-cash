<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Merchandiser;
use App\Order;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    private function sign($text, $private_key)
    {
        $signature = '';

        openssl_sign($text, $signature, $private_key, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    private function verify($data, $public_key)
    {
        if (empty($data['sign'])) {
            return false;
        } else {
            $signature = $data['sign'];
        }

        if (!empty($data['timestamp']) && time() - 60 <= $data['timestamp']) {
            unset($data['description']);
            unset($data['sign']);

            reset($data);
            ksort($data);

            try {
                return openssl_verify(
                    http_build_query($data),
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

    private function jsonOutput($data, $message = '', $status = 0)
    {
        exit(json_encode([
            'data' => $data,
            'message' => $message,
            'status' => $status,
        ]));
    }

    public function submitOrder(Request $request)
    {
        $data = $request->all();

        $merch = Merchandiser::findOrFail($data['merchandiser_id']);

        if ($this->verify($data, $merch['pubkey'])) {
            $order = Order::where('trade_no', $data['trade_no'])->first();
            if (empty($order)) {
                if (parse_url($data['returnUrl'], PHP_URL_HOST) != $merch['domain'] ||
                    parse_url($data['notifyUrl'], PHP_URL_HOST) != $merch['domain']) {
                    $this->jsonOutput(null, 'Your URL must belongs to domain "' . $merch['domain'] . '"', '400');
                }

                $order = new Order;

                $order->merchandiser_id = $data['merchandiser_id'];
                $order->trade_no = $data['trade_no'];
                $order->subject = $data['subject'];
                $order->amount = $data['amount'];
                $order->description = $data['description'];
                $order->returnUrl = $data['returnUrl'];
                $order->notifyUrl = $data['notifyUrl'];

                $order->save();

                $this->jsonOutput($order);
            } else {
                $this->jsonOutput(null, 'trade_no already exsits', '409');
            }
        } else {
            $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403');
        }
    }

    public function getOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->all();

        $merch = Merchandiser::findOrFail($order['merchandiser_id']);

        if ($this->verify($data, $merch['pubkey'])) {
            $this->jsonOutput($order);
        } else {
            $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403');
        }
    }

    public function modifyOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->all();

        $merch = Merchandiser::findOrFail($order['merchandiser_id']);

        if ($this->verify($data, $merch['pubkey'])) {
            if (!empty($data['subject'])) {
                $order->subject = $data['subject'];
            }

            if (!empty($data['amount'])) {
                $order->amount = $data['amount'];
            }

            if (!empty($data['description'])) {
                $order->description = $data['description'];
            }

            if (!empty($data['returnUrl']) && parse_url($data['returnUrl'], PHP_URL_HOST) === $merch['domain']) {
                $order->returnUrl = $data['returnUrl'];
            }

            if (!empty($data['notifyUrl']) && parse_url($data['notifyUrl'], PHP_URL_HOST) === $merch['domain']) {
                $order->notifyUrl = $data['notifyUrl'];
            }

            $order->save();

            $this->jsonOutput($order);
        } else {
            $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403');
        }
    }

    public function completeOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->all();

        $merch = Merchandiser::findOrFail($order['merchandiser_id']);

        if (!empty($data['trade_no']) && $data['trade_no'] === $order->trade_no) {
            if ($this->verify($data, $merch['pubkey'])) {
                if ($order->status === 'processing') {
                    $order->status = 'done';
                    $order->save();
                }

                $this->jsonOutput($order);
            } else {
                $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403');
            }
        } else {
            $this->jsonOutput(null, 'trade_no not matched', '404');
        }
    }

    public function removeOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $data = $request->all();

        $merch = Merchandiser::findOrFail($order['merchandiser_id']);

        if (!empty($data['trade_no']) && $data['trade_no'] === $order->trade_no) {
            if ($this->verify($data, $merch['pubkey'])) {
                if (in_array($order->status, ['refunded', 'cancelled'])) {
                    $order->delete();

                    $this->jsonOutput(null);
                } else {
                    $this->jsonOutput(null, 'Cannot delete this order', '405');
                }
            } else {
                $this->jsonOutput(null, 'Signature Invalid or timestamp expired', '403');
            }
        } else {
            $this->jsonOutput(null, 'trade_no not matched', '404');
        }
    }
}
