<?php
namespace Rultivate\Controllers;

use Rultivate\Services\VendorService;
use Rultivate\Services\RfqService;
use Rultivate\Services\BidService;
use Rultivate\Services\OrderService;
use Rultivate\Services\SubscriptionService;
use Rultivate\Services\MessageService;
use Rultivate\Services\NotificationService;

class VendorController extends BaseController
{
    private function resolveVendorId(array $request): int
    {
        $vendorId = $this->vendorService->getVendorIdByUser((int)$request['user']['id']);
        if (!$vendorId) {
            throw new \RuntimeException('Vendor profile not found');
        }
        return $vendorId;
    }

    public function __construct(
        private VendorService $vendorService,
        private RfqService $rfqService,
        private BidService $bidService,
        private OrderService $orderService,
        private SubscriptionService $subscriptionService,
        private MessageService $messageService,
        private NotificationService $notificationService
    ) {}

    public function profile(array $request): void
    {
        if ($request['method'] === 'GET') {
            $profile = $this->vendorService->getProfile((int)$request['user']['id']);
            $this->json($profile ?? []);
            return;
        }
        $profile = $this->vendorService->updateProfile((int)$request['user']['id'], $request['body']);
        $this->json($profile);
    }

    public function rfqs(array $request): void
    {
        $vendorId = $this->resolveVendorId($request);
        $rfqs = $this->rfqService->listForVendor($vendorId);
        $this->json($rfqs);
    }

    public function submitBid(array $request): void
    {
        $rfqId = (int)$request['body']['rfqId'];
        $vendorId = $this->resolveVendorId($request);
        $bid = $this->bidService->submit($rfqId, $vendorId, $request['body']);
        $this->json($bid, 201);
    }

    public function listBids(array $request): void
    {
        $bids = $this->rfqService->listBids((int)$request['body']['rfqId'] ?? (int)$request['params']['rfqId']);
        $this->json($bids);
    }

    public function bids(array $request): void
    {
        $vendorId = $this->resolveVendorId($request);
        $bids = $this->bidService->listForVendor($vendorId);
        $this->json($bids);
    }

    public function orders(array $request): void
    {
        $vendorId = $this->resolveVendorId($request);
        $orders = $this->orderService->listForVendor($vendorId);
        $this->json($orders);
    }

    public function subscription(array $request): void
    {
        $vendorId = $this->resolveVendorId($request);
        if ($request['method'] === 'GET') {
            $subscription = $this->subscriptionService->getVendorSubscription($vendorId);
            $this->json($subscription ?? []);
            return;
        }
        $planId = (int)$request['body']['planId'];
        $subscription = $this->subscriptionService->activatePlan($vendorId, $planId);
        $this->json($subscription);
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
}
