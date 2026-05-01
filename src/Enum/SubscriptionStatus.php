<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Enum;

/**
 * Subscription lifecycle status.
 *
 * @postman             Subscriptions / Subscription - Retrieve (status)
 * @hyperswitch-source  2026-04-30
 */
enum SubscriptionStatus: string implements LabelledEnum
{
    case CREATED   = 'created';
    case ACTIVE    = 'active';
    case PAUSED    = 'paused';
    case CANCELLED = 'cancelled';
    case PENDING   = 'pending';
    case TRIALING  = 'trialing';
    case PAST_DUE  = 'past_due';
    case UNPAID    = 'unpaid';

    public function label(): string
    {
        return match ($this) {
            self::CREATED   => 'Created',
            self::ACTIVE    => 'Active',
            self::PAUSED    => 'Paused',
            self::CANCELLED => 'Cancelled',
            self::PENDING   => 'Pending',
            self::TRIALING  => 'Trialing',
            self::PAST_DUE  => 'Past due',
            self::UNPAID    => 'Unpaid',
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::CANCELLED;
    }
}
