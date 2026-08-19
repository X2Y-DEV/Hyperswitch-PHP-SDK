<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * HyperSwitch webhook event types. The set is open-ended — HyperSwitch can
 * ship new event types at any time, so consumers should always retain the
 * raw string from `WebhookEvent::$eventTypeRaw` and treat the enum as an
 * ergonomic lookup that may return `null` for unknown types.
 *
 * Use `EventType::tryFrom(...)` rather than `EventType::from(...)` when
 * decoding inbound events.
 *
 * @hyperswitch-source  2026-04-30
 */
enum EventType: string implements LabelledEnum
{
    // Payments
    case PAYMENT_SUCCEEDED            = 'payment_succeeded';
    case PAYMENT_FAILED               = 'payment_failed';
    case PAYMENT_PROCESSING           = 'payment_processing';
    case PAYMENT_CANCELLED            = 'payment_cancelled';
    case PAYMENT_AUTHORIZED           = 'payment_authorized';
    case PAYMENT_CAPTURED             = 'payment_captured';
    case PAYMENT_EXPIRED              = 'payment_expired';
    case ACTION_REQUIRED              = 'action_required';

    // Refunds
    case REFUND_SUCCEEDED = 'refund_succeeded';
    case REFUND_FAILED    = 'refund_failed';

    // Disputes
    case DISPUTE_OPENED      = 'dispute_opened';
    case DISPUTE_EXPIRED     = 'dispute_expired';
    case DISPUTE_ACCEPTED    = 'dispute_accepted';
    case DISPUTE_CANCELLED   = 'dispute_cancelled';
    case DISPUTE_CHALLENGED  = 'dispute_challenged';
    case DISPUTE_WON         = 'dispute_won';
    case DISPUTE_LOST        = 'dispute_lost';

    // Mandates
    case MANDATE_ACTIVE  = 'mandate_active';
    case MANDATE_REVOKED = 'mandate_revoked';

    // Payouts
    case PAYOUT_SUCCESS   = 'payout_success';
    case PAYOUT_FAILED    = 'payout_failed';
    case PAYOUT_PROCESSING = 'payout_processing';
    case PAYOUT_CANCELLED = 'payout_cancelled';
    case PAYOUT_INITIATED = 'payout_initiated';
    case PAYOUT_EXPIRED   = 'payout_expired';
    case PAYOUT_REVERSED  = 'payout_reversed';

    // Subscriptions
    case SUBSCRIPTION_CREATED            = 'subscription_created';
    case SUBSCRIPTION_ACTIVE             = 'subscription_active';
    case SUBSCRIPTION_PAUSED             = 'subscription_paused';
    case SUBSCRIPTION_RESUMED            = 'subscription_resumed';
    case SUBSCRIPTION_CANCELLED          = 'subscription_cancelled';
    case SUBSCRIPTION_RENEWAL_SUCCEEDED  = 'subscription_renewal_succeeded';
    case SUBSCRIPTION_RENEWAL_FAILED     = 'subscription_renewal_failed';

    public function label(): string
    {
        return match ($this) {
            self::PAYMENT_SUCCEEDED            => 'Payment succeeded',
            self::PAYMENT_FAILED               => 'Payment failed',
            self::PAYMENT_PROCESSING           => 'Payment processing',
            self::PAYMENT_CANCELLED            => 'Payment cancelled',
            self::PAYMENT_AUTHORIZED           => 'Payment authorized',
            self::PAYMENT_CAPTURED             => 'Payment captured',
            self::PAYMENT_EXPIRED              => 'Payment expired',
            self::ACTION_REQUIRED              => 'Customer action required',
            self::REFUND_SUCCEEDED             => 'Refund succeeded',
            self::REFUND_FAILED                => 'Refund failed',
            self::DISPUTE_OPENED               => 'Dispute opened',
            self::DISPUTE_EXPIRED              => 'Dispute expired',
            self::DISPUTE_ACCEPTED             => 'Dispute accepted',
            self::DISPUTE_CANCELLED            => 'Dispute cancelled',
            self::DISPUTE_CHALLENGED           => 'Dispute challenged',
            self::DISPUTE_WON                  => 'Dispute won',
            self::DISPUTE_LOST                 => 'Dispute lost',
            self::MANDATE_ACTIVE               => 'Mandate active',
            self::MANDATE_REVOKED              => 'Mandate revoked',
            self::PAYOUT_SUCCESS               => 'Payout succeeded',
            self::PAYOUT_FAILED                => 'Payout failed',
            self::PAYOUT_PROCESSING            => 'Payout processing',
            self::PAYOUT_CANCELLED             => 'Payout cancelled',
            self::PAYOUT_INITIATED             => 'Payout initiated',
            self::PAYOUT_EXPIRED               => 'Payout expired',
            self::PAYOUT_REVERSED              => 'Payout reversed',
            self::SUBSCRIPTION_CREATED         => 'Subscription created',
            self::SUBSCRIPTION_ACTIVE          => 'Subscription active',
            self::SUBSCRIPTION_PAUSED          => 'Subscription paused',
            self::SUBSCRIPTION_RESUMED         => 'Subscription resumed',
            self::SUBSCRIPTION_CANCELLED       => 'Subscription cancelled',
            self::SUBSCRIPTION_RENEWAL_SUCCEEDED => 'Subscription renewal succeeded',
            self::SUBSCRIPTION_RENEWAL_FAILED    => 'Subscription renewal failed',
        };
    }
}
