<?php

namespace Setup\Model;

interface ModelInterface
{
    public function exchangeArrayFromForm(array $data);
    public function exchangeArrayFromDB(array $data);
    public function getArrayCopyForDB();
    public function getArrayCopyForForm();
}