<?php 

namespace App\Repository;

abstract class AbstractRepository {
    
    protected $pdo;
    
    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }
    
    protected function generateUpdateFieldsString(array $fields){
        return implode(', ', array_map(function ($field) {
            return sprintf('"%s" = :%s', $field, $field);
        }, $fields));
    }
    
    protected function generateInsertFieldsString(array $fields){
        return 
        sprintf('(%s)', implode(', ', array_map(function ($field) {
            return sprintf('"%s"', $field);
        }, $fields)))
        ." VALUES "
        . sprintf('(%s)', implode(', ', array_map(function ($field) {
            return sprintf(':%s', $field);
        }, $fields)));
    }
}