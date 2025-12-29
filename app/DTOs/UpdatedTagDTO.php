<?php

namespace App\DTOs;

final readonly class UpdatedTagDTO
{
    public function __construct(
        public ?string $ulid = null,
        public ?string $title = null,
        public int $order = 0,
    ) {}

    /**
     * Create a DTO from an array.
     *
     * @param array<string, mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(
            ulid: $data['ulid'] ?? null,
            title: $data['title'] ?? null,
            order: (int) ($data['order'] ?? 0),
        );
    }

    /**
     * Create multiple DTOs from an array of arrays.
     *
     * @param array<array<string, mixed>> $data
     * @return array<self>
     */
    public static function fromArray(array $data): array
    {
        return array_map(fn(array $item) => self::from($item), $data);
    }
}
