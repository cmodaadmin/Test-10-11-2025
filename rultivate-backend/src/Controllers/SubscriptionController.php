<?php
namespace Rultivate\Controllers;

use Rultivate\Services\SubscriptionService;

class SubscriptionController extends BaseController
{
    public function __construct(private SubscriptionService $subscriptionService) {}

    public function list(array $request): void
    {
        $this->json($this->subscriptionService->listPlans());
    }

    public function adminPlans(array $request): void
    {
        if ($request['method'] === 'GET') {
            $this->json($this->subscriptionService->listPlans());
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
}
