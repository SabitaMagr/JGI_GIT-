<?php

namespace Notification\Repository;

use Application\Helper\EntityHelper;
use Application\Helper\Helper;
use Application\Model\Model;
use Application\Repository\RepositoryInterface;
use Notification\Model\NewsModel;
use Setup\Model\Company;
use Setup\Model\Designation;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

class NewsRepository implements RepositoryInterface {

    private $tableGateway;
    private $adapter;

    public function __construct(AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway(NewsModel::TABLE_NAME, $adapter);
    }

    public function add(Model $model) {
        $this->tableGateway->insert($model->getArrayCopyForDB());
    }

    public function delete($id) {
        $this->tableGateway->update([NewsModel::STATUS => 'D'], [NewsModel::NEWS_ID => $id]);
    }

    public function edit(Model $model, $id) {
        $data = $model->getArrayCopyForDB();
        unset($data[NewsModel::CREATED_BY]);
        unset($data[NewsModel::CREATED_DT]);
        unset($data[NewsModel::STATUS]);
        $this->tableGateway->update($data, [NewsModel::NEWS_ID => $id]);
    }

    public function fetchAll() {
        
         $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['N' => NewsModel::TABLE_NAME]);
        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=N." . NewsModel::COMPANY_ID, array('COMPANY_ID', 'COMPANY_NAME'), 'LEFT');
        $select->where(["N." . NewsModel::STATUS => 'E']);
        $select->order(["N." . NewsModel::NEWS_DATE => Select::ORDER_DESCENDING]);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result;
    }

    public function fetchById($id) {
        $sql = new Sql($this->adapter);
        $select = $sql->select();
        $select->from(['N' => NewsModel::TABLE_NAME]);
        $select->where(["N." . NewsModel::NEWS_ID => $id]);
        $select->columns(Helper::convertColumnDateFormat($this->adapter, new NewsModel(), ['newsDate'], NULL, 'N'), false);

        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $statement->execute();

        return $result->current();
    }

    public function fetchAllDesignationAndCompany() {
        $sql = new Sql($this->adapter);
        $select = $sql->select();

        $select->columns([Designation::DESIGNATION_ID, Designation::DESIGNATION_TITLE]);
        $select->from(['D' => Designation::TABLE_NAME]);

        $select->join(['C' => Company::TABLE_NAME], "C." . Company::COMPANY_ID . "=D." . Designation::COMPANY_ID, array('COMPANY_ID', 'COMPANY_NAME'), 'inner');

        $select->where(["C." . Company::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->where(["D." . Designation::STATUS => EntityHelper::STATUS_ENABLED]);
        $select->order(["D." . Designation::DESIGNATION_TITLE => Select::ORDER_ASCENDING]);

        $statement = $sql->prepareStatementForSqlObject($select);

        $result = $statement->execute();
        $list = Helper::extractDbData($result);
        $designationList = [];
        foreach ($list as $val) {
            $newKey = $val[Company::COMPANY_ID];
            $designationList[$newKey][] = $val;
        }
        return $designationList;
    }

    public function fetchForEmployee($employeeId, $date) {
        $rawResult = EntityHelper::rawQueryResult($this->adapter, "
        SELECT N.*
        FROM HRIS_NEWS N,
          (SELECT COMPANY_ID,   
            BRANCH_ID,
            DEPARTMENT_ID,
            DESIGNATION_ID
          FROM HRIS_EMPLOYEES
          WHERE EMPLOYEE_ID=$employeeId
          ) E
        WHERE (N.COMPANY_ID=E.COMPANY_ID
        OR N.BRANCH_ID     =E.BRANCH_ID
        OR N.DEPARTMENT_ID =E.DEPARTMENT_ID
        OR N.DESIGNATION_ID=E.DESIGNATION_ID)
        AND (N.STATUS      ='E') 
        AND N.NEWS_DATE=$date"
        );
        return $rawResult;
    }
    
    public function allNewsTypeWise($typeId, $employeeId) {
//        $sql="select * from hris_news where news_type={$typeId}";
        $sql = "SELECT * FROM (SELECT N.NEWS_ID,N.NEWS_DATE,N.NEWS_TYPE,N.NEWS_TITLE
FROM HRIS_NEWS N 
WHERE N.STATUS='E' AND N.NEWS_TYPE={$typeId} AND {$employeeId} IN (SELECT NE.EMPLOYEE_ID FROM HRIS_NEWS_EMPLOYEE NE WHERE NE.NEWS_ID=N.NEWS_ID)
UNION
SELECT N.NEWS_ID,N.NEWS_DATE,N.NEWS_TYPE,N.NEWS_TITLE
                    FROM HRIS_NEWS N,(SELECT COMPANY_ID,BRANCH_ID,DEPARTMENT_ID, DESIGNATION_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID ={$employeeId}) E
                    WHERE  N.STATUS = 'E' AND N.NEWS_TYPE={$typeId}
                    AND (N.COMPANY_ID =
                      CASE
                        WHEN N.COMPANY_ID IS NOT NULL
                        THEN E.COMPANY_ID
                      END
                    OR N.COMPANY_ID  IS NULL)
                    AND ( N.BRANCH_ID =
                      CASE
                        WHEN N.BRANCH_ID IS NOT NULL
                        THEN E.BRANCH_ID
                      END
                    OR N.BRANCH_ID      IS NULL)
                    AND (N.DEPARTMENT_ID =
                      CASE
                        WHEN N.DEPARTMENT_ID IS NOT NULL
                        THEN E.DEPARTMENT_ID
                      END
                    OR N.DEPARTMENT_ID   IS NULL)
                    AND (N.DESIGNATION_ID =
                      CASE
                        WHEN N.DESIGNATION_ID IS NOT NULL
                        THEN E.DESIGNATION_ID
                      END
                    OR N.DESIGNATION_ID IS NULL)
                    AND (N.DESIGNATION_ID =
                      CASE
                        WHEN N.DESIGNATION_ID IS NOT NULL
                        THEN E.DESIGNATION_ID
                      END
                    OR N.DESIGNATION_ID IS NULL)
                    AND N.NEWS_ID NOT IN (SELECT NEWS_ID FROM HRIS_NEWS_EMPLOYEE NE WHERE NE.NEWS_ID=N.NEWS_ID)) ORDER BY NEWS_DATE DESC";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return Helper::extractDbData($result);
    }

}
