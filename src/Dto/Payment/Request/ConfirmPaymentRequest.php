<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\Payment\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;
use X2Y\HyperSwitchClient\Dto\Common\Address;
use X2Y\HyperSwitchClient\Dto\Common\BillingDetails;
use X2Y\HyperSwitchClient\Enum\AuthenticationType;
use X2Y\HyperSwitchClient\Enum\CaptureMethod;
use X2Y\HyperSwitchClient\Enum\Currency;
use X2Y\HyperSwitchClient\Enum\SetupFutureUsage;

/**
 * Confirm a previously-created payment intent. Two distinct shapes hit the
 * same `POST /payments/{id}/confirm` endpoint:
 *
 *   - Rich confirm — includes the same fields a create body would carry
 *     (payment_method_data, billing, shipping, customer, etc.).
 *   - Confirm-with-token — a thin body using a previously-vaulted
 *     payment_token plus client-side fields (clientSecret, cardCvc,
 *     browserInfo).
 *
 * One DTO covers both: leave the fields you don't need at null. The Resource
 * binds either form to the same Endpoint.
 *
 * `extras` accepts any field the SDK hasn't typed yet.
 *
 * @endpoint            POST /payments/{id}/confirm
 * @hyperswitch-source  2026-04-30
 */
final class ConfirmPaymentRequest extends AbstractRequestDto
{
    /**
     * @param list<array<string, mixed>>|null $orderDetails
     * @param array<string, mixed>|null       $customer
     * @param array<string, mixed>|null       $paymentMethodData
     * @param array<string, mixed>|null       $browserInfo
     * @param array<string, mixed>|null       $customerAcceptance
     * @param array<string, mixed>|null       $connectorMetadata
     * @param array<string, mixed>|null       $merchantConnectorDetails
     * @param array<string, mixed>|null       $routing
     * @param array<string, mixed>|null       $mandateData
     * @param array<string, mixed>|null       $recurringDetails
     * @param list<string>|null               $allowedPaymentMethodTypes
     * @param array<string, mixed>|null       $metadata
     * @param array<string, mixed>            $extras
     */
    public function __construct(
        public readonly ?string $clientSecret = null,
        public readonly ?int $amount = null,
        public readonly ?Currency $currency = null,
        public readonly ?CaptureMethod $captureMethod = null,
        public readonly ?AuthenticationType $authenticationType = null,
        public readonly ?SetupFutureUsage $setupFutureUsage = null,
        public readonly ?int $amountToCapture = null,
        public readonly ?string $customerId = null,
        public readonly ?array $customer = null,
        public readonly ?string $description = null,
        public readonly ?string $returnUrl = null,
        public readonly ?string $profileId = null,
        public readonly ?string $paymentMethod = null,
        public readonly ?string $paymentMethodType = null,
        public readonly ?array $paymentMethodData = null,
        public readonly ?string $paymentToken = null,
        public readonly ?string $cardCvc = null,
        public readonly ?BillingDetails $billing = null,
        public readonly ?Address $shipping = null,
        public readonly ?array $orderDetails = null,
        public readonly ?array $browserInfo = null,
        public readonly ?array $customerAcceptance = null,
        public readonly ?array $connectorMetadata = null,
        public readonly ?array $merchantConnectorDetails = null,
        public readonly ?array $routing = null,
        public readonly ?array $mandateData = null,
        public readonly ?array $recurringDetails = null,
        public readonly ?array $allowedPaymentMethodTypes = null,
        public readonly ?bool $offSession = null,
        public readonly ?array $metadata = null,
        public readonly array $extras = [],
    ) {
    }

    public function toArray(): array
    {
        $typed = $this->filterNulls([
            'client_secret'                => $this->clientSecret,
            'amount'                       => $this->amount,
            'currency'                     => $this->currency?->value,
            'capture_method'               => $this->captureMethod?->value,
            'authentication_type'          => $this->authenticationType?->value,
            'setup_future_usage'           => $this->setupFutureUsage?->value,
            'amount_to_capture'            => $this->amountToCapture,
            'customer_id'                  => $this->customerId,
            'customer'                     => $this->customer,
            'description'                  => $this->description,
            'return_url'                   => $this->returnUrl,
            'profile_id'                   => $this->profileId,
            'payment_method'               => $this->paymentMethod,
            'payment_method_type'          => $this->paymentMethodType,
            'payment_method_data'          => $this->paymentMethodData,
            'payment_token'                => $this->paymentToken,
            'card_cvc'                     => $this->cardCvc,
            'billing'                      => $this->billing?->toArray(),
            'shipping'                     => $this->shipping?->toArray(),
            'order_details'                => $this->orderDetails,
            'browser_info'                 => $this->browserInfo,
            'customer_acceptance'          => $this->customerAcceptance,
            'connector_metadata'           => $this->connectorMetadata,
            'merchant_connector_details'   => $this->merchantConnectorDetails,
            'routing'                      => $this->routing,
            'mandate_data'                 => $this->mandateData,
            'recurring_details'            => $this->recurringDetails,
            'allowed_payment_method_types' => $this->allowedPaymentMethodTypes,
            'off_session'                  => $this->offSession,
            'metadata'                     => $this->metadata,
        ]);

        return $this->mergeExtras($typed, $this->extras);
    }
}
