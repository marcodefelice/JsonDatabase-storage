<?php
namespace Mdf\JsonStorage\Domain\Repository;

use Mdf\JsonStorage\Service\DbService;
use Mdf\JsonStorage\Domain\Model\JsonModelInterface;

interface RepositoryInterface {

    public function save(JsonModelInterface $data);

    public function find(string $key, string $value): array;
    
    public function update(string|int $id, array $data);

    public function get(int|string $id): array;
}