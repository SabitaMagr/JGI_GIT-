<?php

namespace Setup\Model;

class Model
{
    public $mappings;

    public function exchangeArrayFromForm(array $data)
    {
        $entityKeys = array_keys($this->mappings);
        foreach ($entityKeys as $keys) {
            $this->{$keys} = !empty($data[$keys]) ? $data[$keys] : null;
        }

    }

    public function exchangeArrayFromDB(array $data)
    {

        foreach ($this->mappings as $key => $value) {
            $this->{$key} = !empty($data[$value]) ? $data[$value] : null;
        }
    }

    public function getArrayCopyForDB()
    {
        $tempArray = [];
        foreach ($this->mappings as $key => $value) {
        $tempArray[$value]=$this->{$key};
        }

        return $tempArray;

    }

    public function getArrayCopy()
    {
        $tempArray = [];
        foreach ($this->mappings as $key => $value) {
//            array_push($tempArray, $key, $this->{$key});
            $tempArray[$key]=$this->{$key};
        }
        return $tempArray;
    }
}