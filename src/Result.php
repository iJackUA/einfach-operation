<?php

namespace einfach\operation;

class Result
{
    protected $params;
    protected $finalTrack;
    protected $pipeline;

    function __construct($params, $finalTrack, $pipeline)
    {
        $this->params = $params;
        $this->finalTrack = $finalTrack;
        $this->pipeline = $pipeline;
    }

    function isSuccess()
    {
        return $this->finalTrack == Railway::TRACK_SUCCESS;
    }

    function isError()
    {
        return $this->finalTrack == Railway::TRACK_FAILURE;
    }

    function params()
    {
        return $this->params;
    }

    function param($name)
    {
        return $this->params[$name];
    }

    function errors()
    {
        return $this->params['errors'];
    }

    function errorsText($glue = '.')
    {
        print_r($this->params);
        return implode($glue, $this->params['errors']);
    }

    function pipeline()
    {
        return $this->pipeline;
    }
}
