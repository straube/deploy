<?php

namespace Straube\Deploy\Model;

use Straube\Common\Utils\StringUtils;
use Straube\Deploy\Server\ConnectionFactory;

class Server
{

    /**
     *
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $type;

    /**
     *
     * @var string
     */
    private $branch;

    /**
     *
     * @var string
     */
    private $host;

    /**
     *
     * @var string
     */
    private $user;

    /**
     *
     * @var string
     */
    private $password;

    /**
     *
     * @var string
     */
    private $path;

    /**
     *
     * @var array
     */
    private $preDeployCommands = array();

    /**
     *
     * @var array
     */
    private $postDeployCommands = array();

    /**
     *
     * @param string $name
     * @param array $config
     * @throws \RuntimeException
     */
    public function __construct($name, array $config)
    {
        $this->name = $name;
        $requiredConfigKeys = array(
            'type',
            'branch',
            'host',
        );
        if (count(array_diff_key(array_flip($requiredConfigKeys), $config)) > 0) {
            throw new \RuntimeException(sprintf("Required configuration are missing for server '%s'. You must set the following keys on configuration file: %s", $name, implode(', ', $requiredConfigKeys)));
        }
        $availableTypes = ConnectionFactory::getAvailableTypes();
        if (!in_array($config['type'], $availableTypes)) {
            throw new \RuntimeException(sprintf("Invalid type in '%s' server configuration, expecting one of the following values: %s.", $name, implode(', ', $availableTypes)));
        }
        foreach ($config as $key => $value) {
            $property = StringUtils::jsonKeyToClassProperty($key);
            if (property_exists($this, $property)) {
                $this->$property = in_array($property, array(
                    'preDeployCommands',
                    'postDeployCommands',
                ))
                    ? (array) $value
                    : (string) $value;
            }
        }
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     *
     * @return string
     */
    public function getBranch()
    {
        return $this->branch;
    }

    /**
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     * @return array
     */
    public function getPreDeployCommands()
    {
        return $this->preDeployCommands;
    }

    /**
     *
     * @return array
     */
    public function getPostDeployCommands()
    {
        return $this->postDeployCommands;
    }

}
