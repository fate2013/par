PAR
===

An extension of Yii ORM to support partition tables.

特点
=== 

基于表的一个字段计算hash并分表，方便使用，初始化时指定需要hash的字段即可。

使用方法
===

将par.php放在components目录下，需要分表支持的model继承Par类，并定义$tablename静态变量作为表的未分表名称。代码如下：

Controller

public function actionTest()
{
    $test = new Test(array('url'=>'http://www.baidu.com'));
    $test->name='a test name';
    $test->save()
}

Model

class Test extends Par
{
    public static $tablename = 'test';    //未分表前的表名
    
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            array('url, name', 'safe'),
        );
    }
}


源码：

class Par extends CActiveRecord
{
    protected static $tablename = 'mars';
    protected $hashnum = 10;
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
        if(!empty($entry)){
            $key = key($entry);
            $value = $entry[$key];
            $this->_key = $key;
            static::$tablename .= '_'.$this->getHashTablename($value);
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

    //根据字符串hash到十进制，计算表hash值
    protected function getHashTablename($key){
        $str = base_convert(md5($key),16, 10);
        $key_hash = substr($str, strlen($str)-1, 1);
        $table_hash = $key_hash % $this->hashnum;
        return $table_hash;
    }
}
