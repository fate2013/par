<?php
class Par extends CActiveRecord
{
    protected static $tablename = 'mars';
    protected $hashnum = 10;
    protected $_key;

    public function __construct($entry = array(), array $opts = null){
        if($entry === null){
            return;
        }
        if(!empty($entry)){
            $key = key($entry);
            $value = $entry[$key];
            $this->_key = $key;
            static::$tablename .= $this->getHashTablename($value);
        }
        if(isset($opts['hash_num'])){
            $this->hashnum = $opts['hash_num'];
        }
        parent::__construct();
    }

    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return static::$tablename;
    }

    protected function getHashTablename($key){
        $str = base_convert(md5($key),16, 10);
        $key_hash = substr($str, strlen($str)-1, 1);
        $table_hash = $key_hash % $this->hashnum;
        return $table_hash;
    }
}
