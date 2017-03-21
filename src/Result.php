<?php

namespace einfach\operation;

class Result
{
    protected $params;
    protected $track;
    protected $path;

    function __construct($params, $track, $path)
    {
        $this->params = $params;
        $this->track = $track;
        $this->path = $path;
    }

    function isSuccess()
    {
        return $this->track == Railway::TRACK_SUCCESS;
    }

    function isError()
    {
        return $this->track == Railway::TRACK_FAILURE;
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
        return implode($glue, $this->params['errors']);
    }

    function inspectPath()
    {
        return $this->path;
    }
}
