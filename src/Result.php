<?php

namespace einfach\operation;

class Result
{
    protected $params;
    protected $finalTrack;
    protected $pipeline;

    public function __construct($params, $finalTrack, $pipeline = [])
    {
        $this->params = $params;
        $this->finalTrack = $finalTrack;
        $this->pipeline = $pipeline;
    }

    public function isSuccess()
    {
        return $this->finalTrack == Railway::TRACK_SUCCESS;
    }

    public function isError()
    {
        return $this->finalTrack == Railway::TRACK_FAILURE;
    }

    public function params()
    {
        return $this->params;
    }

    public function param($name)
    {
        return $this->params[$name];
    }

    public function errors()
    {
        return $this->params['__errors'];
    }

    public function errorsText($glue = '.')
    {
        return implode($glue, $this->params['__errors']);
    }

    public function pipeline()
    {
        return $this->pipeline;
    }
}
