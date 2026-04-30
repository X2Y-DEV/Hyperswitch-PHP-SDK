<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient;

use X2Y\HyperswitchClient\Resource\CustomersResource;
use X2Y\HyperswitchClient\Resource\PaymentsResource;
use X2Y\HyperswitchClient\Resource\RefundsResource;

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
}
