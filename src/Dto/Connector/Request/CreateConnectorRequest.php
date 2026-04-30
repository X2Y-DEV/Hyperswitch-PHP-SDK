<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Dto\Connector\Request;

use X2Y\HyperswitchClient\Dto\AbstractRequestDto;

/**
 * Attach a payment connector (Stripe, Adyen, Worldpay, etc.) to a merchant
 * account.
 *
 * `connectorAccountDetails`, `connectorWebhookDetails`, and
 * `paymentMethodsEnabled` shapes are connector-specific — they branch on
 * the chosen connector — so they follow the connector-blob convention and
 * stay loose.
 *
 * @postman             PaymentConnectors / Payment Connector - Create
 * @endpoint            POST /account/{account_id}/connectors
 * @hyperswitch-source  2026-04-30
 */
final class CreateConnectorRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>            $connectorAccountDetails  connector-specific
     * @param list<array<string, mixed>>|null $paymentMethodsEnabled
     * @param array<string, mixed>|null       $connectorWebhookDetails
     * @param array<string, mixed>|null       $metadata
     * @param array<string, mixed>|null       $additionalMerchantData
     * @param list<string>|null               $businessCountries
     */
    public function __construct(
        public readonly string $connectorName,
        public readonly array $connectorAccountDetails,
        public readonly ?string $connectorType = null,
        public readonly ?string $connectorLabel = null,
        public readonly ?string $profileId = null,
        public readonly ?bool $disabled = null,
        public readonly ?bool $testMode = null,
        public readonly ?array $paymentMethodsEnabled = null,
        public readonly ?array $connectorWebhookDetails = null,
        public readonly ?array $metadata = null,
        public readonly ?array $additionalMerchantData = null,
        public readonly ?array $businessCountries = null,
        public readonly ?string $businessLabel = null,
        public readonly ?string $businessSubLabel = null,
        public readonly ?bool $frmConfigsEnabled = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'connector_name'             => $this->connectorName,
            'connector_account_details'  => $this->connectorAccountDetails,
            'connector_type'             => $this->connectorType,
            'connector_label'            => $this->connectorLabel,
            'profile_id'                 => $this->profileId,
            'disabled'                   => $this->disabled,
            'test_mode'                  => $this->testMode,
            'payment_methods_enabled'    => $this->paymentMethodsEnabled,
            'connector_webhook_details'  => $this->connectorWebhookDetails,
            'metadata'                   => $this->metadata,
            'additional_merchant_data'   => $this->additionalMerchantData,
            'business_countries'         => $this->businessCountries,
            'business_label'             => $this->businessLabel,
            'business_sub_label'         => $this->businessSubLabel,
            'frm_configs_enabled'        => $this->frmConfigsEnabled,
        ]);
    }
}
