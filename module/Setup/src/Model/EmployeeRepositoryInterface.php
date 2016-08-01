<?php

namespace Setup\Model;

interface EmployeeRepositoryInterface{
    public function addEmployee(Employee $employee);
    public function editEmployee(Employee $employee);
    public function  fetchAll();
    public function fetchById($id);

    public function deleteEmployee(Employee $employee);


}
