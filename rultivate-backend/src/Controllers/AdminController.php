<?php
namespace Rultivate\Controllers;

use Rultivate\Services\UserService;
use Rultivate\Services\VendorService;
use Rultivate\Services\RfqService;
use Rultivate\Services\BidService;
use Rultivate\Services\OrderService;
use Rultivate\Services\SubscriptionService;
use Rultivate\Services\NotificationService;
use Rultivate\Services\ContactService;
use Rultivate\Services\CmsService;

class AdminController extends BaseController
{
    public function __construct(
        private UserService $userService,
        private VendorService $vendorService,
        private RfqService $rfqService,
        private BidService $bidService,
        private OrderService $orderService,
        private SubscriptionService $subscriptionService,
        private NotificationService $notificationService,
        private ContactService $contactService,
        private CmsService $cmsService
    ) {}

    public function users(array $request): void
    {
        $db = $request['db']->getConnection();
        $stmt = $db->query('SELECT u.id, u.email, u.full_name, u.status, GROUP_CONCAT(r.slug) as roles FROM users u LEFT JOIN user_roles ur ON ur.user_id = u.id LEFT JOIN roles r ON r.id = ur.role_id GROUP BY u.id');
        $this->json($stmt->fetchAll());
    }

    public function updateRoles(array $request): void
    {
        $userId = (int)$request['params']['id'];
        $roles = $request['body']['roles'] ?? [];
        $this->userService->assignRoles($userId, $roles);
        $user = $this->userService->findById($userId);
        $this->json($user ?? []);
    }

    public function vendors(array $request): void
    {
        $filters = $request['query'];
        $filters['include_all'] = true;
        $vendors = $this->vendorService->listPublic($filters);
        $this->json($vendors);
    }

    public function updateVendorStatus(array $request): void
    {
        $vendor = $this->vendorService->updateProfile((int)$request['params']['id'], $request['body']);
        $this->json($vendor);
    }

    public function rfqs(array $request): void
    {
        $db = $request['db']->getConnection();
        $stmt = $db->query('SELECT * FROM rfqs ORDER BY created_at DESC');
        $this->json($stmt->fetchAll());
    }

    public function bids(array $request): void
    {
        $db = $request['db']->getConnection();
        $stmt = $db->query('SELECT * FROM bids ORDER BY created_at DESC');
        $this->json($stmt->fetchAll());
    }

    public function orders(array $request): void
    {
        $db = $request['db']->getConnection();
        $stmt = $db->query('SELECT * FROM orders ORDER BY created_at DESC');
        $this->json($stmt->fetchAll());
    }

    public function subscriptionPlans(array $request): void
    {
        if ($request['method'] === 'GET') {
            $plans = $this->subscriptionService->listPlans();
            $this->json($plans);
            return;
        }
        if ($request['method'] === 'POST') {
            $plan = $this->subscriptionService->createPlan($request['body']);
            $this->json($plan, 201);
            return;
        }
        if ($request['method'] === 'PUT') {
            $plan = $this->subscriptionService->updatePlan((int)$request['params']['id'], $request['body']);
            $this->json($plan);
            return;
        }
        if ($request['method'] === 'DELETE') {
            $this->subscriptionService->deletePlan((int)$request['params']['id']);
            $this->json(['message' => 'Plan deleted']);
            return;
        }
    }

    public function broadcast(array $request): void
    {
        $body = $request['body'];
        $userIds = $body['userIds'] ?? [];
        $this->notificationService->broadcast($body, $userIds);
        $this->json(['message' => 'Notification sent']);
    }

    public function contacts(array $request): void
    {
        $this->json($this->contactService->list());
    }

    public function cms(array $request): void
    {
        if ($request['method'] === 'GET') {
            $this->json($this->cmsService->list());
            return;
        }
        $updated = $this->cmsService->update($request['params']['slug'], $request['body']);
        $this->json($updated);
    }
}
