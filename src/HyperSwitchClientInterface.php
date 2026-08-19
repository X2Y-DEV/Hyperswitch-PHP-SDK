<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient;

use X2Y\HyperSwitchClient\Resource\ApiKeysResource;
use X2Y\HyperSwitchClient\Resource\BusinessProfileResource;
use X2Y\HyperSwitchClient\Resource\ConnectorsResource;
use X2Y\HyperSwitchClient\Resource\CustomersResource;
use X2Y\HyperSwitchClient\Resource\DisputesResource;
use X2Y\HyperSwitchClient\Resource\MandatesResource;
use X2Y\HyperSwitchClient\Resource\MerchantAccountsResource;
use X2Y\HyperSwitchClient\Resource\PaymentLinksResource;
use X2Y\HyperSwitchClient\Resource\PaymentMethodSessionsResource;
use X2Y\HyperSwitchClient\Resource\PaymentMethodsResource;
use X2Y\HyperSwitchClient\Resource\PaymentsResource;
use X2Y\HyperSwitchClient\Resource\PayoutsResource;
use X2Y\HyperSwitchClient\Resource\PlatformResource;
use X2Y\HyperSwitchClient\Resource\ProxyResource;
use X2Y\HyperSwitchClient\Resource\RefundsResource;
use X2Y\HyperSwitchClient\Resource\RelayResource;
use X2Y\HyperSwitchClient\Resource\SubscriptionsResource;

/**
 * Top-level facade. Each API group exposes one resource accessor.
 */
interface HyperSwitchClientInterface
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
