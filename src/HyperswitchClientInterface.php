<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient;

use X2Y\HyperswitchClient\Resource\ApiKeysResource;
use X2Y\HyperswitchClient\Resource\BusinessProfileResource;
use X2Y\HyperswitchClient\Resource\ConnectorsResource;
use X2Y\HyperswitchClient\Resource\CustomersResource;
use X2Y\HyperswitchClient\Resource\MerchantAccountsResource;
use X2Y\HyperswitchClient\Resource\PaymentLinksResource;
use X2Y\HyperswitchClient\Resource\PaymentsResource;
use X2Y\HyperswitchClient\Resource\RefundsResource;
use X2Y\HyperswitchClient\Resource\SubscriptionsResource;

/**
 * Top-level facade. Each Postman group exposes one resource accessor.
 *
 * The interface is grown as new Resource classes land — adding `subscriptions()`,
 * `customers()`, etc. as Phase 3 fills in the SDK.
 */
interface HyperswitchClientInterface
{
    public function payments(): PaymentsResource;

    public function refunds(): RefundsResource;

    public function customers(): CustomersResource;

    public function paymentLinks(): PaymentLinksResource;

    public function apiKeys(): ApiKeysResource;

    public function businessProfile(): BusinessProfileResource;

    public function merchantAccounts(): MerchantAccountsResource;

    public function connectors(): ConnectorsResource;

    public function subscriptions(): SubscriptionsResource;
}
