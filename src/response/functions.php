<?php

namespace einfach\operation\response;

const RESPONSE_TYPE_OK = 'ok_step_response';
const RESPONSE_TYPE_ERROR = 'error_step_response';

function ok(array $params, array $appendParams = [])
{
    return [
        'type' => RESPONSE_TYPE_OK,
        'params' => $params,
        'appendParams' => $appendParams
        ];
}

/**
 * @param mixed $appendError Can accept Array of string or a single String
 *                           and convert it to Array of strings
 */
function error(array $params, $appendError = [])
{
    $appendError = (is_string($appendError)) ? [$appendError] : $appendError;
    return [
        'type' => RESPONSE_TYPE_ERROR,
        'params' => $params,
        'appendError' => $appendError
        ];
}

function isOk($stepResult)
{
    return isValidResponse($stepResult) && $stepResult['type'] == RESPONSE_TYPE_OK;
}

function isError($stepResult)
{
    return isValidResponse($stepResult) && $stepResult['type'] == RESPONSE_TYPE_ERROR;
}

function isValidResponse($response)
{
    return is_array($response) && isset($response['params']) && isset($response['type']);
}
