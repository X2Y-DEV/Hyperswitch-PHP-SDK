<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient;

use X2Y\HyperswitchClient\Resource\ApiKeysResource;
use X2Y\HyperswitchClient\Resource\BusinessProfileResource;
use X2Y\HyperswitchClient\Resource\ConnectorsResource;
use X2Y\HyperswitchClient\Resource\CustomersResource;
use X2Y\HyperswitchClient\Resource\DisputesResource;
use X2Y\HyperswitchClient\Resource\MandatesResource;
use X2Y\HyperswitchClient\Resource\MerchantAccountsResource;
use X2Y\HyperswitchClient\Resource\PaymentLinksResource;
use X2Y\HyperswitchClient\Resource\PaymentMethodSessionsResource;
use X2Y\HyperswitchClient\Resource\PaymentMethodsResource;
use X2Y\HyperswitchClient\Resource\PaymentsResource;
use X2Y\HyperswitchClient\Resource\PayoutsResource;
use X2Y\HyperswitchClient\Resource\PlatformResource;
use X2Y\HyperswitchClient\Resource\ProxyResource;
use X2Y\HyperswitchClient\Resource\RefundsResource;
use X2Y\HyperswitchClient\Resource\RelayResource;
use X2Y\HyperswitchClient\Resource\SubscriptionsResource;

/**
 * Top-level facade. Each Postman group exposes one resource accessor.
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

    public function paymentMethods(): PaymentMethodsResource;

    public function paymentMethodSessions(): PaymentMethodSessionsResource;

    public function mandates(): MandatesResource;

    public function disputes(): DisputesResource;

    public function payouts(): PayoutsResource;

    public function relay(): RelayResource;

    public function proxy(): ProxyResource;

    public function platform(): PlatformResource;
}
