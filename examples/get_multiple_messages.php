<?php

/*
 * Copyright Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMqdb\Examples;

use PhpMqdb\Client;
use PhpMqdb\Config\TableConfig;
use PhpMqdb\Filter;
use PhpMqdb\Message\MessageFactory;
use PhpMqdb\Query\QueryBuilderFactory;
use PhpMqdb\Repository\PDOMessageRepository;

require_once __DIR__ . '/../vendor/autoload.php';
$dbConf = require_once __DIR__ . '/config.php';

//~ Connection
$connection = new \PDO($dbConf->dsn, $dbConf->user, $dbConf->pass, $dbConf->opts);
$connection->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

//~ Table Config
$tableConfig = new TableConfig();

//~ Factories
$messageFactory      = new MessageFactory($tableConfig);
$queryBuilderFactory = new QueryBuilderFactory($tableConfig);

//~ Repository
$repository = new PDOMessageRepository($connection);
$repository->setMessageFactory($messageFactory);
$repository->setQueryBuilderFactory($queryBuilderFactory);

//~ Client
$client = new Client($repository);

//~ Filter
$filter = (new Filter())->setLimit(5);

//~ Get messages
$messages = $client->getMessages($filter);
foreach ($messages as $message) {
    echo 'Message: ' . var_export($message, true) . PHP_EOL;
    $client->ack($message->getId());
}
