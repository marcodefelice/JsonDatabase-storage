<?php
namespace Mdf\JsonStorage\Domain\Model;

use DateTime;
use Ramsey\Uuid\Uuid;

class JsonModel implements JsonModelInterface {

    protected string $id;
    protected string $createdAt;
    protected string $updatedAt;

    protected function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the value of createdAt
     *
     * @param DateTime $createdAt
     * @return self
     */
    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt->format(DateTime::ATOM);

        return $this;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * Set the value of updatedAt
     *
     * @param DateTime $updatedAt
     * @return self
     */
    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt->format(DateTime::ATOM);

        return $this;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function __clone()
    {
        $this->id = Uuid::uuid4();
    }

    public static function create(array $data): JsonModelInterface
    {
        $model = new self();
        $model->setCreatedAt(new DateTime());
        $model->setUpdatedAt(new DateTime());

        return $model;
    }

    public function __toArray(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties();
        $result = [];

        foreach ($properties as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            $value = $property->getValue($this);
            
            // Handle nested JsonModel objects
            if ($value instanceof JsonModelInterface) {
                /** @var JsonModelInterface $value */
                $result[$propertyName] = $value->__toArray();
            } 
            // Handle arrays of JsonModel objects
            elseif (is_array($value)) {
                $result[$propertyName] = array_map(function($item) {
                    return ($item instanceof JsonModelInterface) ? $item->__toArray() : $item;
                }, $value);
            } 
            // Handle all other properties
            else {
                $result[$propertyName] = $value;
            }
        }

        return $result;
    }

    public function __toString(): string
    {
        return json_encode($this->__toArray());
    }
}