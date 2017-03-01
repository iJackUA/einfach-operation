<?php

namespace einfach\operation\response;

const RESPONSE_TYPE_OK = 'ok';
const RESPONSE_TYPE_ERROR = 'error';

function ok($appendParams = [])
{
    return ['type' => RESPONSE_TYPE_OK, 'appendParams' => $appendParams];
}

function error($appendError = [])
{
    return ['type' => RESPONSE_TYPE_ERROR, 'appendError' => $appendError];
}

function isOk($type)
{
    return $type == RESPONSE_TYPE_OK;
}

function isError($type)
{
    return $type == RESPONSE_TYPE_ERROR;
}

function isValidResponse($response)
{
    return is_array($response) && isset($response['type']);
}
