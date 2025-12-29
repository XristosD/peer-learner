<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class NoteDataDTO
{
    public Collection $data;

    public function __construct()
    {
        $this->data = collect([]);
    }

    public function setBody(string $body): self
    {
        $this->data = $this->data->put('body', $body);
        return $this;
    }

    public function setDetails(?string $details): self
    {
        $this->data = $this->data->put('details', $details);
        return $this;
    }

    public function getBody(): string
    {
        return $this->data->get('body');
    }

    public function getDetails(): ?string
    {
        return $this->data->get('details');
    }

    public function hasBody(): bool
    {
        return $this->data->has('body');
    }

    public function hasDetails(): bool
    {
        return $this->data->has('details');
    }

    public function toArray(): array
    {
        return $this->data->toArray();
    }
}