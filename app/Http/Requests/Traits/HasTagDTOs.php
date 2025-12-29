<?php

namespace App\Http\Requests\Traits;

use App\DTOs\UpdatedTagDTO;

trait HasTagDTOs
{
    /**
     * Get the validated tags as DTOs.
     *
     * @return array<UpdatedTagDTO>
     */
    public function getTagDTOs(): array
    {
        return UpdatedTagDTO::fromArray($this->validated('tags'));
    }
}
