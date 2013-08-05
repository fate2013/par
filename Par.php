<?php
class Par extends CActiveRecord
{
    const STRING_HASH = 1;
    const DATE_HASH = 2;

    protected static $tablename = 'pars';
    protected $hashnum = 10;
    protected $hashtype = self::STRING_HASH;

    protected $_key;

    /**
     * Constructor.
     * @param array $entry=array($key=>$value) key is the partition field name.
     * @param array $opts some optitions: hash_num=>the hash num default 10.
     */
    public function __construct($entry = array(), array $opts = null){
        if($entry === null){
            return;
        }
        $this->parseParams($entry, $opts);
        parent::__construct();
    }

    public static function model($entry = array(), array $opts = null, $className=__CLASS__)
    {
        $model = new $className($entry, $opts);
        return $model;
    }

    public function tableName()
    {
        return static::$tablename;
    }

    protected function parseParams($entry, $opts){
        if(isset($opts['hash_num'])){
            $this->hashnum = $opts['hash_num'];
        }
        if(!empty($entry)){
            $key = key($entry);
            $value = $entry[$key];
            $this->_key = $key;
            static::$tablename .= '_'.$this->getHashTablename($value);
        }
    }

    //根据字符串hash到十进制，计算表hash值
    protected function getHashTablename($key){
        $str = base_convert(md5($key),16, 10);
        $key_hash = substr($str, strlen($str)-1, 1);
        $table_hash = $key_hash % $this->hashnum;
        return $table_hash;
    }
}
