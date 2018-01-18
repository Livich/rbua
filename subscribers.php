<?php

class Subscribers{
    private $file = './subscribers.json';
    public $subscribers = array();

    function __construct(){
        if (!file_exists($this->file)) {
            $this->subscribers = array();
            return;
        }
        $this->subscribers = json_decode(file_get_contents($this->file), true);
    }

    function get(){
        return array_keys($this->subscribers);
    }

    function add($id){
        $this->subscribers[$id] = '';
        $this->flush();
        return $this;
    }

    function del($id){
        if(!isset($this->subscribers[$id])){
            return $this;
        }
        unset($this->subscribers[$id]);
        $this->flush();
        return $this;
    }

    public function flush(){
        file_put_contents($this->file, json_encode($this->subscribers));
        return $this;
    }
}