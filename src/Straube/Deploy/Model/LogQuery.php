<?php

namespace Straube\Deploy\Model;

use mysqli;

class LogQuery
{

    /**
     *
     * @var \mysqli
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
        $sql = "SELECT `config` AS `project`, `server`, `from`, `to`, `user`, `time` AS `date` FROM `deploy` WHERE `config` = ? AND `server` = ? ORDER BY `time` DESC LIMIT ?";
        $stmt = self::getConnection()->prepare($sql);
        $stmt->bind_param('ssd', $project, $server, $limit);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            while (null !== ($row = $result->fetch_array(MYSQLI_ASSOC))) {
                $logs[] = new Log($row);
            }
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
        $sql = "INSERT INTO `deploy` (`config`, `server`, `from`, `to`, `user`, `time`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = self::getConnection()->prepare($sql);
        $project = $log->getProject();
        $server = $log->getServer();
        $from = $log->getFrom();
        $to = $log->getTo();
        $user = $log->getUser();
        $date = $log->getDate();
        $stmt->bind_param('ssssss', $project, $server, $from, $to, $user, $date);

        return $stmt->execute();
    }

    /**
     *
     * @return \mysqli
     * @throws \RuntimeException
     */
    private static function getConnection()
    {
        if (!isset(self::$connection)) {
            $config = new Config();
            $databaseConfig = $config->getDatabase();
            if (empty($databaseConfig)) {
                throw new \RuntimeException('Database configuration not found.');
            }
            self::$connection = new mysqli($databaseConfig['host'], $databaseConfig['user'], $databaseConfig['password'], $databaseConfig['name']);
        }

        return self::$connection;
    }

}
