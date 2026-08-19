<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\MerchantAccount\Response;

use X2Y\HyperSwitchClient\Dto\AbstractResponseDto;

/**
 * @endpoint            DELETE /accounts/{id}
 * @hyperswitch-source  2026-04-30
 */
final class MerchantAccountDeleteResponse extends AbstractResponseDto
{
    /**
     * @param array<string, mixed> $extras
     */
    public function __construct(
        public readonly string $merchantId,
        public readonly bool $deleted,
        array $extras = [],
    ) {
        parent::__construct($extras);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): static
    {
        $known = ['merchant_id', 'deleted'];

        return new self(
            merchantId: (string) ($data['merchant_id'] ?? ''),
            deleted:    (bool)   ($data['deleted'] ?? false),
            extras:     array_diff_key($data, array_flip($known)),
        );
    }
}
