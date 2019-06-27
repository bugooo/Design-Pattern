<?php
//定义一个适配器接口
interface Db_Adapter{
    /**数据库连接
     * @param $config数据库配置
     * @return resource
     */
    public function connect($config);
    /**执行数据库查询
     *@param string $query 数据库查询SQL字符串
     *@param mixed $handle 连接对象
     *@return resource
     */
    public function query($query,$handle);
}
//﻿定义MySQL数据库的操作类
class Db_Adapter_Mysql implements Db_Adapter{

    private $_dbLink;//数据库链接字符串标示

    /**
     *数据库连接函数
     *@param $config 数据库配置
     *@throws Db_Exception
     *@return resource
     */
    public function connect($config)
    {
        if ($this->_dbLink = @mysql_connect($config->host . (empty($config->port) ? '' : '' . $config->port), $config->user, $config->password, true)) {
            if (@mysql_select_db($config->database, $this->_dbLink)) {
                if ($config->charset) {
                    mysql_query("SETNAMES'{$config->charset}'", $this->_dbLink);
                }
                return $this->_dbLink;
            }
        }
        /**数据库异常*/
        throw new Db_Exception(@mysql_error($this->_dbLink));
    }

    /**执行数据库查询
     * @param string $query 数据库查询SQL字符串
     * @param mixed $handle 连接对象
     * @return resource
     */
    public function query($query,$handle){
        if($resource=@mysql_query($query,$handle)){
            return $resource;
        }
    }

}

//SQLite数据库的操作类
class Db_Adapter_sqlite implements Db_Adapter{
    private $_dblink;//数据库连接字符串标示
    /**
     *数据库连接函数
     *@param $config 数据库配置
     *@throws Db_Exception
     *@return resource
     */
    public function connect($config){
        if($this->_dblink=sqlite_open($config->file,0666,$error)){return $this->_dblink;}
        /**数据库异常*/
        throw new Db_Exception($error);
    }
    /**
     *执行数据库查询
     *@param string $query 数据库查询SQL字符串
     *@param mixed $handle 连接对象
     *@return resource
     */
    public function query($query,$handle){
        if($resource=@sqlite_query($query,$handle)){
            return $resource;
        }
    }
}

// ﻿定义一个工厂类
class sqlFatory{
    public static function factory($type){
        $classname='Db_Adapter_'.$type;
        if(class_exists($classname)){
            return new $classname;
        }else{
            throw new Exception('Drivernotfound');
        }
    }
}
$db = sqlFatory::factory('MySQL');
$db = sqlFactory::factory('SQLite');









