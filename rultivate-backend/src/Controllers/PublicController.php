<?php
namespace Rultivate\Controllers;

use Rultivate\Services\VendorService;
use Rultivate\Services\CmsService;

class PublicController extends BaseController
{
    public function __construct(private VendorService $vendorService, private CmsService $cmsService) {}

    public function vendors(array $request): void
    {
        $vendors = $this->vendorService->listPublic($request['query']);
        $this->json($vendors);
    }

    public function vendorDetail(array $request): void
    {
        $vendor = $this->vendorService->getPublic($request['params']['slug']);
        if (!$vendor) {
            $this->json(['message' => 'Vendor not found'], 404);
            return;
        }
        $this->json($vendor);
    }

    public function cmsPages(array $request): void
    {
        $pages = $this->cmsService->list();
        $this->json($pages);
    }

    public function cmsPage(array $request): void
    {
        $page = $this->cmsService->getBySlug($request['params']['slug']);
        if (!$page) {
            $this->json(['message' => 'Page not found'], 404);
            return;
        }
        if ($request['method'] === 'GET') {
            $this->json($page);
            return;
        }
        $updated = $this->cmsService->update($request['params']['slug'], $request['body']);
        $this->json($updated);
    }
}
