<?php
/**
 * Customer lifecycle end-to-end against the HyperSwitch sandbox.
 *
 * Covers create → retrieve → update → mint ephemeral key → list → delete.
 *
 * Run from the repo root:
 *
 *     HYPERSWITCH_SANDBOX_KEY=snd_... php docs/examples/customer-lifecycle.php
 */

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;
use X2Y\HyperSwitchClient\Auth\MerchantKeyAuthResolver;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Customer\Request\CreateCustomerRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Request\CreateEphemeralKeyRequest;
use X2Y\HyperSwitchClient\Dto\Customer\Request\UpdateCustomerRequest;
use X2Y\HyperSwitchClient\HyperSwitchClient;

$apiKey = getenv('HYPERSWITCH_SANDBOX_KEY') ?: throw new RuntimeException(
    'Set HYPERSWITCH_SANDBOX_KEY before running this example.'
);
$psr17  = new Psr17Factory();
$client = HyperSwitchClient::sandbox(
    httpClient:     new Psr18Client(),
    requestFactory: $psr17,
    streamFactory:  $psr17,
    auth:           [new MerchantKeyAuthResolver($apiKey)],
);

$customerId = 'demo_' . bin2hex(random_bytes(4));

$created = $client->customers()->create(new CreateCustomerRequest(
    customerId: $customerId,
    email:      'demo@example.test',
    name:       'Demo Customer',
    address:    new Address(line1: '1 Test St', city: 'SF', country: 'US'),
    metadata:   ['source' => 'sdk_example'],
));
echo "1. created  : {$created->customerId}\n";

$got = $client->customers()->retrieve($customerId);
echo "2. retrieved: {$got->name} <{$got->email}>\n";

$updated = $client->customers()->update($customerId, new UpdateCustomerRequest(
    name: 'Demo Customer (renamed)',
));
echo "3. updated  : {$updated->name}\n";

// Ephemeral keys are short-lived per-customer keys handed to client-side code
// (browser / mobile) so it can call publishable-tier endpoints scoped to one
// customer without ever seeing the merchant key.
$ek = $client->customers()->createEphemeralKey(new CreateEphemeralKeyRequest($customerId));
echo "4. eph key  : {$ek->id} expires={$ek->expires?->format('c')}\n";

$list = $client->customers()->list();
echo "5. list     : {$list->count()} customers visible to this merchant\n";

$deleted = $client->customers()->delete($customerId);
echo "6. deleted  : customer_deleted=" . ($deleted->customerDeleted ? 'true' : 'false') . "\n";

// HyperSwitch soft-deletes — the record remains retrievable with
// `customer_deleted: true` rather than 404'ing afterward.
$post = $client->customers()->retrieve($customerId);
echo "7. retained : id={$post->customerId} (sandbox preserves the record)\n";
