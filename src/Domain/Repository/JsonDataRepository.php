<?php
namespace Mdf\JsonStorage\Domain\Repository;

use DateTime;
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

    public function update(string|int $id, array $data): void
    {
        $this->dbService->update($id, $data);
    }

    public function deactivate($id)
    {
        $deactivateAt = date(DATE_ATOM);
        $this->dbService->update($id, ['deactivated_at' => $deactivateAt]);
    }

    public function get(int|string $id): array
    {
        return $this->dbService->createQuery()->where('id', $id)->fetchAll();
    }
    
}