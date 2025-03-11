<?php
namespace Mdf\JsonStorage\Service;

use DateTime;
use Mdf\JsonStorage\Domain\Model\JsonModelInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class DbService
 * 
 * This class provides methods to interact with a JSON file as a simple database.
 */
class DbService {

    private string $storagePath;
    /**
     * The name of the table.
     * 
     * @var string
     */
    private $tableName;

    public array $content = [];

    /**
     * DbService constructor.
     * 
     * Creates a new instance of the DbService class.
     * If the storage directory does not exist, it will be created.
     * If the JSON file for the given table does not exist, it will be created with an empty object.
     * 
     * @param string $tableName The name of the table.
     */
    public function __construct(string $storagePath)
    {
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }
        $this->storagePath = $storagePath;
    }

    /**
     * Creates a new instance of the DbService class.
     *
     * This static method serves as a factory for creating new DbService instances
     * with the specified storage path.
     *
     * @param string $storagePath The path where the database files will be stored
     * @param string $tableName The name of the table
     * @return self A new instance of DbService
     */
    public static function createInstance(string $storagePath, string $tableName): self
    {
        $instance = new self($storagePath);
        $instance->setTableName($tableName);
        return $instance;
    }

    /**
     * Sets the table name for the database operations.
     *
     * @param string $tableName The name of the table to be used
     * @return self Returns instance of self for method chaining
     */
    public function setTableName(string $tableName): self
    {

        $this->tableName = $tableName;
        if (!file_exists($this->storagePath . $tableName . '.json')) {
            // Create the JSON file
            $file = file_put_contents($this->storagePath. $tableName . '.json', '{[]}');

            if ($file === false) {
                throw new JsonDbServiceException('Could not create the JSON file.');
            }
        }

        return $this;
    }

    /**
     * Get the content of the JSON file as an array.
     * 
     * @return array The content of the JSON file as an array.
     */
    private function getContent(): array
    {
        return (array) json_decode(file_get_contents($this->storagePath. $this->tableName . '.json'), true);
    }

    /**
     * Put content into the JSON file.
     * 
     * @param JsonModelInterface $content The content to be put into the JSON file.
     */
    private function putContent(JsonModelInterface $content)
    {
        $currentContent = $this->getContent();

        if(null === $content->getId()) {
            throw new JsonDbServiceException('The content must have an ID.');
        }

        $content->setCreatedAt(new DateTime());
        $content->setUpdatedAt(new DateTime());
        
        $newContent[$content->getId()] = $content->__toArray();
        $newContent = array_merge($currentContent, $newContent);

        file_put_contents($this->storagePath. $this->tableName . '.json', json_encode($newContent));
    }

    /**
     * Inserts the given content into the database.
     *
     * @param JsonModelInterface $content The content to be inserted.
     * @return void
     */
    public function insert(JsonModelInterface $content)
    {
        // ceck if already exist
        $current = $this->getContent();
        foreach($current as $item) {
            if ($item['id'] == $content->getId()) {
                $this->update($content['id'], $content);
                return;
            }
        }
        $this->putContent($content);
    }

    /**
     * Updates a record in the database.
     *
     * @param mixed $id The identifier of the record to update
     * @param JsonModelInterface $content The new content to update the record with
     * @return mixed Returns the updated record or false on failure
     */
    public function update($id, JsonModelInterface $content)
    {
        $content->setUpdatedAt(new DateTime());
        $current = $this->getContent();

        // remove old fields
        foreach($content as $key => $value) {
            unset($current[$key]);
        }
        
        $current[$id] = array_merge($current[$id], $content);
        file_put_contents($this->storagePath. $this->tableName . '.json', json_encode($current));
    }

    /**
     * Creates a new query object.
     *
     * @return self
     */
    public function createQuery(): self
    {
        $this->content = $this->getContent();
        return $this;
    }

    /**
     * Selects data from the database based on the specified fields.
     *
     * @param array $fields The fields to select.
     * @return array The selected data.
     */
    public function select(array $fields): self
    {
        $this->content = array_map(function ($item) use ($fields) {
            return array_filter($item, function ($key) use ($fields) {
                return in_array($key, $fields);
            }, ARRAY_FILTER_USE_KEY);
        }, $this->content);

        return $this;
    }

    /**
     * Example usage of select method:
     * 
     * $db = new DbService('/path/to/storage/');
     * $db->setTableName('users');
     * 
     * // This will return only id and name fields from all records
     * $users = $db->createQuery()
     *            ->select(['id', 'name'])
     *            ->fetchAll();
     *
     * // This will return only email field from users where role is 'admin'
     * $adminEmails = $db->createQuery()
     *                  ->where('role', 'admin')
     *                  ->select(['email'])
     *                  ->fetchAll();
     */

    public function get(string|int $id) 
    {
        return $this->content[$id] ?? null;
    }

    public function where(string $key, string $value)
    {
        $results = [];
        foreach($this->content as $_ => $item) {
            if (@$item[$key] == $value){
                $results[] = $item;
            }
        }

        $this->content = $results;

        return $this;
    }
    
    /**
     * Fetches all records from the database.
     *
     * @return array An array of records fetched from the database.
     */
    public function fetchAll(): array
    {
        return $this->content;
    }

    public function fetchOne($id)
    {
        return $this->content[$id] ?? null;
    }

    public function delete(string $id): void
    {
        $current = $this->getContent();
        unset($current[$id]);
        file_put_contents($this->storagePath. $this->tableName . '.json', json_encode($current));
    }
}