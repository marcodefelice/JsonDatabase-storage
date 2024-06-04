# JsonDatabase-storage
Database storage on file system with files JSON and PHP

## Usage

Implements some models with Mdf\JsonStorage\Domain\Model\JsonModelInterface;

You can use a Mdf\JsonStorage\Helpers\Repository trait in yopur model
````
YourModel::get(1);
```

You can use DbService Mdf\JsonStorage\Service\DbService;
````
use Mdf\JsonStorage\Service\DbService;

$service = new DbService("modelname");
$service->insert($yourModel);
```

## Contributing

Contributions are welcome! If you find any issues or have suggestions, please feel free to open an issue or submit a pull request.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more details.