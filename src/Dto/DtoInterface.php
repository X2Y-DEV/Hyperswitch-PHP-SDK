<?php
declare(strict_types=1);

namespace X2Y\HyperSwitchClient\Dto;

/**
 * Marker interface for every DTO.
 *
 * Concrete DTOs extend `AbstractRequestDto` (with `toArray()`) or
 * `AbstractResponseDto` (with `fromArray()`). This interface lets callers
 * accept "any DTO" without distinguishing direction.
 */
interface DtoInterface
{
}
