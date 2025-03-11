<?php
namespace Mdf\JsonStorage\Domain\Repository;

use Mdf\JsonStorage\Domain\Model\JsonModelInterface;
use Mdf\JsonStorage\Service\DbService;

class JsonDataRepository implements RepositoryInterface {

    private DbService $dbService;

    public function __construct()
    {
        $this->dbService = new DbService('giftcards');
    }

    public function save(JsonModelInterface $data): void
    {
        $this->dbService->insert($data);
    }

    public function find(string $key, string $value): array
    {
        return $this->dbService->createQuery()->where($key, $value)->fetchAll();
    }

    public function update(string|int $id, JsonModelInterface $data): void
    {
        $this->dbService->update($id, $data);
    }

    public function delete($id)
    {
        $this->dbService->delete($id);
    }

    public function get(int|string $id): array
    {
        return $this->dbService->createQuery()->where('id', $id)->fetchAll();
    }
    
}