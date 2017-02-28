<?php

namespace einfach\operation\response;

const RESPONSE_TYPE_OK = 'ok';
const RESPONSE_TYPE_ERROR = 'error';

function ok($appendParams = null)
{
    return ['type' => RESPONSE_TYPE_OK, 'appendParams' => $appendParams];
}

function error($appendErrorString = null)
{
    return ['type' => RESPONSE_TYPE_ERROR, 'appendErrorString' => $appendErrorString];
}
