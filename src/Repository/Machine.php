<?php

namespace App\Repository;

class Machine extends AbstractRepository {

    public function getMachine($sn, $params = null) {
        $stmt = $this->pdo->prepare(
            'SELECT * from machines WHERE "serial" = :serial'
        );
        $stmt->execute([
            ':serial' => $sn,
        ]);
        
        $machine = $stmt->fetch(\PDO::FETCH_OBJ);
        if ($machine) {
            if (isset($params['withOptions']) && $params['withOptions']) {
                $stmt = $this->pdo->prepare(
                    'SELECT * from machines_options WHERE "machine_id" = :id'
                );
                $stmt->execute([':id' => $machine->id,]);
                $options = $stmt->fetch(\PDO::FETCH_ASSOC);
                if ($options)
                    foreach ($options as $name => $val) {
                        if (!in_array($name, ['machine_id']))
                        $machine->$name = $val;
                    }
            }
                
        }
            
        return $machine;
    }
    
    public function saveMachineOptions($data, $id = null) {
        $fields = ['firmware', 'connect_freq'];
        $fields = array_intersect($fields, array_keys($data));
        
        $sql = $id ? 
            'UPDATE machines_options SET ' . $this->generateUpdateFieldsString($fields):
            'INSERT INTO machines_options '. $this->generateInsertFieldsString($fields);
            
        $stmt = $this->pdo->prepare($sql);
        $data = array_intersect_key($data, array_flip($fields));
        if (count($data))
            $stmt->execute($data);
        else 
            throw new \App\Exceptions\NothingToUpdateException();

        return $id ?: $this->pdo->lastInsertId('machines_id_seq');
    }
    
    public function saveMachineOptionsSet($data, $id = null) {
        if (isset($data['machine_id']))
            unset($data['machine_id']);
        
        $stmt = $this->pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_name = 'machines_options_set'");
        $stmt->execute();
        $tableFields = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $makeFields = array_diff_key($data, array_flip($tableFields));
        
        if (count($makeFields)) {
            $sql = "ALTER TABLE machines_options_set ";
            $addSql = [];
            foreach ($makeFields as $makeField => $val) {
                //тут можно добавить распознавание типа и создание поля соответстветствующего типа, 
                //для этого ранее в валидации нужно определять тип создаваемых полей, которые отсутвуют в схеме
                $addSql[] = "ADD $makeField VARCHAR(64)";
            }
            $this->pdo->exec($sql.implode(', ', $addSql));
        }
        
        $sql = 'UPDATE machines_options_set SET ' . $this->generateUpdateFieldsString(array_keys($data));
            
        $stmt = $this->pdo->prepare($sql);
        if (count($data))
            $stmt->execute($data);
        else 
            throw new \App\Exceptions\NothingToUpdateException();

        return $id;
    }
    
    public function saveMachine($data, $id = null) {
        $fields = ['serial'];
            
        $sql = $id ? 
            'UPDATE machines SET ' . $this->generateUpdateFieldsString($fields):
            'INSERT INTO machines '. $this->generateInsertFieldsString($fields);
            
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute(array_intersect_key($data, array_flip($fields)));

        return $id ?: $this->pdo->lastInsertId('machines_id_seq');
    }

}
