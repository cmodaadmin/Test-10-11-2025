<?php
use Rultivate\Utils\Database;
use Rultivate\Utils\JWT as JwtFactory;
use Rultivate\Services\UserService;
use Rultivate\Services\AuthService;
use Rultivate\Services\CustomerService;
use Rultivate\Services\VendorService as VendorDomainService;
use Rultivate\Services\RfqService;
use Rultivate\Services\OrderService;
use Rultivate\Services\BidService;
use Rultivate\Services\SubscriptionService;
use Rultivate\Services\MessageService;
use Rultivate\Services\NotificationService;
use Rultivate\Services\ReviewService;
use Rultivate\Services\CmsService;
use Rultivate\Services\ContactService;
use Rultivate\Controllers\AuthController;
use Rultivate\Controllers\CustomerController;
use Rultivate\Controllers\VendorController;
use Rultivate\Controllers\PublicController;
use Rultivate\Controllers\ContactController;
use Rultivate\Controllers\AdminController;
use Rultivate\Controllers\SubscriptionController;

$database = new Database($router->getConfig()['db']);
$jwtFactory = new JwtFactory($router->getConfig()['jwt']);
$userService = new UserService($database);
$orderService = new OrderService($database);
$subscriptionService = new SubscriptionService($database);
$authService = new AuthService($database, $userService, $jwtFactory);
$customerService = new CustomerService($database);
$vendorDomainService = new VendorDomainService($database);
$rfqService = new RfqService($database);
$bidService = new BidService($database, $orderService);
$messageService = new MessageService($database);
$notificationService = new NotificationService($database);
$reviewService = new ReviewService($database);
$cmsService = new CmsService($database);
$contactService = new ContactService($database);

$authController = new AuthController($authService);
$customerController = new CustomerController($customerService, $rfqService, $bidService, $orderService, $messageService, $notificationService, $reviewService);
$vendorController = new VendorController($vendorDomainService, $rfqService, $bidService, $orderService, $subscriptionService, $messageService, $notificationService);
$publicController = new PublicController($vendorDomainService, $cmsService);
$contactController = new ContactController($contactService);
$subscriptionController = new SubscriptionController($subscriptionService);
$adminController = new AdminController($userService, $vendorDomainService, $rfqService, $bidService, $orderService, $subscriptionService, $notificationService, $contactService, $cmsService);

// Auth routes
$router->add('POST', '/auth/register/customer', [$authController, 'registerCustomer']);
$router->add('POST', '/auth/register/vendor', [$authController, 'registerVendor']);
$router->add('POST', '/auth/login', [$authController, 'login']);
$router->add('POST', '/auth/forgot-password', [$authController, 'forgotPassword']);
$router->add('POST', '/auth/reset-password', [$authController, 'resetPassword']);
$router->add('GET', '/auth/me', [$authController, 'me'], ['auth' => true]);

// Public endpoints
$router->add('GET', '/vendors/public', [$publicController, 'vendors']);
$router->add('GET', '/vendors/{slug}', [$publicController, 'vendorDetail']);
$router->add('GET', '/cms/pages', [$publicController, 'cmsPages']);
$router->add('GET', '/cms/pages/{slug}', [$publicController, 'cmsPage']);
$router->add('PUT', '/cms/pages/{slug}', [$publicController, 'cmsPage'], ['auth' => true, 'roles' => ['super_admin', 'dept_content']]);
$router->add('POST', '/contact', [$contactController, 'submit']);
$router->add('GET', '/subscription/plans', [$subscriptionController, 'list']);

// Customer secured routes
$router->add('GET', '/customer/profile', [$customerController, 'profile'], ['auth' => true, 'roles' => ['customer']]);
$router->add('PUT', '/customer/profile', [$customerController, 'profile'], ['auth' => true, 'roles' => ['customer']]);
$router->add('GET', '/rfqs', [$customerController, 'listRfqs'], ['auth' => true, 'roles' => ['customer']]);
$router->add('POST', '/rfqs', [$customerController, 'createRfq'], ['auth' => true, 'roles' => ['customer']]);
$router->add('GET', '/rfqs/{id}', [$customerController, 'rfqDetail'], ['auth' => true, 'roles' => ['customer', 'vendor', 'super_admin', 'dept_vendor']]);
$router->add('PUT', '/rfqs/{id}', [$customerController, 'updateRfq'], ['auth' => true, 'roles' => ['customer']]);
$router->add('DELETE', '/rfqs/{id}', [$customerController, 'deleteRfq'], ['auth' => true, 'roles' => ['customer']]);
$router->add('GET', '/rfqs/{id}/bids', [$customerController, 'rfqBids'], ['auth' => true, 'roles' => ['customer', 'vendor', 'super_admin', 'dept_vendor']]);
$router->add('POST', '/bids/{id}/accept', [$customerController, 'acceptBid'], ['auth' => true, 'roles' => ['customer']]);
$router->add('GET', '/orders', [$customerController, 'orders'], ['auth' => true, 'roles' => ['customer']]);
$router->add('GET', '/orders/{id}', [$customerController, 'orderDetail'], ['auth' => true, 'roles' => ['customer', 'vendor', 'super_admin', 'dept_operations']]);
$router->add('GET', '/orders/{id}/payments', [$customerController, 'orderPayments'], ['auth' => true, 'roles' => ['customer', 'vendor', 'super_admin', 'dept_finance']]);
$router->add('GET', '/messages/{threadId}', [$customerController, 'messages'], ['auth' => true]);
$router->add('POST', '/messages/{threadId}', [$customerController, 'messages'], ['auth' => true]);
$router->add('GET', '/notifications', [$customerController, 'notifications'], ['auth' => true]);
$router->add('POST', '/notifications/{id}/read', [$customerController, 'notifications'], ['auth' => true]);
$router->add('POST', '/reviews', [$customerController, 'reviews'], ['auth' => true, 'roles' => ['customer']]);
$router->add('GET', '/vendors/{vendorId}/reviews', [$customerController, 'reviews']);

// Vendor secured routes
$router->add('GET', '/vendor/profile', [$vendorController, 'profile'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('PUT', '/vendor/profile', [$vendorController, 'profile'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('GET', '/vendor/rfqs', [$vendorController, 'rfqs'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('POST', '/bids', [$vendorController, 'submitBid'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('GET', '/vendor/bids', [$vendorController, 'bids'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('GET', '/vendor/orders', [$vendorController, 'orders'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('GET', '/vendor/subscription', [$vendorController, 'subscription'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('POST', '/vendor/subscription/activate', [$vendorController, 'subscription'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('GET', '/vendor/messages/{threadId}', [$vendorController, 'messages'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('POST', '/vendor/messages/{threadId}', [$vendorController, 'messages'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('GET', '/vendor/notifications', [$vendorController, 'notifications'], ['auth' => true, 'roles' => ['vendor']]);
$router->add('POST', '/vendor/notifications/{id}/read', [$vendorController, 'notifications'], ['auth' => true, 'roles' => ['vendor']]);

// Admin routes
$router->add('GET', '/admin/users', [$adminController, 'users'], ['auth' => true, 'roles' => ['super_admin']]);
$router->add('POST', '/admin/users/{id}/roles', [$adminController, 'updateRoles'], ['auth' => true, 'roles' => ['super_admin']]);
$router->add('GET', '/admin/vendors', [$adminController, 'vendors'], ['auth' => true, 'roles' => ['super_admin', 'dept_vendor']]);
$router->add('PUT', '/admin/vendors/{id}', [$adminController, 'updateVendorStatus'], ['auth' => true, 'roles' => ['super_admin', 'dept_vendor']]);
$router->add('GET', '/admin/rfqs', [$adminController, 'rfqs'], ['auth' => true, 'roles' => ['super_admin', 'dept_operations']]);
$router->add('GET', '/admin/bids', [$adminController, 'bids'], ['auth' => true, 'roles' => ['super_admin', 'dept_operations']]);
$router->add('GET', '/admin/orders', [$adminController, 'orders'], ['auth' => true, 'roles' => ['super_admin', 'dept_operations', 'dept_finance']]);
$router->add('GET', '/admin/subscription/plans', [$subscriptionController, 'adminPlans'], ['auth' => true, 'roles' => ['super_admin']]);
$router->add('POST', '/admin/subscription/plans', [$subscriptionController, 'adminPlans'], ['auth' => true, 'roles' => ['super_admin']]);
$router->add('PUT', '/admin/subscription/plans/{id}', [$subscriptionController, 'adminPlans'], ['auth' => true, 'roles' => ['super_admin']]);
$router->add('DELETE', '/admin/subscription/plans/{id}', [$subscriptionController, 'adminPlans'], ['auth' => true, 'roles' => ['super_admin']]);
$router->add('POST', '/admin/notifications/broadcast', [$adminController, 'broadcast'], ['auth' => true, 'roles' => ['super_admin', 'dept_support']]);
$router->add('GET', '/admin/contacts', [$adminController, 'contacts'], ['auth' => true, 'roles' => ['super_admin', 'dept_support']]);
$router->add('GET', '/admin/cms', [$adminController, 'cms'], ['auth' => true, 'roles' => ['super_admin', 'dept_content']]);
$router->add('PUT', '/admin/cms/{slug}', [$adminController, 'cms'], ['auth' => true, 'roles' => ['super_admin', 'dept_content']]);
