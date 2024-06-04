<?php
namespace Mdf\JsonStorage\Helpers;

use Mdf\JsonStorage\Domain\Repository\GiftCardRepository;
use Mdf\JsonStorage\Domain\Repository\RepositoryInterface;

trait Repository
{
    protected RepositoryInterface $repository;
    abstract public function toArray();

    abstract public function getId(): string|int;

    public function save(): void
    {
        $dbService = $this->repository;
        $dbService->save($this->toArray());
    }

    public function update(array $data): void
    {
        $dbService = $this->repository;
        $dbService->update($this->getId(), $data);
    }

    public static function find(RepositoryInterface $repository, string $key, string $value): self|null
    {
        $data = $repository->find($key, $value);
        if(empty($data)) {
            return null;
        }

        $data = $data[0];
        return new self($data);
    }

    public static function get(RepositoryInterface $repository, string|int $id): self
    {
        $data = $repository->get($id);

        if(empty($data)) {
            return null;
        }

        $data = $data[0];
        return new self($data);
    }
}