<?php
namespace Rultivate\Controllers;

use Rultivate\Services\CustomerService;
use Rultivate\Services\RfqService;
use Rultivate\Services\BidService;
use Rultivate\Services\OrderService;
use Rultivate\Services\MessageService;
use Rultivate\Services\NotificationService;
use Rultivate\Services\ReviewService;

class CustomerController extends BaseController
{
    public function __construct(
        private CustomerService $customerService,
        private RfqService $rfqService,
        private BidService $bidService,
        private OrderService $orderService,
        private MessageService $messageService,
        private NotificationService $notificationService,
        private ReviewService $reviewService
    ) {}

    public function profile(array $request): void
    {
        if ($request['method'] === 'GET') {
            $profile = $this->customerService->getProfile((int)$request['user']['id']);
            $this->json($profile ?? []);
            return;
        }
        $profile = $this->customerService->updateProfile((int)$request['user']['id'], $request['body']);
        $this->json($profile);
    }

    public function listRfqs(array $request): void
    {
        $rfqs = $this->rfqService->list((int)$request['user']['id']);
        $this->json($rfqs);
    }

    public function createRfq(array $request): void
    {
        $rfq = $this->rfqService->create((int)$request['user']['id'], $request['body']);
        $this->json($rfq, 201);
    }

    public function updateRfq(array $request): void
    {
        $rfqId = (int)$request['params']['id'];
        $rfq = $this->rfqService->update($rfqId, (int)$request['user']['id'], $request['body']);
        $this->json($rfq);
    }

    public function deleteRfq(array $request): void
    {
        $rfqId = (int)$request['params']['id'];
        $this->rfqService->delete($rfqId, (int)$request['user']['id']);
        $this->json(['message' => 'RFQ deleted']);
    }

    public function rfqDetail(array $request): void
    {
        $rfq = $this->rfqService->find((int)$request['params']['id']);
        $this->json($rfq ?? []);
    }

    public function rfqBids(array $request): void
    {
        $bids = $this->rfqService->listBids((int)$request['params']['id']);
        $this->json($bids);
    }

    public function acceptBid(array $request): void
    {
        $bidId = (int)$request['params']['id'];
        $order = $this->bidService->accept($bidId, (int)$request['user']['id']);
        $this->json($order);
    }

    public function orders(array $request): void
    {
        $orders = $this->orderService->listForCustomer((int)$request['user']['id']);
        $this->json($orders);
    }

    public function orderDetail(array $request): void
    {
        $order = $this->orderService->find((int)$request['params']['id']);
        $this->json($order ?? []);
    }

    public function orderPayments(array $request): void
    {
        $payments = $this->orderService->listPayments((int)$request['params']['id']);
        $this->json($payments);
    }

    public function messages(array $request): void
    {
        $threadId = (int)$request['params']['threadId'];
        if ($request['method'] === 'GET') {
            $messages = $this->messageService->list($threadId, (int)$request['user']['id']);
            $this->json($messages);
            return;
        }
        $body = $request['body'];
        $message = $this->messageService->send($threadId, (int)$request['user']['id'], (int)$body['receiverId'], $body['body']);
        $this->json($message, 201);
    }

    public function notifications(array $request): void
    {
        if ($request['method'] === 'GET') {
            $notifications = $this->notificationService->list((int)$request['user']['id']);
            $this->json($notifications);
            return;
        }
        $notification = $this->notificationService->markAsRead((int)$request['params']['id'], (int)$request['user']['id']);
        $this->json($notification);
    }

    public function reviews(array $request): void
    {
        if ($request['method'] === 'POST') {
            $review = $this->reviewService->create((int)$request['body']['orderId'], (int)$request['user']['id'], $request['body']);
            $this->json($review, 201);
            return;
        }
        $reviews = $this->reviewService->listByVendor((int)$request['params']['vendorId']);
        $this->json($reviews);
    }
}
