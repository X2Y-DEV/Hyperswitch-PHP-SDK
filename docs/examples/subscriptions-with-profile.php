<?php
/**
 * Subscriptions are profile-scoped — every call needs `X-Profile-Id`.
 * Demonstrates the fluent `withProfileId()` builder pattern.
 *
 * Run from the repo root:
 *
 *     HYPERSWITCH_SANDBOX_KEY=snd_... \
 *     HYPERSWITCH_PROFILE_ID=pro_xxx \
 *     php docs/examples/subscriptions-with-profile.php
 */

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use X2Y\HyperSwitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperSwitchClient\Auth\MerchantKeyV1AuthResolver;
use X2Y\HyperSwitchClient\Dto\Subscription\Request\ListSubscriptionsRequest;
use X2Y\HyperSwitchClient\Exception\NotFoundException;
use X2Y\HyperSwitchClient\HyperSwitchClient;

$apiKey    = getenv('HYPERSWITCH_SANDBOX_KEY') ?: throw new RuntimeException('Set HYPERSWITCH_SANDBOX_KEY.');
$profileId = getenv('HYPERSWITCH_PROFILE_ID') ?: throw new RuntimeException('Set HYPERSWITCH_PROFILE_ID.');

$psr17  = new Psr17Factory();
$client = HyperSwitchClient::sandbox(
    httpClient:     new Psr18Client(),
    requestFactory: $psr17,
    streamFactory:  $psr17,
    auth:           [
        new MerchantKeyAuthResolver($apiKey),
        // V1 endpoints (Subscriptions, PaymentMethods, PaymentMethodSessions)
        // use a distinct wire format for the same merchant key. Register both.
        new MerchantKeyV1AuthResolver($apiKey),
    ],
);

// withProfileId() returns a *new* Resource instance scoped to that profile —
// the original $client->subscriptions() is untouched, so multiple profiles
// can coexist in the same process.
$subs = $client->subscriptions()->withProfileId($profileId);

$list = $subs->list(new ListSubscriptionsRequest(limit: 10));
echo "Subscriptions in profile {$profileId}: {$list->count()}\n";

foreach ($list->items() as $sub) {
    $next = $sub->nextBillingAt?->format('Y-m-d') ?? 'n/a';
    echo "  - {$sub->subscriptionId}  status={$sub->status->value}  next_billing={$next}\n";
}

// Retrieving a non-existent subscription confirms auth / profile wiring is
// working — we get a typed NotFoundException (404) rather than a 422 about
// missing X-Profile-Id, which would mean the builder didn't apply.
try {
    $subs->retrieve('sub_definitely_does_not_exist_' . bin2hex(random_bytes(3)));
    echo "(unexpected) retrieve succeeded\n";
} catch (NotFoundException $e) {
    echo "auth + profile wiring confirmed (got typed 404 as expected)\n";
}
