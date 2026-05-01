<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\BusinessProfile\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Update an existing business profile. Same field set as create; every
 * field is optional and only the provided fields are updated.
 *
 * @postman             Business Profile / Business Profile - Update
 * @endpoint            POST /account/{merchant_id}/business_profile/{profile_id}
 * @hyperswitch-source  2026-04-30
 */
final class UpdateBusinessProfileRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $webhookDetails
     * @param array<string, mixed>|null $metadata
     * @param array<string, mixed>|null $routingAlgorithm
     * @param array<string, mixed>|null $frmRoutingAlgorithm
     * @param array<string, mixed>|null $payoutRoutingAlgorithm
     * @param list<string>|null         $applepayVerifiedDomains
     * @param array<string, mixed>|null $paymentLinkConfig
     * @param array<string, mixed>|null $authenticationConnectorDetails
     * @param array<string, mixed>|null $payoutLinkConfig
     * @param array<string, mixed>|null $outgoingWebhookCustomHttpHeaders
     */
    public function __construct(
        public readonly ?string $profileName = null,
        public readonly ?string $returnUrl = null,
        public readonly ?bool $enablePaymentResponseHash = null,
        public readonly ?bool $redirectToMerchantWithHttpPost = null,
        public readonly ?array $webhookDetails = null,
        public readonly ?array $metadata = null,
        public readonly ?int $intentFulfillmentTime = null,
        public readonly ?array $routingAlgorithm = null,
        public readonly ?array $frmRoutingAlgorithm = null,
        public readonly ?array $payoutRoutingAlgorithm = null,
        public readonly ?array $applepayVerifiedDomains = null,
        public readonly ?int $sessionExpiry = null,
        public readonly ?array $paymentLinkConfig = null,
        public readonly ?array $authenticationConnectorDetails = null,
        public readonly ?bool $useBillingAsPaymentMethodBilling = null,
        public readonly ?bool $collectShippingDetailsFromWalletConnector = null,
        public readonly ?bool $collectBillingDetailsFromWalletConnector = null,
        public readonly ?bool $isConnectorAgnosticMitEnabled = null,
        public readonly ?array $payoutLinkConfig = null,
        public readonly ?array $outgoingWebhookCustomHttpHeaders = null,
        public readonly ?bool $isTaxConnectorEnabled = null,
        public readonly ?string $taxConnectorId = null,
        public readonly ?bool $isNetworkTokenizationEnabled = null,
        public readonly ?bool $isAutoRetriesEnabled = null,
        public readonly ?int $maxAutoRetriesEnabled = null,
        public readonly ?bool $alwaysRequestExtendedAuthorization = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'profile_name'                                  => $this->profileName,
            'return_url'                                    => $this->returnUrl,
            'enable_payment_response_hash'                  => $this->enablePaymentResponseHash,
            'redirect_to_merchant_with_http_post'           => $this->redirectToMerchantWithHttpPost,
            'webhook_details'                               => $this->webhookDetails,
            'metadata'                                      => $this->metadata,
            'intent_fulfillment_time'                       => $this->intentFulfillmentTime,
            'routing_algorithm'                             => $this->routingAlgorithm,
            'frm_routing_algorithm'                         => $this->frmRoutingAlgorithm,
            'payout_routing_algorithm'                      => $this->payoutRoutingAlgorithm,
            'applepay_verified_domains'                     => $this->applepayVerifiedDomains,
            'session_expiry'                                => $this->sessionExpiry,
            'payment_link_config'                           => $this->paymentLinkConfig,
            'authentication_connector_details'              => $this->authenticationConnectorDetails,
            'use_billing_as_payment_method_billing'         => $this->useBillingAsPaymentMethodBilling,
            'collect_shipping_details_from_wallet_connector'=> $this->collectShippingDetailsFromWalletConnector,
            'collect_billing_details_from_wallet_connector' => $this->collectBillingDetailsFromWalletConnector,
            'is_connector_agnostic_mit_enabled'             => $this->isConnectorAgnosticMitEnabled,
            'payout_link_config'                            => $this->payoutLinkConfig,
            'outgoing_webhook_custom_http_headers'          => $this->outgoingWebhookCustomHttpHeaders,
            'is_tax_connector_enabled'                      => $this->isTaxConnectorEnabled,
            'tax_connector_id'                              => $this->taxConnectorId,
            'is_network_tokenization_enabled'               => $this->isNetworkTokenizationEnabled,
            'is_auto_retries_enabled'                       => $this->isAutoRetriesEnabled,
            'max_auto_retries_enabled'                      => $this->maxAutoRetriesEnabled,
            'always_request_extended_authorization'         => $this->alwaysRequestExtendedAuthorization,
        ]);
    }
}
