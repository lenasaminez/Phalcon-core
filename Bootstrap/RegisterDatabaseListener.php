<?php

namespace Core\Bootstrap;

class RegisterDatabaseListener
{
    protected function afterMergeConfig($event, $application)
    {
        $di = $application->getDI();
        $config = $di->get('config');
        if (isset($config['databases']) && $config['databases']) {
            foreach ($config['databases'] as $k => $database) {
                $db_name = 'db_' . $k;
                $di->set($db_name, function () use ($database) {

                    $config = [
                        'host' => $database->host,
                        'dbname' => $database->dbname,
                        'username' => $database->username,
                        'password' => $database->password,
                    ];

                    if (isset($database->port)) {
                        $config['port'] = (int) $database->port;
                    }

                    //Faz a conexão propriamente dita com o banco de dados
                    if ('Postgresql' == $database->adapter) {
                        $connection = new Phalcon\Db\Adapter\Pdo\Postgresql($config);
                    }
                    if ('Mongodb' == $database->adapter) {
                        $mongo = new MongoClient('mongodb://' . $database->username . ':' . $database->password . '@' . $database->host);
                        $connection = $mongo->selectDB($database->dbname);
                    } else {
                        $connection = new \Phalcon\Db\Adapter\Pdo\Mysql($config);
                    }

                    return $connection;
                });
            }
        }
    }
}
