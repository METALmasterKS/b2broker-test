<?php

namespace App\Controller;

class Server {
    /**
     *
     * @var \App\Repository\Machine 
     */
    private $machinesRepository;

    private $errors;
    
    private $data = [];
    
    private $response = [];

    public function __construct(\App\Repository\Machine $machinesRepository) {
        $this->machinesRepository = $machinesRepository;
    }
    
    public function receive($params) {
        if ($this->isValid($params)) {
            $machine = $this->machinesRepository->getMachine($this->data['serial'], [ 'withOptions' => true, ]);
            if ($machine === false)
                throw new \App\Exceptions\MachineException('Machine not found, check serial');

            $diff = array_udiff_assoc($this->data, get_object_vars($machine), function ($a, $b){
                return $a !== $b;
            });
            if (isset($diff['sets']))
                unset($diff['sets']);
            
            try {
                $this->machinesRepository->saveMachineOptions($diff, $machine->id);
                
                $this->response = array_merge(
                    $this->response, 
                    \App\Utils\Functions::array_map_keys([self, 'addSetPrefix'], $diff)
                );
            } catch (\App\Exceptions\NothingToUpdateException $e) {
                
            }
            
            try {
                $this->machinesRepository->saveMachineOptionsSet($this->data['sets'], $machine->id);
                
                $this->response = array_merge(
                    $this->response, 
                    \App\Utils\Functions::array_map_keys([self, 'addSetPrefix'], $this->data['sets'])
                );
            } catch (\App\Exceptions\NothingToUpdateException $e) {
                
            }

        }
        
        return $this;
    }
    
    public function response() {
        $this->response['time'] = new \DateTime();
        $this->response['status'] = 'ok';
        
        return json_encode($this->response);
    }
    
    private function isValid($params) {
        if (isset($params['serial']) && mb_strlen($params['serial']) == 15)
            $this->data['serial'] = (int) $params['serial'];
        else
            throw new \App\Exceptions\SerialException('Invalid serial string.');
        
        if (isset($params['time']) && preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}\s[0-9]{2}:[0-9]{2}:[0-9]{2}$/', $params['time'])) {
            //$this->data['time'] = new \DateTime($params['time']); // не вижу смысла передавать time, оно в базе не используется
        } else
            throw new \App\Exceptions\TimeException('Invalid time string.');
        
        if (isset($params['connect_freq']) ) {
            if (preg_match('/^[0-9]{1,2}$/', $params['connect_freq']) ) 
                $this->data['connect_freq'] = (int) $params['connect_freq'];
            else
                throw new \App\Exceptions\ConnectFreqException('Invalid connect_freq string.');
        }
        
        if (isset($params['firmware'])) {
            if (preg_match('/^.{1,32}$/', $params['firmware']) ) 
                $this->data['firmware'] = $params['firmware'];
            else
                throw new \App\Exceptions\FirmwareException('Invalid firmware string.');
        }
        
        //тут я не очень понял ТЗ, делаю как понял, далее нужно обсуждать
        
        $this->data['sets'] = $params['sets'] ?: [];
        
        return true;
    }
    
    public static function addSetPrefix(string $str) {
        return "set_$str";
    }
}
