<?php

namespace Setup\Model;

class Model
{
    private $mappings=[];

    public function exchangeArrayFromForm(array $data)
    {
        $entityKeys=array_keys($this->mappings);
        foreach($entityKeys as $keys){
            $this->{$keys} = !empty($data[$keys]) ? $data[$keys] : null;
        }
    }

    public function exchangeArrayFromDB(array $data)
    {
        foreach($this->mappings as $key => $value){
            $this->{$key} = !empty($data[$value]) ? $data[$value] : null;
        }
    }
    public function getArrayCopyForDB()
    {
        $tempArray=[];
        foreach($this->mappings as $key => $value){
            $tempArray[$value]=$this->{$key};
        }
        return $tempArray;
    }

    public function getArrayCopyForForm()
    {
        $tempArray=[];
        foreach($this->mappings as $key => $value){
            $tempArray[$key]=$this->{$key};
        }
        return $tempArray;
    }

}