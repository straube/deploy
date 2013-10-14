<?php

namespace Straube\Deploy\Model;

class Config
{

    /**
     *
     * @var string
     */
    const FILE = 'config.json';

    /**
     *
     * @var array
     */
    private static $config;

    /**
     *
     * @var array
     */
    private $projects;

    /**
     *
     * @var array
     */
    private $database;

    /**
     *
     */
    public function __construct()
    {
        if (!isset(self::$config)) {
            $this->loadConfig();
        }
        if (!empty(self::$config['database'])) {
            $this->database = self::$config['database'];
        }
        $this->projects = array();
        foreach (self::$config as $projectName => $projectConfig) {
            if ('database' == $projectName) {
                continue;
            }
            $this->projects[$projectName] = new Project($projectName, $projectConfig);
        }
    }

    /**
     *
     * @return array
     */
    public function getAvailableProjects()
    {
        return array_keys($this->projects);
    }

    /**
     *
     * @param string $projectName
     * @return \Straube\Deploy\Model\Project
     */
    public function getProject($projectName)
    {
        $project = null;
        if ($this->hasProject($projectName)) {
            $project = $this->projects[$projectName];
        }

        return $project;
    }

    /**
     *
     * @param string $projectName
     * @return boolean
     */
    public function hasProject($projectName)
    {
        return isset($this->projects[$projectName]);
    }

    /**
     * 
     * @return array
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     *
     * @throws \RuntimeException
     */
    private function loadConfig()
    {
        $path = self::FILE;
        if (!is_file($path)) {
            throw new \RuntimeException(sprintf('Config file cannot be found. Looking for %s.', $path));
        }
        $contents = file_get_contents($path);
        $assoc = true;
        if (null === (self::$config = @json_decode($contents, $assoc))) {
            throw new \RuntimeException('Config file cannot be decode as JSON.');
        }
    }

}
