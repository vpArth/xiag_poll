<?php

use Ramsey\Uuid\Uuid;
use Xiag\Poll\Data\CrudDb;
use Xiag\Poll\Data\CrudDbInterface;
use Xiag\Poll\Data\DataProvider;
use Xiag\Poll\Data\DataProviderInterface;
use Xiag\Poll\Data\PdoDB;
use Xiag\Poll\Data\SqlDbInterface;
use Xiag\Poll\Util\JsonRequestDecorator;
use Xiag\Poll\Util\Request;
use Xiag\Poll\Util\RequestInterface;
use Xiag\Poll\Util\UniqIdGenInterface;

return [
    RequestInterface::class      => static function () {
      $request = new Request();
      if (preg_match('#^application/json#', $request->getHeaders()['Content-Type'] ?? '')) {
        $request = new JsonRequestDecorator($request);
      }

      return $request;
    },
    SqlDbInterface::class        => static function () {
      $pdo = new PDO($_ENV['DB_DSN'], $_ENV['DB_USER'], $_ENV['DB_PASS'],
          $_ENV['DB_OPTIONS'] ? preg_split('#\s*,\s*#', $_ENV['DB_OPTIONS']) : null);
      return new PdoDB($pdo);
    },
    UniqIdGenInterface::class    => static function () {
      return new class implements UniqIdGenInterface {
        public function generate(): string
        {
          return Uuid::uuid4()->toString();
        }
      };
    },
    CrudDbInterface::class       => DI\autowire(CrudDb::class),
    DataProviderInterface::class => DI\autowire(DataProvider::class),
];
