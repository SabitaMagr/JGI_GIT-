<?php

namespace Setup\Model;

interface ModelInterface
{
    public function exchangeArray(array $data);
    public function getArrayCopy();
    public function exchangeArrayDb(array $data);
    public function getLocalArrayCopy();
}