<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\PaymentMethod\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Enum\PaymentMethodAckStatus;

/**
 * Update a saved payment method — e.g. updating cardholder name, nickname,
 * or recording the network transaction id and acknowledgement status from
 * a CIT challenge.
 *
 * @endpoint            PUT /v1/payment-methods/{id}/update-saved-payment-method
 * @hyperswitch-source  2026-04-30
 */
final class UpdatePaymentMethodRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed>|null $paymentMethodData
     * @param array<string, mixed>|null $connectorTokenDetails
     * @param array<string, mixed>      $extras
     */
    public function __construct(
        public readonly ?array $paymentMethodData = null,
        public readonly ?array $connectorTokenDetails = null,
        public readonly ?string $networkTransactionId = null,
        public readonly ?PaymentMethodAckStatus $acknowledgementStatus = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'payment_method_data'     => $this->paymentMethodData,
            'connector_token_details' => $this->connectorTokenDetails,
            'network_transaction_id'  => $this->networkTransactionId,
            'acknowledgement_status'  => $this->acknowledgementStatus?->value,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
