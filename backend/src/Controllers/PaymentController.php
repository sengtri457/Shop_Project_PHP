<?php

namespace App\Controllers;

use App\Core\Response;
use App\Models\Order;
use App\Services\BakongService;

class PaymentController
{
    private BakongService $bakongService;

    public function __construct()
    {
        $this->bakongService = new BakongService();
    }

    /**
     * GET /payments/bakong/config
     * Returns Bakong merchant configuration info
     */
    public function config(): void
    {
        Response::json([
            'code' => 200,
            'data' => $this->bakongService->getConfig()
        ]);
    }

    /**
     * POST /payments/bakong/generate
     * Generates Bakong KHQR for an order
     * Body: { "order_id": 123, "currency": "USD" }
     */
    public function generateBakongQr(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $orderId = (int) ($data['order_id'] ?? 0);
        $currency = strtoupper(trim($data['currency'] ?? 'USD'));

        if (!$orderId) {
            Response::error('order_id is required');
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        $amount = (float) $order['total'];
        if ($amount <= 0) {
            Response::error('Invalid order total amount');
            return;
        }

        $khqrData = $this->bakongService->generateKhqr($orderId, $amount, $currency);

        // Update order with payment method and MD5 hash
        Order::updatePaymentStatus($orderId, 'unpaid', null, $khqrData['md5'], 'bakong');

        Response::json($khqrData);
    }

    /**
     * POST /payments/bakong/check
     * Checks payment status via Bakong Open API
     * Body: { "order_id": 123, "md5": "..." }
     */
    public function checkBakongStatus(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $orderId = (int) ($data['order_id'] ?? 0);
        $md5 = trim($data['md5'] ?? '');

        if (!$orderId) {
            Response::error('order_id is required');
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Response::error('Order not found', 404);
            return;
        }

        if (empty($md5)) {
            $md5 = $order['bakong_md5'] ?? '';
        }

        if (empty($md5)) {
            Response::error('No Bakong MD5 transaction hash found for this order');
            return;
        }

        $result = $this->bakongService->checkTransactionByMd5($md5);

        if ($result['paid']) {
            // Update order to paid & confirmed
            Order::updatePaymentStatus($orderId, 'paid', 'confirmed', $md5, 'bakong');
            Response::json([
                'status'   => 'paid',
                'paid'     => true,
                'message'  => 'Payment verified successfully by Bakong KHQR',
                'order_id' => $orderId
            ]);
            return;
        }

        Response::json([
            'status'   => 'pending',
            'paid'     => false,
            'message'  => $result['message'] ?? 'Payment pending KHQR scan',
            'order_id' => $orderId
        ]);
    }
}
