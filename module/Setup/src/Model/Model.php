<?php

namespace Setup\Model;

class Model
{
    public $mappings=[];

    public function exchangeArrayFromForm(array $data)
    {
        foreach($this->mappings as $key => $value){
            $this->{$value} = !empty($data[$value]) ? $data[$value] : null;
        }
    }

    public function exchangeArrayFromDB(array $data)
    {
        foreach($this->mappings as $key => $value){
            $this->{$value} = !empty($data[$key]) ? $data[$key] : null;
        }
    }
    public function getArrayCopyForDB()
    {
        $tempArray=[];
        foreach($this->mappings as $key => $value){
            $tempArray[$key]=$this->{$value};
        }
        return $tempArray;
    }

    public function getArrayCopyForForm()
    {
        $tempArray=[];
        foreach($this->mappings as $key => $value){
            $tempArray[$value]=$this->{$value};
        }
        return $tempArray;
    }

}