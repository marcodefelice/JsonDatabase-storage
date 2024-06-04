<?php
namespace Mdf\JsonStorage\Service;

use DateTime;
use Mdf\JsonStorage\Domain\Model\JsonModelInterface;

/**
 * Class DbService
 * 
 * This class provides methods to interact with a JSON file as a simple database.
 */
class DbService {

   private  const STORAGE_PATH = __DIR__ . '/../../storage/database/';

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
    public function __construct(string $tableName)
    {
        if (!is_dir(self::STORAGE_PATH)) {
            mkdir(self::STORAGE_PATH, 0777, true);
        }

        $this->tableName = $tableName;
        if (!file_exists(self::STORAGE_PATH . $tableName . '.json')) {
            // Create the JSON file
            file_put_contents(self::STORAGE_PATH . $tableName . '.json', '{}');
        }
    }

    /**
     * Get the content of the JSON file as an array.
     * 
     * @return array The content of the JSON file as an array.
     */
    private function getContent(): array
    {
        return (array) json_decode(file_get_contents(self::STORAGE_PATH . $this->tableName . '.json'), true);
    }

    /**
     * Put content into the JSON file.
     * 
     * @param JsonModelInterface $content The content to be put into the JSON file.
     */
    private function putContent(JsonModelInterface $content)
    {
        $currentContent = $this->getContent();

        if(null !== $content->getId()) {
            $id = uniqid();
        }

        $content = [$id => array_merge($content->toArray(), [
            'created_at' => date(DATE_ATOM),
            'updated_at' => date(DATE_ATOM)
        ])];

        $newContent = array_merge($currentContent, $content);
        file_put_contents(self::STORAGE_PATH . $this->tableName . '.json', json_encode($newContent));
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
                $this->update($content['id'], $content->toArray());
                return;
            }
        }
        $this->putContent($content);
    }

    public function update($id, array $content)
    {
        $content['updated_at'] = date(DATE_ATOM);
        $current = $this->getContent();

        // remove old fields
        foreach($content as $key => $value) {
            unset($current[$key]);
        }
        
        $current[$id] = array_merge($current[$id], $content);
        file_put_contents(self::STORAGE_PATH . $this->tableName . '.json', json_encode($current));
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
    public function fetchAll()
    {
        return $this->content;
    }

    public function fetchOne($id)
    {
        return $this->content[$id] ?? null;
    }
}