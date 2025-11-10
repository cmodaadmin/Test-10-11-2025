# Rultivate Marketplace Platform

Rultivate is a Salesforce-style marketplace built for Indian procurement teams. It combines an Angular single-page application with a secure Core PHP REST API and a MySQL backend. The project is split into two deployable workspaces:

- `rultivate-frontend/` – Angular SPA for customers, vendors and admin teams.
- `rultivate-backend/` – Core PHP 8 REST API designed for Apache + cPanel hosting.

## Features

- JWT authentication with role-based access control for guests, customers, vendors and departmental admin roles.
- RFQ lifecycle (creation, vendor invitations, bids, order generation) fully modelled in PHP services and surfaced through Angular dashboards.
- Vendor subscription management with mock Razorpay-ready payment hooks and INR pricing display.
- Messaging, notifications, reviews and CMS page management modules wired front-to-back.
- MySQL schema with referential integrity and timestamp tracking for all business entities.

## Getting Started

### Backend (Core PHP)

1. Install PHP 8.1+, Composer and MySQL 8+ on your local machine or VPS.
2. Copy `rultivate-backend/` to the server (for cPanel, upload inside a subdirectory and map `/public` as the web root for the API subdomain).
3. From the `rultivate-backend/` directory run `composer install` to pull dependencies (`firebase/php-jwt`).
4. Create a MySQL database and import the schema:
   ```sql
   SOURCE database_schema.sql;
   ```
5. Update `rultivate-backend/config/config.php` with database credentials, JWT secret and the API base URL.
6. Create the initial super admin user manually using the SQL comments at the bottom of `database_schema.sql` (replace `PASSWORD_HASH_HERE` with the output of `password_hash('YourStrongPassword', PASSWORD_DEFAULT)` and set `NEW_USER_ID` to the inserted user id).
7. Ensure Apache rewrites are enabled. The provided `.htaccess` rewrites `/api/*` to `public/index.php`.
8. Test locally with `php -S localhost:8000 -t public` (for quick development) or deploy via cPanel by pointing the subdomain to `rultivate-backend/public`.

### Frontend (Angular)

1. Install Node.js 18+ and Angular CLI 16 (`npm install -g @angular/cli`).
2. From `rultivate-frontend/` run:
   ```bash
   npm install
   npm run build
   ```
3. Set the API endpoint in `src/environments/environment.ts` (development) and `environment.prod.ts` (production). For cPanel deployment, upload the `dist/rultivate/` build into your `public_html` directory or use a subfolder and configure Angular routing with rewrites.
4. The Angular app ships with Salesforce-inspired layouts, lazy-loaded modules for customer, vendor and admin workspaces, and services that call the PHP REST API.

### Module Overview

- **Public**: Home, About, How it Works, Pricing, FAQ, Contact, and Vendor Directory pages pulling data from CMS and vendor APIs.
- **Auth**: Customer and vendor registration, multi-role login, forgot/reset password flows.
- **Customer Workspace**: Dashboards, profile management, vendor browsing, RFQ creation, bid comparison, order tracking, messaging, notifications, reviews and invoice viewing.
- **Vendor Workspace**: Profile & KYC, subscription upgrades, RFQ discovery, bid submission, order pipelines, messaging, notifications and earnings overview.
- **Admin & Department**: Users & roles, vendor approvals, RFQ/bid monitoring, order oversight, subscription plans, contact submissions, CMS management and notification broadcasts.

## Deployment Notes

- **cPanel/Apache**: Point the API subdomain to `rultivate-backend/public` and ensure `AllowOverride All` is enabled to honor `.htaccess`. The Angular build should be served separately (e.g. primary domain).
- **Environment variables**: Store sensitive credentials in environment-specific config files or cPanel environment manager; do not commit secrets to version control.
- **File Permissions**: Ensure `storage/`-like directories (if introduced later) are writable by the web server.

## Testing & Tooling

- Backend endpoints can be validated using Postman collections pointed at `/api/*` routes. JWT tokens returned from `/api/auth/login` must be supplied in the `Authorization: Bearer <token>` header.
- Angular modules rely on strongly typed interfaces located in `src/app/shared/models`. Update these when backend contracts evolve.
- Use ESLint/Prettier or Angular CLI formatting commands to keep the front-end consistent.

## Roadmap

- Integrate Razorpay SDK for real payment flows.
- Add analytics dashboards for spend tracking and SLA performance.
- Introduce unit/integration test suites (PHPUnit for backend, Jasmine/Karma for Angular).

