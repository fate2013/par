<?php
class Par extends CActiveRecord
{
    const STRING_HASH = 1;
    const DATE_HASH = 2;

    protected static $tablename = 'pars';
    protected $hashnum = 10;
    protected $hashtype = self::DATE_HASH;

    protected $_key;

    /**
     * Constructor.
     * @param array $entry=array($key=>$value) key is the partition field name.
     * @param array $opts some optitions: hash_num=>the hash num default 10.
     */
    public function __construct($opts = array(), $entry = array()){
        if($opts === null){
            return;
        }
        $this->parseParams($opts, $entry);
        parent::__construct();
    }

    public static function model($opts = null, $entry = array(), $className=__CLASS__)
    {
        $model = new $className($opts, $entry);
        return $model;
    }

    public function tableName()
    {
        return static::$tablename;
    }

    protected function parseParams($opts, $entry){
        if(isset($opts['hash_num'])){
            $this->hashnum = $opts['hash_num'];
        }
        if(isset($opts['hash_type'])){
            $this->hashtype = $opts['hash_type'];
        }
        if(!empty($entry)){
            $key = key($entry);
            $value = $entry[$key];
            $this->_key = $value;
        }
        static::$tablename .= '_'.$this->getHashTablename();
    }

    //根据字符串hash到十进制，计算表hash值
    protected function getHashTablename(){
        switch($this->hashtype){
        case self::STRING_HASH:
            $str = base_convert(md5($this->_key),16, 10);
            $key_hash = substr($str, strlen($str)-1, 1);
            $table_hash = $key_hash % $this->hashnum;
            break;
        case self::DATE_HASH:
            $table_hash = date('Y_m');
            break;
        default:
            break;
        }

        return $table_hash;
    }
}
