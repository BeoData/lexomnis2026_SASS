# Payment Setup Guide

## Overview

This guide explains how to set up payment processing (Stripe, PayPal, and Manual) for the LexOmnis SaaS application.

## Prerequisites

- Stripe account (for Stripe payments)
- PayPal Business account (for PayPal payments)
- Access to both Core App (LexOmnisC) and SaaS App (lexomnis2026_SASS) configuration

## Environment Variables

### Core App (LexOmnisC)

Add the following variables to `.env`:

```env
# Stripe Configuration
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...

# PayPal Configuration
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_CLIENT_SECRET=your_paypal_client_secret
PAYPAL_MODE=sandbox
PAYPAL_WEBHOOK_ID=your_webhook_id
```

### SaaS App (lexomnis2026_SASS)

The SaaS App uses the Core App API, so it only needs the Core App URL and API token:

```env
TENANT_APP_URL=http://localhost:8000
TENANT_APP_API_TOKEN=your_api_token_here
```

## Stripe Setup

### 1. Create Stripe Account

1. Go to [Stripe Dashboard](https://dashboard.stripe.com)
2. Create an account or sign in
3. Get your API keys from the Developers > API keys section

### 2. Configure Stripe Keys

Add your Stripe keys to Core App `.env`:

```env
STRIPE_KEY=pk_test_...  # Public key (starts with pk_)
STRIPE_SECRET=sk_test_...  # Secret key (starts with sk_)
```

### 3. Set Up Webhook

1. In Stripe Dashboard, go to Developers > Webhooks
2. Click "Add endpoint"
3. Set endpoint URL: `https://your-domain.com/api/webhooks/stripe`
4. Select events to listen to:
   - `checkout.session.completed`
   - `payment_intent.succeeded`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
   - `customer.subscription.deleted`
5. Copy the webhook signing secret and add to `.env`:
   ```env
   STRIPE_WEBHOOK_SECRET=whsec_...
   ```

### 4. Test Mode vs Live Mode

- **Test Mode**: Use keys starting with `pk_test_` and `sk_test_`
- **Live Mode**: Use keys starting with `pk_live_` and `sk_live_`

Switch `PAYPAL_MODE` in `.env` when ready for production.

## PayPal Setup

### 1. Create PayPal App

1. Go to [PayPal Developer Dashboard](https://developer.paypal.com)
2. Create a new app
3. Get your Client ID and Secret

### 2. Configure PayPal Keys

Add your PayPal credentials to Core App `.env`:

```env
PAYPAL_CLIENT_ID=your_client_id
PAYPAL_CLIENT_SECRET=your_client_secret
PAYPAL_MODE=sandbox  # or 'live' for production
```

### 3. Set Up Webhook

1. In PayPal Dashboard, go to My Apps & Credentials
2. Select your app
3. Go to Webhooks section
4. Add webhook URL: `https://your-domain.com/api/webhooks/paypal`
5. Select events:
   - `PAYMENT.SALE.COMPLETED`
   - `PAYMENT.SALE.DENIED`
   - `PAYMENT.SALE.REFUNDED`
6. Copy the Webhook ID and add to `.env`:
   ```env
   PAYPAL_WEBHOOK_ID=your_webhook_id
   ```

### 4. Sandbox vs Live

- **Sandbox**: Use for testing with fake payments
- **Live**: Use for production with real payments

## Manual Payment Setup

Manual payments don't require external service configuration. They work out of the box.

### Admin Approval Process

1. Users request manual payment through checkout
2. Payment transaction is created with `status: pending`
3. Admin reviews payment in SaaS App: `/payments/manual`
4. Admin approves or rejects the payment
5. Subscription is activated upon approval

## Database Setup

### Run Migrations

In Core App (LexOmnisC):

```bash
php artisan migrate
```

This will create:
- `payment_transactions` table
- Updates to `subscriptions` table (if needed)

### Seed Plans

Run the plans seeder to create default subscription plans:

```bash
php artisan db:seed --class=PlansSeeder
```

This creates three plans:
- **Basic** - €30/month
- **Professional** - €50/month
- **Premium** - €100/month

## Testing

### Stripe Test Cards

Use these test card numbers in Stripe test mode:

- Success: `4242 4242 4242 4242`
- Decline: `4000 0000 0000 0002`
- 3D Secure: `4000 0025 0000 3155`

Use any future expiry date and any CVC.

### PayPal Sandbox

1. Create sandbox accounts in PayPal Developer Dashboard
2. Use sandbox buyer account to test payments
3. Use sandbox business account to receive payments

## Frontend Configuration

### Stripe Public Key

In SaaS App, update `StripeCheckout.vue` component with your Stripe public key:

```javascript
const stripeKey = 'pk_test_...'; // Replace with actual key
```

Or better, pass it from backend via Inertia props.

### PayPal Client ID

In SaaS App, update `PayPalButton.vue` component:

```javascript
script.src = 'https://www.paypal.com/sdk/js?client-id=YOUR_CLIENT_ID&currency=EUR';
```

## Security Considerations

1. **Never commit API keys to version control**
   - Use `.env` files (already in `.gitignore`)
   - Use environment variables in production

2. **Webhook Signature Verification**
   - Always verify webhook signatures
   - Both Stripe and PayPal services verify signatures automatically

3. **HTTPS Required**
   - Webhooks must use HTTPS in production
   - Stripe and PayPal require HTTPS for webhook endpoints

4. **Rate Limiting**
   - Payment endpoints are rate-limited
   - Adjust limits in `routes/api.php` if needed

## Troubleshooting

### Stripe Webhook Not Working

1. Check webhook URL is accessible
2. Verify webhook secret in `.env`
3. Check Stripe Dashboard for webhook delivery logs
4. Verify signature verification in `StripeService.php`

### PayPal Webhook Not Working

1. Verify webhook URL in PayPal Dashboard
2. Check webhook ID in `.env`
3. Review PayPal webhook event logs
4. Ensure HTTPS is enabled

### Manual Payments Not Appearing

1. Check that payment transactions are created
2. Verify admin has access to `/payments/manual`
3. Check database for pending transactions
4. Review API endpoint permissions

## Production Checklist

- [ ] Switch Stripe to live mode keys
- [ ] Switch PayPal to live mode
- [ ] Update webhook URLs to production domain
- [ ] Test all payment methods end-to-end
- [ ] Set up monitoring for failed payments
- [ ] Configure email notifications
- [ ] Review and test refund process
- [ ] Set up backup payment methods
- [ ] Document support procedures

## Support

For issues or questions:
- Check API logs: `storage/logs/laravel.log`
- Review webhook delivery in payment provider dashboards
- Check database for transaction records
- Review error messages in browser console (frontend)
