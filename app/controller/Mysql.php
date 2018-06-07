<?php
namespace app\controller;

class Mysql
{
    private $db = null;

    public function __construct()
    {
        $this->db = \myf\Mysql::instance('default');
    }

    public function create()
    {
        // 建表语句
        $tableSql = <<<END
            create table `bulk`(
              `id` bigint not NULL auto_increment,
               `key` bigint not NULL, 
               `name` varchar(32) not NULL, 
               PRIMARY KEY (`id`),
               UNIQUE KEY `key_unique`(`key`)
           );
END;
        $this->db->exec($tableSql);
    }

    /**
     * 合并插入
     */
    public function bulk($startId, $times, $batchSize = 100)
    {
        $placeholders = [];
        $values = [];

        $s = microtime(true);

        for ($i = 0; $i < $times; ++$i) {
            $key = $startId + $i;

            $placeholders[] = "(?,?)";
            $values[] = $key;
            $values[] = strval(time());
            if (count($placeholders) == $batchSize) {
                $sql = "insert ignore into `bulk`(`key`, `name`) values " . implode(',', $placeholders);
                $this->db->exec($sql, $values);

                $placeholders = [];
                $values = [];
            }
            if ($i % 10000 == 0) {
                echo $i / (microtime(true) - $s) . PHP_EOL;
            }
        }

        echo "总耗时:" . (microtime(true) - $s) . PHP_EOL;
    }

    /**
     * 单条插入
     * @param $startId
     * @param $times
     */
    public function single($startId, $times)
    {
        $s = microtime(true);

        for ($i = 0; $i < $times; ++$i) {
            $key = $startId + $i;

            $sql = "insert ignore into `bulk`(`key`, `name`) values (:key, :name)";
            $this->db->exec($sql, [':key' => $key, ':name' => strval(time())]);

            if ($i % 10000 == 0) {
                echo $i / (microtime(true) - $s) . PHP_EOL;
            }
        }

        echo "总耗时:" . (microtime(true) - $s) . PHP_EOL;
    }

    /**
     * 事务插入
     */
    public function tran($startId, $times, $batchSize = 100)
    {
        $values = [];

        $s = microtime(true);

        for ($i = 0; $i < $times; ++$i) {
            $key = $startId + $i;

            $values[] = [$key, strval(time())];
            if (count($values) == $batchSize) {
                $this->db->begin();
                foreach ($values as $value) {
                    $sql = "insert ignore into `bulk`(`key`, `name`) values(?,?)";
                    $this->db->exec($sql, $value);
                }
                $this->db->commit();

                $values = [];
            }
            if ($i % 10000 == 0) {
                echo $i / (microtime(true) - $s) . PHP_EOL;
            }
        }

        echo "总耗时:" . (microtime(true) - $s) . PHP_EOL;
    }
}