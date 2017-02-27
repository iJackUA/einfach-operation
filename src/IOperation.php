<?php

namespace Railway;

interface IOperation
{
    public function __invoke($params);
}
