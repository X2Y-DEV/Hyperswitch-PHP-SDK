<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Dispute\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * Submit evidence files / fields for an open dispute.
 *
 * The fields are file references (uploaded separately via the file API)
 * keyed by evidence type: `refund_policy`, `customer_communication`,
 * `receipt`, `service_documentation`, etc. Field set is open-ended;
 * `extras` accepts any evidence type the SDK has not typed.
 *
 * @endpoint            POST /disputes/evidence
 * @hyperswitch-source  2026-04-30
 */
final class SubmitEvidenceRequest extends AbstractRequestDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $disputeId,
        public readonly ?string $refundPolicy = null,
        public readonly ?string $cancellationPolicy = null,
        public readonly ?string $customerCommunication = null,
        public readonly ?string $customerSignature = null,
        public readonly ?string $productDescription = null,
        public readonly ?string $receipt = null,
        public readonly ?string $serviceDocumentation = null,
        public readonly ?string $shippingDocumentation = null,
        public readonly ?string $invoiceShowingDistinctTransactions = null,
        public readonly ?string $recurringTransactionAgreement = null,
        public readonly ?string $uncategorizedFile = null,
        public readonly ?string $uncategorizedText = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'dispute_id'                            => $this->disputeId,
            'refund_policy'                         => $this->refundPolicy,
            'cancellation_policy'                   => $this->cancellationPolicy,
            'customer_communication'                => $this->customerCommunication,
            'customer_signature'                    => $this->customerSignature,
            'product_description'                   => $this->productDescription,
            'receipt'                               => $this->receipt,
            'service_documentation'                 => $this->serviceDocumentation,
            'shipping_documentation'                => $this->shippingDocumentation,
            'invoice_showing_distinct_transactions' => $this->invoiceShowingDistinctTransactions,
            'recurring_transaction_agreement'       => $this->recurringTransactionAgreement,
            'uncategorized_file'                    => $this->uncategorizedFile,
            'uncategorized_text'                    => $this->uncategorizedText,
        ]);
        return $this->mergeExtras($typed, $this->extras);
    }
}
