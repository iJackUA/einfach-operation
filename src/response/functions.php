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
