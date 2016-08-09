<?php

namespace Setup\Model;

interface ModelInterface
{
   public function exchangeArray(array $data);
   // public function exchangeArrayFromDB(array $data);
   // public function getArrayCopyForDB();
   public function getArrayCopy();
}