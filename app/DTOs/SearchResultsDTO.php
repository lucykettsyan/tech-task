<?php


namespace App\DTOs;

class SearchResultsDTO
{
    public function transform(array $data): array
    {
        return array_map(
            fn($item) => $this->transformObject($item),
            $data
        );
    }

    public function transformObject(array $data): array
    {
        return [
            'title' => $data['title'] ?? null,
            'object_url' => $data['objectURL'] ?? null
        ];
    }
}