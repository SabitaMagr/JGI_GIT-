<?php

namespace Setup\Model;

interface ModelInterface
{
    public function exchangeArray(array $data);
    public function getArrayCopy();
}