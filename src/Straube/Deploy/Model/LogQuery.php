<?php

namespace Straube\Deploy\Model;

use PDO;

/**
 * Class LogQuery
 * @package Straube\Deploy\Model
 */
class LogQuery
{

    /**
     *
     * @var PDO
     */
    private static $connection;

    /**
     *
     * @param string $project
     * @param string $server
     * @param int $limit
     * @return array
     */
    public static function findLogs($project, $server, $limit = 10)
    {
        $logs = array();
        $sql = "SELECT `config` AS `project`, `server`, `from`, `to`, `user`, `time` AS `date` FROM `deploy` WHERE `config` = :project AND `server` = :server ORDER BY `time` DESC LIMIT :limit";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->bindParam(':project', $project, PDO::PARAM_STR);
        $stmt->bindParam(':server', $server, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $logs[] = new Log($row);
        }

        return $logs;
    }

    /**
     *
     * @param \Straube\Deploy\Model\Log $log
     * @return boolean
     */
    public static function addLog(Log $log)
    {
        $sql = "INSERT INTO `deploy` (`config`, `server`, `from`, `to`, `user`, `time`) VALUES (:project, :server, :from, :to, :user, :date)";
        $stmt = self::getConnection()->prepare($sql);

        $project = $log->getProject();
        $server = $log->getServer();
        $from = $log->getFrom();
        $to = $log->getTo();
        $user = $log->getUser();
        $date = $log->getDate();
        $stmt->bindParam(':project', $project, PDO::PARAM_STR);
        $stmt->bindParam(':server', $server, PDO::PARAM_STR);
        $stmt->bindParam(':from', $from, PDO::PARAM_STR);
        $stmt->bindParam(':to', $to, PDO::PARAM_STR);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     *
     * @return \PDO
     * @throws \RuntimeException
     */
    private static function getConnection()
    {
        if (!isset(self::$connection)) {
            self::connect();
        } else {
            try {
                @self::$connection->prepare("SELECT 1")->execute();
            } catch (\PDOException $e) {
                $message = $e->getMessage();
                if (preg_match("/gone away/i", $message)) {
                    self::$connection = null;
                    self::connect();
                }
            }
        }

        return self::$connection;
    }


    /**
     * Connects to Database
     */
    private static function connect()
    {
        $config = new Config();
        $databaseConfig = $config->getDatabase();
        if (empty($databaseConfig)) {
            throw new \RuntimeException('Database configuration not found.');
        }

        $dsn = sprintf(
            "mysql:host=%s;dbname=%s",
            $databaseConfig['host'],
            $databaseConfig['name']
        );

        self::$connection = new PDO($dsn, $databaseConfig['user'], $databaseConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    }
}
