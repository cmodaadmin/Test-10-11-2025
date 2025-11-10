<?php
namespace Rultivate\Controllers;

use Rultivate\Services\ContactService;

class ContactController extends BaseController
{
    public function __construct(private ContactService $contactService) {}

    public function submit(array $request): void
    {
        $result = $this->contactService->submit($request['body']);
        $this->json($result, 201);
    }

    public function list(array $request): void
    {
        $submissions = $this->contactService->list();
        $this->json($submissions);
    }
}
