<?php

namespace Straube\Deploy\Model;

class Repo
{

    /**
     *
     * @var string
     */
    private $url;

    /**
     *
     * @param array $config
     * @throws \RuntimeException
     */
    public function __construct(array $config)
    {
        if (!isset($config['url'])) {
            throw new \RuntimeException('No URL given on repository configuration.');
        }
        $this->url = $config['url'];
    }

    /**
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

}
