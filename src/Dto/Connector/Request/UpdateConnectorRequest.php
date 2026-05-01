<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Connector\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Update an existing connector configuration. Same field shape as create
 * but every field optional.
 *
 * @postman             PaymentConnectors / Payment Connector - Update
 * @endpoint            POST /account/{account_id}/connectors/{connector_id}
 * @hyperswitch-source  2026-04-30
 */
final class UpdateConnectorRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null      $connectorAccountDetails
     * @param list<array<string, mixed>>|null $paymentMethodsEnabled
     * @param array<string, mixed>|null      $connectorWebhookDetails
     * @param array<string, mixed>|null      $metadata
     * @param array<string, mixed>|null      $additionalMerchantData
     * @param list<string>|null              $businessCountries
     */
    public function __construct(
        public readonly ?string $connectorLabel = null,
        public readonly ?array $connectorAccountDetails = null,
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
            'connector_label'            => $this->connectorLabel,
            'connector_account_details'  => $this->connectorAccountDetails,
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
