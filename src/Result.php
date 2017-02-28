<?php

namespace einfach\operation;

class Result
{
    protected $params;
    protected $track;

    function __construct($params, $track)
    {
        $this->params = $params;
        $this->track = $track;
    }

    function isSuccess()
    {
        return $this->track == Railway::TRACK_OK;
    }

    function isError()
    {
        return $this->track == Railway::TRACK_ERROR;
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
}
