<?php
namespace Application\Helper;

use Zend\Db\Adapter\AdapterInterface;
use Application\Helper\Helper;
use Setup\Repository\LoanRepository;
use Setup\Repository\LoanRestrictionRepository;
use Setup\Repository\EmployeeRepository;

class LoanAdvanceHelper{
    
    public static function getLoanList(AdapterInterface $adapter,$employeeId){
        $employeeId = $employeeId;
        $loanRepo = new LoanRepository($adapter);
        $loanRestrictionRepo = new LoanRestrictionRepository($adapter);
        $employeeRepo = new EmployeeRepository($adapter);
        
        $employeeDetail = $employeeRepo->fetchById($employeeId);
        
        $position = $employeeDetail['POSITION_ID'];
        $serviceType = $employeeDetail['SERVICE_TYPE_ID'];
        $designation = $employeeDetail['DESIGNATION_ID'];
        
        $salary = (int)$employeeDetail['SALARY'];
        $joinDate = \DateTime::createFromFormat(Helper::PHP_DATE_FORMAT, $employeeDetail['JOIN_DATE']);
        $currentDate = new \DateTime();
        
        $different = date_diff($joinDate,$currentDate);
        $yr = $different->format('%y');
        $mn = $different->format('%m');
        $days = $different->format('%d');
        $mnPercentage = 0.083;
        
        $mnInPer = $mn * $mnPercentage;
        $totalYr =(double)($yr+$mnInPer);
        $actTotalYr = round($totalYr,2);
        $loanList = $loanRepo->fetchActiveRecord();
        
        $loanResultList = [];
        foreach($loanList as $loanRow){
            $loanId = $loanRow['LOAN_ID'];
            $restrictionDtl = $loanRestrictionRepo->getByLoanId($loanId);
            
            $positionList = explode(",",$restrictionDtl['position']);
            $serviceTypeList =  explode(",",$restrictionDtl['serviceType']);
            $designationList = explode(",",$restrictionDtl['designation']);
            $salaryRange =  explode(",",$restrictionDtl['salaryRange']);
            
            if(count($salaryRange)>1){
                $salaryFrom = (int)$salaryRange[0];
            }
            if(count($salaryRange)==2){
               $salaryTo = (int)$salaryRange[1];
            }
            if(count($salaryRange)==1 ||  count($salaryRange)!=2){
                $salaryTo = "";
            }
            
            $workingPeriod =  explode(",",$restrictionDtl['workingPeriod']);
            if(count($workingPeriod)>1){
                $workingPeriodFrom = (int)$workingPeriod[0];
            }
            if(count($workingPeriod)==2){
               $workingPeriodTo = (int)$workingPeriod[1];
            }
            if(count($workingPeriod)==1 ||  count($workingPeriod)!=2){
                $workingPeriodTo = "";
            }
            
            if(!in_array($position,$positionList) && !in_array($serviceType, $serviceTypeList) && !in_array($designation,$designationList)  && !($salary>=$salaryFrom && $salary<=$salaryTo) && !($actTotalYr>=$workingPeriodFrom && $actTotalYr<=$workingPeriodTo)){
                $loanResultList[$loanRow['LOAN_ID']] = $loanRow['LOAN_NAME'];
            }
        }
        return $loanResultList;
    }
}