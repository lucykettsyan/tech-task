<?php 

namespace App\Contracts;

interface SearchInterface {
    public function search(string $department_id, string $query): array;

    public function getDepartments(): array;

}