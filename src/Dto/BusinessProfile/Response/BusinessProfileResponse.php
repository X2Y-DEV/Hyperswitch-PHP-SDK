<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\BusinessProfile\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Response from business profile create / retrieve / update / list (item).
 *
 * Top-level scalars are typed; the many nested config blobs follow the
 * connector-blob convention and stay as `array<string, mixed>` (preserved
 * via the `extras` mechanism).
 *
 * @endpoint            GET /account/{merchant_id}/business_profile/{profile_id}
 * @hyperswitch-source  2026-04-30
 */
final class BusinessProfileResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $profileId,
        public readonly string $merchantId,
        public readonly string $profileName,
        public readonly ?string $returnUrl = null,
        public readonly ?bool $enablePaymentResponseHash = null,
        public readonly ?bool $redirectToMerchantWithHttpPost = null,
        public readonly ?int $intentFulfillmentTime = null,
        public readonly ?int $sessionExpiry = null,
        public readonly ?bool $isConnectorAgnosticMitEnabled = null,
        public readonly ?bool $isAutoRetriesEnabled = null,
        public readonly ?int $maxAutoRetriesEnabled = null,
        public readonly ?bool $isTaxConnectorEnabled = null,
        public readonly ?string $taxConnectorId = null,
        public readonly ?bool $isNetworkTokenizationEnabled = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'profile_id', 'merchant_id', 'profile_name', 'return_url',
            'enable_payment_response_hash', 'redirect_to_merchant_with_http_post',
            'intent_fulfillment_time', 'session_expiry', 'is_connector_agnostic_mit_enabled',
            'is_auto_retries_enabled', 'max_auto_retries_enabled',
            'is_tax_connector_enabled', 'tax_connector_id',
            'is_network_tokenization_enabled',
        ];

        return new self(
            profileId:                      (string) $data['profile_id'],
            merchantId:                     (string) $data['merchant_id'],
            profileName:                    (string) $data['profile_name'],
            returnUrl:                      isset($data['return_url']) ? (string) $data['return_url'] : null,
            enablePaymentResponseHash:      isset($data['enable_payment_response_hash'])
                ? (bool) $data['enable_payment_response_hash']
                : null,
            redirectToMerchantWithHttpPost: isset($data['redirect_to_merchant_with_http_post'])
                ? (bool) $data['redirect_to_merchant_with_http_post']
                : null,
            intentFulfillmentTime:          isset($data['intent_fulfillment_time'])
                ? (int) $data['intent_fulfillment_time']
                : null,
            sessionExpiry:                  isset($data['session_expiry'])
                ? (int) $data['session_expiry']
                : null,
            isConnectorAgnosticMitEnabled:  isset($data['is_connector_agnostic_mit_enabled'])
                ? (bool) $data['is_connector_agnostic_mit_enabled']
                : null,
            isAutoRetriesEnabled:           isset($data['is_auto_retries_enabled'])
                ? (bool) $data['is_auto_retries_enabled']
                : null,
            maxAutoRetriesEnabled:          isset($data['max_auto_retries_enabled'])
                ? (int) $data['max_auto_retries_enabled']
                : null,
            isTaxConnectorEnabled:          isset($data['is_tax_connector_enabled'])
                ? (bool) $data['is_tax_connector_enabled']
                : null,
            taxConnectorId:                 isset($data['tax_connector_id'])
                ? (string) $data['tax_connector_id']
                : null,
            isNetworkTokenizationEnabled:   isset($data['is_network_tokenization_enabled'])
                ? (bool) $data['is_network_tokenization_enabled']
                : null,
            extras:                         array_diff_key($data, array_flip($known)),
        );
    }
}
