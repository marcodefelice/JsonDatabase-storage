<?php
namespace Mdf\JsonStorage\Domain\Model;

use DateTime;
use Mdf\JsonStorage\Domain\Repository\GiftCardRepository;
use Mdf\JsonStorage\Helpers\Repository;

interface JsonModelInterface {

    public function toArray(): array;

    /**
     * Get the value of id
     *
     * @return string
     */
    public function getId(): string;
}