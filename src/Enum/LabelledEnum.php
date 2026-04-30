<?php
declare(strict_types=1);

namespace X2Y\HyperswitchClient\Enum;

/**
 * Implemented by every enum in the SDK.
 *
 * `label()` returns a short human-readable description of the case — useful for
 * admin UIs that render dropdowns / selects from enum values without the consumer
 * needing a separate translation map.
 *
 * Translations are the consumer's responsibility; the labels here are the
 * canonical English strings.
 */
interface LabelledEnum
{
    public function label(): string;
}
