<?php

namespace einfach\operation\response;

const RESPONSE_TYPE_OK = 'ok_step_response';
const RESPONSE_TYPE_ERROR = 'error_step_response';

function ok(array $appendParams = [])
{
    return ['type' => RESPONSE_TYPE_OK, 'appendParams' => $appendParams];
}

/**
 * @param mixed $appendError Can accept Array of string or a single String 
 * and convert it to Array of strings
 */
function error($appendError)
{
    $appendError = (is_string($appendError)) ? [$appendError] : $appendError;
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
