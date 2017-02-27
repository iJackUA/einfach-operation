<?php

namespace Railway\Response;

const RESPONSE_TYPE_OK = 'ok';
const RESPONSE_TYPE_ERROR = 'error';

function Ok($appendParams = null)
{
    return ['type' => RESPONSE_TYPE_OK, 'appendParams' => $appendParams];
}

function Error($appendErrorString = null)
{
    return ['type' => RESPONSE_TYPE_ERROR, 'appendErrorString' => $appendErrorString];
}
