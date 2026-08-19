<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Auth;

/**
 * HyperSwitch authentication tiers.
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

    /**
     * V1 merchant auth — same merchant key, but HyperSwitch's V1 endpoints
     * (paths under `/v1/payment-methods` and `/v1/payment-method-sessions`)
     * expect it as `Authorization: api-key=<key>` rather than the standard
     * `api-key: <key>` header. V1 endpoints additionally require an
     * `X-Profile-Id` header — see Resource::withProfileId().
     */
    case MERCHANT_V1 = 'merchant_v1';

    /**
     * JWT bearer auth — dashboard/JWT-scoped endpoints (event management, some
     * routing reads). Sent as `Authorization: Bearer <token>`. See
     * JwtKeyAuthResolver.
     */
    case JWT = 'jwt';
}
