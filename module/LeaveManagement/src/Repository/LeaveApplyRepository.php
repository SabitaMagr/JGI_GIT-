<?php

/**
 * Created by PhpStorm.
 * User: ukesh
 * Date: 9/9/16
 * Time: 10:53 AM 
 */
 
namespace LeaveManagement\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use LeaveManagement\Model\LeaveApply;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class LeaveApplyRepository implements RepositoryInterface {

    private $tableGateway;  
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->tableGateway = new TableGateway(LeaveApply::TABLE_NAME, $adapter);
        $this->adapter = $adapter;
    }

    public function fileUpload(){
        if(!empty($_FILES['files']['name'][0])){ 
            for($i = 0; $i < count($_FILES['files']['name']); $i++){
                $fileName = $_FILES['files']['name'][$i];  
                $ext = end((explode(".", $fileName)));
                $fileInDir = Helper::generateUniqueName().".".$ext; 
                $sql = "INSERT INTO HRIS_LEAVE_FILES(FILE_ID, FILE_NAME, FILE_IN_DIR_NAME, LEAVE_ID) VALUES((SELECT MAX(FILE_ID)+1 FROM HRIS_LEAVE_FILES), '$fileName', '$fileInDir', (SELECT MAX(ID) FROM HRIS_EMPLOYEE_LEAVE_REQUEST))";
                $statement = $this->adapter->query($sql);
                $statement->execute();
                move_uploaded_file($_FILES["files"]["tmp_name"][$i], Helper::UPLOAD_DIR.'/leave_documents/'.$fileInDir);
            } 
        }
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
        $this->fileUpload();
    }

    public function edit(Model $model, $id) { 
        // TODO: Implement edit() method.
    }

    public function fetchAll() {
        // TODO: Implement fetchAll() method.
    }

    public function fetchById($id) {
        return $this->tableGateway->select(function(Select $select) use($id) {
                    $select->columns(Helper::convertColumnDateFormat($this->adapter, new LeaveApply(), [
                                'startDate', 'endDate'
                            ]), false);
                    $select->where([LeaveApply::ID => $id]);
                })->current();
    }

    public function delete($id) {
        // TODO: Implement delete() method.
    }

}
