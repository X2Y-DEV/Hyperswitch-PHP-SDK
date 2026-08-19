<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto\ApiKey\Request;

use X2Y\HyperSwitchClient\Dto\AbstractRequestDto;

/**
 * @endpoint            POST /api_keys/{merchant_id}
 * @hyperswitch-source  2026-04-30
 */
final class CreateApiKeyRequest extends AbstractRequestDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly ?\DateTimeImmutable $expiration = null,
    ) {
    }

    public function toArray(): array
    {
        return $this->filterNulls([
            'name'        => $this->name,
            'description' => $this->description,
            'expiration'  => $this->expiration?->format(\DateTimeInterface::ATOM),
        ]);
    }
}
