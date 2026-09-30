# Laravel Razorpay Payment Integration

A Laravel application demonstrating a full Razorpay payment integration — order creation, checkout, signature verification, webhook handling, PDF invoice generation, email notifications, and background job processing — built with Laravel Breeze (Tailwind CSS) for authentication and UI.

## Overview

This project implements a complete payment lifecycle:

1. An authenticated user submits payment details (name, email, phone, amount).
2. A Razorpay order is created and a local `Payment` record is stored.
3. Razorpay Checkout opens client-side; on completion, the browser posts the payment/order/signature back to the server for verification.
4. Independently, Razorpay's servers send a webhook confirming the payment — providing a second, server-to-server confirmation path in case the browser-driven flow is interrupted.
5. Once confirmed, a PDF invoice is generated and emailed to the customer via a queued background job.
6. The customer can download the invoice from the success page (permanent link) or from the emailed notification (time-limited signed link).

## Core Concepts Used

### Routing & HTTP

- Named routes, route groups, and prefixing
- Route-model binding on custom columns (e.g. `{payment:order_id}`)
- Post/Redirect/Get (PRG) pattern to prevent form resubmission on refresh/back navigation
- CSRF protection with explicit route exemptions (webhooks)
- Signed, expiring URLs (`URL::temporarySignedRoute`, `signed` middleware)

### Middleware

- Custom middleware to redirect away from in-progress pages once a payment is already resolved
- Cache-control middleware to prevent the browser from serving stale pages via back/forward cache

### Eloquent & Database

- Models, relationships (`hasOne`, `belongsTo`), query scopes
- ULIDs for the `Invoice` primary key
- Foreign keys with `unique()` and `cascadeOnDelete()` to guarantee one invoice per payment
- Eager loading to avoid N+1 queries
- Eloquent Observers for side effects on model lifecycle events (cache invalidation)

### Architecture Patterns

- **Action classes** — single-purpose classes (`CreateOrder`, `VerifyPayment`, `RecordFailedPayment`) that encapsulate business logic, keeping controllers thin
- **Service classes** — `InvoiceService` centralizes PDF generation, storage, and URL-building logic
- **Service Container & Providers** — the Razorpay API client and `InvoiceService` are bound as singletons and injected via constructor, rather than instantiated inline
- SOLID principles applied deliberately, particularly Single Responsibility and Dependency Inversion

### Jobs & Queues

- `GenerateInvoiceJob` — queued, retryable, idempotent job for invoice generation and email dispatch
- Configurable retry attempts and backoff
- Failure logging distinct from success paths

### Notifications & Mail

- Laravel Notifications with on-demand routing (`Notification::route()`) for guest recipients with no `Notifiable` model
- Separate permanent vs. time-limited signed download links depending on context (success page vs. email)

### Caching

- Version-keyed cache invalidation for paginated list views, triggered automatically via an Observer

### File Storage & PDFs

- Laravel's Filesystem abstraction (disk-agnostic; swappable between local and cloud storage via config)
- PDF generation via DomPDF, including embedded images

### Error Handling & Logging

- Layered `try/catch` blocks distinguishing recoverable failures (logged, swallowed) from failures that should surface to the user
- Structured logging with contextual data

### Third-Party Integration

- Razorpay SDK for order creation and signature verification
- Webhook signature verification and idempotent event handling

## File Structure

```
app/
├── Actions/
│   └── Payments/
│       ├── CreateOrder.php              # Creates Razorpay order + Payment record
│       ├── VerifyPayment.php            # Verifies signature, marks payment complete, dispatches invoice job
│       ├── RecordFailedPayment.php      # Records a failed payment attempt
│       └── VerifyRazorpayWebhookSignature.php
├── Http/
│   ├── Controllers/
│   │   ├── PaymentController.php        # Checkout flow: form, order creation, verification, failure handling
│   │   ├── PaymentListController.php    # Paginated, cached list of payments (single-action controller)
│   │   ├── InvoiceController.php        # Invoice download (permanent + signed routes)
│   │   └── RazorpayWebhookController.php
│   ├── Middleware/
│   │   ├── CheckPaymentResolved.php     # Redirects away from process page once payment is resolved
│   │   └── PreventBackHistoryCache.php  # Prevents stale bfcache on payment status pages
│   └── Requests/
│       └── Payment/PaymentFormRequest.php
├── Jobs/
│   └── GenerateInvoiceJob.php           # Queued: generates PDF, sends notification, marks invoice mailed
├── Models/
│   ├── Payment.php                      # status: created|completed|failed
│   └── Invoice.php                      # ULID PK, belongsTo Payment, unique payment_id
├── Notifications/
│   └── InvoicePaidNotification.php      # Emails the invoice with a signed download link
├── Observers/
│   └── PaymentObserver.php              # Invalidates list cache on payment create/update/delete
├── Providers/
│   ├── RazorpayServiceProvider.php      # Binds Razorpay Api client as a singleton
│   └── InvoiceServiceProvider.php       # Binds InvoiceService with configured disk + link expiry
└── Services/
    └── InvoiceService.php               # PDF generation, storage, permanent + signed URL builders

resources/views/
├── payment/
│   ├── index.blade.php                  # Payment form
│   ├── process.blade.php                # Opens Razorpay Checkout, handles success/failure callbacks
│   ├── success.blade.php
│   ├── failure.blade.php
│   └── list.blade.php                   # Paginated payment list with status badges and actions
├── invoices/
│   └── pdf.blade.php                    # Invoice PDF template
└── emails/
    └── invoice.blade.php                # (or rendered via Notification's toMail)

database/migrations/
├── ..._create_payments_table.php
└── ..._create_invoices_table.php

config/
└── invoices.php                         # disk + signed link expiry, env-driven

routes/
└── web.php                              # Payment routes (auth-gated), invoice download routes, webhook route
```

## Environment Configuration

```env
RAZORPAY_KEY=
RAZORPAY_SECRET=
RAZORPAY_WEBHOOK_SECRET=

INVOICE_DISK=local
INVOICE_LINK_EXPIRY_DAYS=7

QUEUE_CONNECTION=database
MAIL_MAILER=log
```

## Local Development Notes

- Requires the PHP **GD extension** enabled (needed by DomPDF for embedded images in invoices).
- Webhook testing requires a public tunnel to your local server, since Razorpay must reach your machine directly. `ngrok` is blacklisted by Razorpay's webhook URL policy — use **zrok** instead.
- Run a queue worker (`php artisan queue:work`) for invoice generation and email delivery to actually process; `QUEUE_CONNECTION=sync` can be used for immediate, synchronous testing without a worker.
- A Docker Compose setup (app, nginx, mysql, queue worker) is available for containerized local development.

## Payment Status Flow

```
created ──► completed  (signature verified, invoice generation queued)
   │
   └──────► failed      (signature verification failed, or client reported failure)
```

Both the browser-driven verification callback and the Razorpay webhook can independently confirm a payment; the system is designed to be idempotent against either arriving more than once, or both arriving for the same payment.
