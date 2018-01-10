<?php

namespace App;

class Main {
    
    const CONFIG_PATH = 'config/main.config.php';

    private static $instance;
    
    private $config;
    
    private $pdo;

    private $container;
    
    public static function init() {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->configure();
            self::$instance->getPdo();
            self::$instance->initContainer();
        }
        return self::$instance;
    }
    
    private function configure() {
        if (file_exists(self::CONFIG_PATH))
            $this->config = include self::CONFIG_PATH;
        else 
            throw new \App\Exceptions\ConfigNotExistException('Config file not found.');
    }
    
    private function getPdo() {
        if (!isset($this->pdo)) {
            $this->pdo = new \PDO($this->config['db']['dsn'], $this->config['db']['user'], $this->config['db']['pass']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        
        return $this->pdo;
    }
    
    private function initContainer()
    {
        $this->container['MachinesRepository'] = new \App\Repository\Machine($this->getPdo());
    }

    public function run() {
        if (count($_POST)) {
            $response = (new Controller\Server($this->container['MachinesRepository']))
                ->receive($_POST)
                ->response();

            echo $response, "\n";
        } else {
            echo 'Сервер принимает только POST запросы.';
        }
    }
}