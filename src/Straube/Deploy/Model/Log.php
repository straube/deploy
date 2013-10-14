<?php

namespace Straube\Deploy\Model;

class Log
{

    /**
     *
     * @var string
     */
    private $project;

    /**
     *
     * @var string
     */
    private $server;

    /**
     *
     * @var string
     */
    private $from;

    /**
     *
     * @var string
     */
    private $to;

    /**
     *
     * @var string
     */
    private $user;

    /**
     *
     * @var string
     */
    private $date;

    /**
     *
     * @param array $data
     */
    public function __construct(array $data = null)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
        if (empty($this->date)) {
            $this->date = date('Y-m-d H:i:s');
        }
    }

    /**
     *
     * @return string
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     *
     * @return string
     */
    public function getTo()
    {
        return $this->to;
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
    public function getDate()
    {
        return $this->date;
    }

    /**
     *
     * @return boolean
     */
    public function save()
    {
        return LogQuery::addLog($this);
    }

}
