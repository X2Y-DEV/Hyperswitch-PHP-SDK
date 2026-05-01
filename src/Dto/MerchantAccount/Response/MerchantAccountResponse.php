<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\MerchantAccount\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * Response from /accounts create / retrieve / update.
 *
 * @postman             MerchantAccounts / Merchant Account - Retrieve
 * @endpoint            GET /accounts/{id}
 * @hyperswitch-source  2026-04-30
 */
final class MerchantAccountResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $merchantId,
        public readonly ?string $merchantName = null,
        public readonly ?string $defaultProfile = null,
        public readonly ?string $publishableKey = null,
        public readonly ?string $returnUrl = null,
        public readonly ?bool $subMerchantsEnabled = null,
        public readonly ?string $parentMerchantId = null,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = [
            'merchant_id', 'merchant_name', 'default_profile', 'publishable_key',
            'return_url', 'sub_merchants_enabled', 'parent_merchant_id',
        ];

        return new self(
            merchantId:          (string) $data['merchant_id'],
            merchantName:        isset($data['merchant_name'])    ? (string) $data['merchant_name']    : null,
            defaultProfile:      isset($data['default_profile'])  ? (string) $data['default_profile']  : null,
            publishableKey:      isset($data['publishable_key'])  ? (string) $data['publishable_key']  : null,
            returnUrl:           isset($data['return_url'])       ? (string) $data['return_url']       : null,
            subMerchantsEnabled: isset($data['sub_merchants_enabled'])
                ? (bool) $data['sub_merchants_enabled']
                : null,
            parentMerchantId:    isset($data['parent_merchant_id'])
                ? (string) $data['parent_merchant_id']
                : null,
            extras:              array_diff_key($data, array_flip($known)),
        );
    }
}
