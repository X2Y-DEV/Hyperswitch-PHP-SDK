<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient;

use X2Y\HyperswitchClient\Resource\PaymentsResource;

/**
 * Top-level facade. Each Postman group exposes one resource accessor.
 *
 * The interface is grown as new Resource classes land — adding `subscriptions()`,
 * `refunds()`, etc. as Phase 3 fills in the SDK.
 */
interface HyperswitchClientInterface
{
    public function payments(): PaymentsResource;
}
