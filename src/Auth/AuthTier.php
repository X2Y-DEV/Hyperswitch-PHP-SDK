<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Auth;

/**
 * Hyperswitch authentication tiers.
 *
 * Each Endpoint declares which tier it requires; the ApiClient picks the
 * matching AuthContextResolver to populate request headers.
 */
enum AuthTier: string
{
    /** Admin-tier API key — merchant-account, connector, business-profile management. */
    case ADMIN = 'admin';

    /** Merchant-tier API key — primary key for /payments, /refunds, /customers, etc. */
    case MERCHANT = 'merchant';

    /** Publishable key — client-side, lower-privilege confirms / session tokens. */
    case PUBLISHABLE = 'publishable';

    /** Ephemeral key — short-lived per-customer key for client-side payment-method calls. */
    case EPHEMERAL = 'ephemeral';
}
