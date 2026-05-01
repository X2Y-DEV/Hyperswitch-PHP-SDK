<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Resource;

use X2Y\HyperswitchClient\Dto\PaymentLink\Response\PaymentLinkResponse;
use X2Y\HyperswitchClient\Endpoint\PaymentLinksEndpoints;
use X2Y\HyperswitchClient\Exception\HyperswitchApiException;

/**
 * Typed facade over the Hyperswitch Payment Links API group.
 *
 * Payment links are *created* by setting `payment_link: true` on a regular
 * `Payments::create()` call. Only retrieval has its own endpoint.
 *
 * @postman             Payment Links
 * @hyperswitch-source  2026-04-30
 */
final class PaymentLinksResource extends AbstractResource
{
    /**
     * @postman   Payment Links / Payment Link - Retrieve
     */
    public function retrieve(string $paymentLinkId): PaymentLinkResponse
    {
        $response = $this->execute(
            PaymentLinksEndpoints::retrieve(),
            pathParams: ['id' => $paymentLinkId],
        );
        if (!$response instanceof PaymentLinkResponse) {
            throw new HyperswitchApiException('Unexpected response type from PaymentLinks::retrieve.');
        }
        return $response;
    }
}
