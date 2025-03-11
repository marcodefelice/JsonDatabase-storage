<?php
namespace Mdf\JsonStorage\Domain\Model;

use DateTime;
use Mdf\JsonStorage\Domain\Repository\GiftCardRepository;
use Mdf\JsonStorage\Helpers\Repository;

interface JsonModelInterface {

    public function __toArray(): array;

    public function __toString(): string;

    /**
     * Get the value of id
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Get the value of createdAt
     *
     * @return string
     */
    public function getCreatedAt(): string;

    /**
     * Get the value of updatedAt
     *
     * @return string
     */
    public function getUpdatedAt(): string;

    /**
     * Set the value of createdAt
     *
     * @param DateTime $createdAt
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt): self;

    /**
     * Set the value of updatedAt
     *
     * @param DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt): self;

    /**
     * Creates a new instance of the JSON model from an array of data.
     *
     * @param array $data The data to populate the model with
     * @return JsonModelInterface The newly created model instance
     */
    public static function create(array $data): JsonModelInterface;
}