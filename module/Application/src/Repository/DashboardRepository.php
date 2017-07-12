<?php

namespace Application\Repository;

use Application\Helper\Helper;
use Application\Model\Model;
use Zend\Authentication\AuthenticationService;

class DashboardRepository implements RepositoryInterface {

    private $adapter;
    private $fiscalYr;

    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
        $auth = new AuthenticationService();
        $this->fiscalYr = $auth->getStorage()->read()['fiscal_year'];
    }

    public function add(Model $model) {
        
    }

    public function delete($id) {
        
    }

    public function edit(Model $model, $id) {
        
    }

    public function fetchById($id) {
        
    }

    public function fetchAll() {
        
    }

    /*
     * EMPLOYEE DASHBOARD FUNCTIONS
     */

    public function fetchEmployeeDashboardDetail($employeeId, $startDate, $endDate) {
        $sql = "-- EMPLOYEE DETAIL
            SELECT EMPLOYEE_TBL.*,
              NVL(LATE_TBL.LATE_IN, 0) LATE_IN,
              NVL(EARLY_TBL.EARLY_OUT, 0) EARLY_OUT,
              NVL(MISSED_PUNCH_TBL.MISSED_PUNCH, 0) MISSED_PUNCH,
              NVL(PRESENT_TBL.PRESENT_DAY, 0) PRESENT_DAY,
              NVL(ABSENT_TBL.ABSENT_DAY, 0) ABSENT_DAY,
              NVL(LEAVE_TBL.LEAVE, 0) LEAVE,
              NVL(WOH_TBL.WOH, 0) WOH,
              NVL(TOUR_TBL.TOUR, 0) TOUR,
              NVL(TRAINING_TBL.TRAINING, 0) TRAINING
            FROM
              (SELECT EMP.EMPLOYEE_ID,
                EMP.FULL_NAME,
                EMP.GENDER_ID,
                EMP.COMPANY_ID,
                EMP.BRANCH_ID,
                EMP.EMAIL_OFFICIAL,
                EMP.EMAIL_PERSONAL,
                TO_CHAR(EMP.JOIN_DATE, 'DD-MON-YYYY') JOIN_DATE,
                TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE) / 12)                                        AS SERVICE_YEARS,
                TRUNC(MOD(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE), 12))                                    AS SERVICE_MONTHS,
                TRUNC(SYSDATE) - ADD_MONTHS(EMP.JOIN_DATE, TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE))) AS SERVICE_DAYS,
                DSG.DESIGNATION_TITLE,
                EFL.FILE_PATH
              FROM HRIS_EMPLOYEES EMP,
                HRIS_DESIGNATIONS DSG,
                HRIS_EMPLOYEE_FILE EFL
              WHERE EMP.DESIGNATION_ID   = DSG.DESIGNATION_ID(+)
              AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
              AND EMP.RETIRED_FLAG       = 'N'
              AND EMP.EMPLOYEE_ID = {$employeeId}
              ) EMPLOYEE_TBL
            LEFT JOIN
              (SELECT ATTEN.EMPLOYEE_ID,
                COUNT (*) LATE_IN
              FROM HRIS_ATTENDANCE_DETAIL ATTEN
              WHERE (ATTEN.LATE_STATUS = 'L'
              OR ATTEN.LATE_STATUS     ='B')
              AND ATTEN.EMPLOYEE_ID    ={$employeeId}
              AND (ATTEN.ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY'))
              GROUP BY ATTEN.EMPLOYEE_ID
              ) LATE_TBL
            ON LATE_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN (
              SELECT ATTEN.EMPLOYEE_ID,
                COUNT (*) EARLY_OUT
              FROM HRIS_ATTENDANCE_DETAIL ATTEN
              WHERE (ATTEN.LATE_STATUS = 'E'
              OR ATTEN.LATE_STATUS     ='B')
              AND ATTEN.EMPLOYEE_ID    ={$employeeId}
              AND (ATTEN.ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY'))
              GROUP BY ATTEN.EMPLOYEE_ID
              ) EARLY_TBL
            ON EARLY_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN
              (
              SELECT ATTEN.EMPLOYEE_ID,
                COUNT(*) MISSED_PUNCH
              FROM HRIS_ATTENDANCE_DETAIL ATTEN
              WHERE ATTEN.EMPLOYEE_ID = {$employeeId}
              AND ATTEN.ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              AND ATTEN.OUT_TIME IS NULL
              AND ATTEN.IN_TIME IS NOT NULL
              GROUP BY ATTEN.EMPLOYEE_ID
              ) MISSED_PUNCH_TBL
            ON MISSED_PUNCH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN          
            (SELECT ATTEN.EMPLOYEE_ID,
              COUNT (*) PRESENT_DAY
            FROM
              (SELECT EMPLOYEE_ID
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE (IN_TIME IS NOT NULL
              OR LEAVE_ID    IS NOT NULL
              OR HOLIDAY_ID  IS NOT NULL
              OR TRAINING_ID IS NOT NULL
              OR TRAVEL_ID   IS NOT NULL)
              AND DAYOFF_FLAG = 'N'
              AND EMPLOYEE_ID = {$employeeId}
              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              ) ATTEN
            GROUP BY ATTEN.EMPLOYEE_ID) PRESENT_TBL

            ON PRESENT_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
                COUNT (*) ABSENT_DAY
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE IN_TIME   IS NULL
              AND LEAVE_ID    IS NULL
              AND HOLIDAY_ID  IS NULL
              AND TRAINING_ID IS NULL
              AND TRAVEL_ID   IS NULL
              AND DAYOFF_FLAG  = 'N'
              AND EMPLOYEE_ID  = {$employeeId}
              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              GROUP BY EMPLOYEE_ID
              ) ABSENT_TBL
            ON ABSENT_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
                COUNT (*) LEAVE
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE LEAVE_ID IS NOT NULL
              AND DAYOFF_FLAG = 'N'
              AND EMPLOYEE_ID = {$employeeId}
              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              GROUP BY EMPLOYEE_ID
              ) LEAVE_TBL
            ON LEAVE_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
                COUNT (*) WOH
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE HOLIDAY_ID IS NOT NULL
              AND IN_TIME      IS NOT NULL
              AND OUT_TIME     IS NOT NULL
              AND DAYOFF_FLAG   = 'N'
              AND EMPLOYEE_ID   = {$employeeId}
              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              GROUP BY EMPLOYEE_ID
              ) WOH_TBL
            ON WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
                COUNT (*) TOUR
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE TRAVEL_ID IS NOT NULL
              AND DAYOFF_FLAG  = 'N'
              AND EMPLOYEE_ID  = {$employeeId}
              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              GROUP BY EMPLOYEE_ID
              ) TOUR_TBL
            ON TOUR_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
            LEFT JOIN
              (SELECT EMPLOYEE_ID,
                COUNT (*) TRAINING
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE HOLIDAY_ID IS NULL
              AND TRAINING_ID  IS NOT NULL
              AND EMPLOYEE_ID   = {$employeeId}
              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
              GROUP BY EMPLOYEE_ID
              ) TRAINING_TBL
            ON TRAINING_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
          ";

        print_r($sql);
        exit;

        $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();
        return $result;
    }

//    public function fetchEmployeeDashboardDetail($employeeId, $startDate, $endDate) {
//        $sql = "-- EMPLOYEE DETAIL
//            SELECT EMPLOYEE_TBL.*,
//              NVL(LATE_TBL.LATE_IN, 0) LATE_IN,
//              NVL(EARLY_TBL.EARLY_OUT, 0) EARLY_OUT,
//              NVL(MISSED_PUNCH_TBL.MISSED_PUNCH, 0) MISSED_PUNCH,
//              NVL(PRESENT_TBL.PRESENT_DAY, 0) PRESENT_DAY,
//              NVL(ABSENT_TBL.ABSENT_DAY, 0) ABSENT_DAY,
//              NVL(LEAVE_TBL.LEAVE, 0) LEAVE,
//              NVL(WOH_TBL.WOH, 0) WOH,
//              NVL(TOUR_TBL.TOUR, 0) TOUR,
//              NVL(TRAINING_TBL.TRAINING, 0) TRAINING,
//              NVL(AVERAGE_OFFICE_HRS_TBL.AVG_HOURS, 0) AVG_HOURS,
//              NVL(AVERAGE_OFFICE_HRS_TBL.AVG_MINUTES, 0) AVG_MINUTES,
//              NVL(CUR_MONTH_WOH_TBL.CUR_MONTH_WOH, 0) CUR_MONTH_WOH,
//              NVL(PREV_MONTH_WOH_TBL.PREV_MONTH_WOH, 0) PREV_MONTH_WOH,
//              NVL(JOINED_THIS_MONTH_TBL.JOINED_THIS_MONTH, 0) JOINED_THIS_MONTH
//            FROM
//              (SELECT EMP.EMPLOYEE_ID,
//                (
//                CASE
//                  WHEN MIDDLE_NAME IS NULL
//                  THEN EMP.FIRST_NAME
//                    || ' '
//                    || EMP.LAST_NAME
//                  ELSE EMP.FIRST_NAME
//                    || ' '
//                    || EMP.MIDDLE_NAME
//                    || ' '
//                    || EMP.LAST_NAME
//                END ) FULL_NAME,
//                EMP.GENDER_ID,
//                EMP.COMPANY_ID,
//                EMP.BRANCH_ID,
//                EMP.EMAIL_OFFICIAL,
//                EMP.EMAIL_PERSONAL,
//                TO_CHAR(EMP.JOIN_DATE, 'DD-MON-YYYY') JOIN_DATE,
//                TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE) / 12)                                        AS SERVICE_YEARS,
//                TRUNC(MOD(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE), 12))                                    AS SERVICE_MONTHS,
//                TRUNC(SYSDATE) - ADD_MONTHS(EMP.JOIN_DATE, TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE))) AS SERVICE_DAYS,
//                DSG.DESIGNATION_TITLE,
//                EFL.FILE_PATH
//              FROM HRIS_EMPLOYEES EMP,
//                HRIS_DESIGNATIONS DSG,
//                HRIS_EMPLOYEE_FILE EFL
//              WHERE EMP.DESIGNATION_ID   = DSG.DESIGNATION_ID(+)
//              AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
//              AND EMP.RETIRED_FLAG       = 'N'
//                -- AND EMP.COMPANY_ID = 2
//              AND EMP.EMPLOYEE_ID = {$employeeId}
//              ) EMPLOYEE_TBL
//              -- LATE IN
//            LEFT JOIN
//              (SELECT ATTEN.EMPLOYEE_ID,
//                COUNT (*) LATE_IN
//              FROM HRIS_ATTENDANCE_DETAIL ATTEN
//              WHERE (ATTEN.LATE_STATUS = 'L'
//              OR ATTEN.LATE_STATUS     ='B')
//              AND ATTEN.EMPLOYEE_ID    ={$employeeId}
//              AND (ATTEN.ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY'))
//              GROUP BY ATTEN.EMPLOYEE_ID
//              ) LATE_TBL
//            ON LATE_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- EARLY OUT
//            LEFT JOIN (
//              SELECT ATTEN.EMPLOYEE_ID,
//                COUNT (*) EARLY_OUT
//              FROM HRIS_ATTENDANCE_DETAIL ATTEN
//              WHERE (ATTEN.LATE_STATUS = 'E'
//              OR ATTEN.LATE_STATUS     ='B')
//              AND ATTEN.EMPLOYEE_ID    ={$employeeId}
//              AND (ATTEN.ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY'))
//              GROUP BY ATTEN.EMPLOYEE_ID
//              ) EARLY_TBL
//            ON EARLY_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- MISSED PUNCH
//            LEFT JOIN
//              --  (SELECT EMPLOYEE_ID,
//              --    COUNT(*) MISSED_PUNCH
//              --  FROM
//              --    (SELECT EMPLOYEE_ID,
//              --      ATTENDANCE_DT,
//              --      COUNT (*)
//              --    FROM HRIS_ATTENDANCE
//              --    WHERE EMPLOYEE_ID = {$employeeId}
//              --    AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              --    GROUP BY EMPLOYEE_ID,
//              --      ATTENDANCE_DT
//              --    HAVING MOD(COUNT(*), 2) <> 0
//              --    )
//              --  GROUP BY EMPLOYEE_ID
//              --  ) MISSED_PUNCH_TBL
//              (
//              SELECT ATTEN.EMPLOYEE_ID,
//                COUNT(*) MISSED_PUNCH
//              FROM HRIS_ATTENDANCE_DETAIL ATTEN
//              WHERE ATTEN.EMPLOYEE_ID = {$employeeId}
//              AND ATTEN.ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              AND ATTEN.OUT_TIME IS NULL
//              AND ATTEN.IN_TIME IS NOT NULL
//              GROUP BY ATTEN.EMPLOYEE_ID
//              ) MISSED_PUNCH_TBL
//            ON MISSED_PUNCH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- PRESENT DAY
//            LEFT JOIN
//            --  (SELECT EMPLOYEE_ID,
//            --    COUNT (*) PRESENT_DAY
//            --  FROM HRIS_ATTENDANCE_DETAIL
//            --  WHERE IN_TIME  IS NOT NULL
//            --  OR LEAVE_ID    IS NOT NULL
//            --  OR HOLIDAY_ID  IS NOT NULL
//            --  OR TRAINING_ID IS NOT NULL
//            --  OR TRAVEL_ID   IS NOT NULL
//            --  AND DAYOFF_FLAG = 'N'
//            --  AND EMPLOYEE_ID = {$employeeId}
//            --  AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//            --  GROUP BY EMPLOYEE_ID
//            --  ) PRESENT_TBL
//
//            (SELECT ATTEN.EMPLOYEE_ID,
//              COUNT (*) PRESENT_DAY
//            FROM
//              (SELECT EMPLOYEE_ID
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE (IN_TIME IS NOT NULL
//              OR LEAVE_ID    IS NOT NULL
//              OR HOLIDAY_ID  IS NOT NULL
//              OR TRAINING_ID IS NOT NULL
//              OR TRAVEL_ID   IS NOT NULL)
//              AND DAYOFF_FLAG = 'N'
//              AND EMPLOYEE_ID = {$employeeId}
//              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              ) ATTEN
//            GROUP BY ATTEN.EMPLOYEE_ID) PRESENT_TBL
//
//            ON PRESENT_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- ABSENT DAY
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT (*) ABSENT_DAY
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE IN_TIME   IS NULL
//              AND LEAVE_ID    IS NULL
//              AND HOLIDAY_ID  IS NULL
//              AND TRAINING_ID IS NULL
//              AND TRAVEL_ID   IS NULL
//              AND DAYOFF_FLAG  = 'N'
//              AND EMPLOYEE_ID  = {$employeeId}
//              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              GROUP BY EMPLOYEE_ID
//              ) ABSENT_TBL
//            ON ABSENT_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- LEAVE COUNT
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT (*) LEAVE
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE LEAVE_ID IS NOT NULL
//              AND DAYOFF_FLAG = 'N'
//              AND EMPLOYEE_ID = {$employeeId}
//              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              GROUP BY EMPLOYEE_ID
//              ) LEAVE_TBL
//            ON LEAVE_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- WOH
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT (*) WOH
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE HOLIDAY_ID IS NOT NULL
//              AND IN_TIME      IS NOT NULL
//              AND OUT_TIME     IS NOT NULL
//              AND DAYOFF_FLAG   = 'N'
//              AND EMPLOYEE_ID   = {$employeeId}
//              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              GROUP BY EMPLOYEE_ID
//              ) WOH_TBL
//            ON WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- ON TOUR
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT (*) TOUR
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE TRAVEL_ID IS NOT NULL
//              AND DAYOFF_FLAG  = 'N'
//              AND EMPLOYEE_ID  = {$employeeId}
//              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              GROUP BY EMPLOYEE_ID
//              ) TOUR_TBL
//            ON TOUR_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT (*) TRAINING
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE HOLIDAY_ID IS NULL
//              AND TRAINING_ID  IS NOT NULL
//              AND EMPLOYEE_ID   = {$employeeId}
//              AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//              GROUP BY EMPLOYEE_ID
//              ) TRAINING_TBL
//            ON TRAINING_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- AVERAGE OFFICE HOURS
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                FLOOR(AVERAGE_TOTAL_OFFICE_HRS     /3600) AVG_HOURS,
//                (MOD(AVERAGE_TOTAL_OFFICE_HRS,3600)/60) AVG_MINUTES,
//                AVERAGE_TOTAL_OFFICE_HRS
//              FROM
//                (SELECT EMPLOYEE_ID,
//                  AVG(SYSDATE + (OUT_TIME - IN_TIME)*24*60*60 - SYSDATE) AVERAGE_TOTAL_OFFICE_HRS
//                FROM HRIS_ATTENDANCE_DETAIL
//                WHERE EMPLOYEE_ID = {$employeeId}
//                AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
//                GROUP BY EMPLOYEE_ID
//                )
//              ) AVERAGE_OFFICE_HRS_TBL
//            ON AVERAGE_OFFICE_HRS_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- CURRENT MONTH WOH
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT(*) CUR_MONTH_WOH
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE TO_CHAR (ATTENDANCE_DT, 'MM') = TO_CHAR (SYSDATE, 'MM')
//              AND IN_TIME                        IS NOT NULL
//              AND (DAYOFF_FLAG                    = 'Y'
//              OR HOLIDAY_ID                      IS NOT NULL)
//              AND EMPLOYEE_ID                     = {$employeeId}
//              GROUP BY EMPLOYEE_ID
//              ) CUR_MONTH_WOH_TBL
//            ON CUR_MONTH_WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- PREVIOUS MONTH WOH
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT(*) PREV_MONTH_WOH
//              FROM HRIS_ATTENDANCE_DETAIL
//              WHERE ADD_MONTHS (TRUNC (SYSDATE, 'MM'), -1) = ADD_MONTHS (TRUNC (ATTENDANCE_DT, 'MM'), 0)
//              AND IN_TIME                                 IS NOT NULL
//              AND (DAYOFF_FLAG                             = 'Y'
//              OR HOLIDAY_ID                               IS NOT NULL)
//              AND EMPLOYEE_ID                              = {$employeeId}
//              GROUP BY EMPLOYEE_ID
//              ) PREV_MONTH_WOH_TBL
//            ON PREV_MONTH_WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
//              -- JOINED THIS MONTH
//            LEFT JOIN
//              (SELECT EMPLOYEE_ID,
//                COUNT (*) JOINED_THIS_MONTH
//              FROM HRIS_EMPLOYEES
//              WHERE TO_CHAR(JOIN_DATE, 'YYYYMM') = TO_CHAR(SYSDATE, 'YYYYMM')
//              AND STATUS                         = 'E'
//              GROUP BY EMPLOYEE_ID
//              ) JOINED_THIS_MONTH_TBL
//            ON JOINED_THIS_MONTH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID";
//
//        $statement = $this->adapter->query($sql);
//        $result = $statement->execute()->current();
//        return $result;
//    }

    public function fetchUpcomingHolidays($employeeId = null) {
        if ($employeeId == null) {
            $sql = "SELECT HM.HOLIDAY_ID,
                  HM.HOLIDAY_ENAME,
                  TO_CHAR(HM.START_DATE,'Day, fmddth Month') START_DATE,
                  TO_CHAR(HM.END_DATE,'Day, fmddth Month') END_DATE,
                  HM.HALFDAY,
                  TO_CHAR(HM.START_DATE, 'DAY') WEEK_DAY,
                  HM.START_DATE - TRUNC(SYSDATE) DAYS_REMAINING
                FROM HRIS_HOLIDAY_MASTER_SETUP HM
                WHERE  HM.START_DATE > TRUNC(SYSDATE) ORDER BY HM.START_DATE";
        } else {
            $sql = "SELECT HM.HOLIDAY_ID,
                  HM.HOLIDAY_ENAME,
                  TO_CHAR(HM.START_DATE,'Day, fmddth Month') START_DATE,
                  TO_CHAR(HM.END_DATE,'Day, fmddth Month') END_DATE,
                  HM.HALFDAY,
                  TO_CHAR(HM.START_DATE, 'DAY') WEEK_DAY,
                  HM.START_DATE - TRUNC(SYSDATE) DAYS_REMAINING
                FROM HRIS_HOLIDAY_MASTER_SETUP HM
                JOIN HRIS_EMPLOYEE_HOLIDAY EH
                ON (HM.HOLIDAY_ID   =EH.HOLIDAY_ID)
                WHERE EH.EMPLOYEE_ID={$employeeId} AND HM.START_DATE > TRUNC(SYSDATE) ORDER BY HM.START_DATE";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchEmployeeNotice($employeeId = null) {
        if ($employeeId == null) {
            $sql = "SELECT N.NEWS_ID,
                      N.NEWS_DATE,
                      TO_CHAR(N.NEWS_DATE, 'DD') NEWS_DAY,
                      TO_CHAR(N.NEWS_DATE, 'Mon YYYY') NEWS_MONTH_YEAR,
                      N.NEWS_TITLE,
                      N.NEWS_EDESC
                    FROM HRIS_NEWS N
                    WHERE N.NEWS_DATE   > TRUNC(SYSDATE) - 1";
        } else {
            $sql = "SELECT N.NEWS_ID,
                      N.NEWS_DATE,
                      TO_CHAR(N.NEWS_DATE, 'DD') NEWS_DAY,
                      TO_CHAR(N.NEWS_DATE, 'Mon YYYY') NEWS_MONTH_YEAR,
                      N.NEWS_TITLE,
                      N.NEWS_EDESC
                    FROM HRIS_NEWS N,(SELECT COMPANY_ID,BRANCH_ID,DEPARTMENT_ID, DESIGNATION_ID FROM HRIS_EMPLOYEES WHERE EMPLOYEE_ID ={$employeeId}) E
                    WHERE N.NEWS_DATE   > TRUNC(SYSDATE) - 1
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
                    ORDER BY N.NEWS_DATE ASC";
        }

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchEmployeeTask($employeeId) {
        $sql = "SELECT TSK.TASK_ID,
                        ( CASE
                          WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                          ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                        END ) FULL_NAME, 
                        DSG.DESIGNATION_TITLE,
                        TSK.TASK_EDESC,
                        TSK.END_DATE,
                        TSK.STATUS
                    FROM HRIS_TASK TSK, HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG
                    WHERE 1 = 1
                        AND TSK.EMPLOYEE_ID = EMP.EMPLOYEE_ID
                        AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                        AND TSK.EMPLOYEE_ID = {$employeeId}
                        AND (TSK.END_DATE> TRUNC(SYSDATE) OR TSK.STATUS = 'O')";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return Helper::extractDbData($result);
    }

    public function fetchEmployeesBirthday() {
        $sql = "SELECT * FROM (
                                SELECT EMP.EMPLOYEE_ID,
                                  ( CASE
                                      WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                      ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                                  END ) FULL_NAME, 
                                  DSG.DESIGNATION_TITLE,
                                  EFL.FILE_PATH,
                                  EMP.BIRTH_DATE,
                                  TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE, 
                                  'TODAY' BIRTHDAYFOR
                                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_EMPLOYEE_FILE EFL
                                WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') = TO_CHAR(SYSDATE,'MMDD')
                                AND EMP.RETIRED_FLAG = 'N'
                                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                                AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                                UNION ALL
                                SELECT EMP.EMPLOYEE_ID,
                                  ( CASE
                                      WHEN EMP.MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                      ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                                  END ) FULL_NAME, 
                                  DSG.DESIGNATION_TITLE,
                                  EFL.FILE_PATH,
                                  EMP.BIRTH_DATE,
                                  TO_CHAR(EMP.BIRTH_DATE, 'fmddth Month') EMP_BIRTH_DATE, 
                                  'UPCOMING' BIRTHDAYFOR
                                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_EMPLOYEE_FILE EFL
                                WHERE TO_CHAR(EMP.BIRTH_DATE, 'MMDD') > TO_CHAR(SYSDATE,'MMDD')
                                AND EMP.RETIRED_FLAG = 'N'
                                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                                AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                ) ORDER BY TO_CHAR(BIRTH_DATE,'MMDD')";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $birthdayResult = array();
        foreach ($result as $rs) {
            if ('TODAY' == strtoupper($rs['BIRTHDAYFOR'])) {
                $birthdayResult['TODAY'][$rs['EMPLOYEE_ID']] = $rs;
            }
            if ('UPCOMING' == strtoupper($rs['BIRTHDAYFOR'])) {
                $birthdayResult['UPCOMING'][$rs['EMPLOYEE_ID']] = $rs;
            }
        }

        return $birthdayResult;
    }

    public function fetchEmployeeCalendarData($employeeId, $startDate, $endDate) {
        $rangeClause = "";
        if ($startDate && $endDate) {
            $rangeClause = "AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'YYYY-MM-DD') AND TO_DATE('{$endDate}', 'YYYY-MM-DD')";
        }
        $rangeClause = "AND ATTENDANCE_DT BETWEEN TRUNC(TO_DATE('{$startDate}', 'YYYY-MM-DD'), 'MONTH') AND LAST_DAY(TO_DATE('{$endDate}', 'YYYY-MM-DD'))";


        $sql = "SELECT TO_CHAR(CAL.MONTH_DAY, 'YYYY-MM-DD') MONTH_DAY,
                   ATN.EMPLOYEE_ID,
                   TO_CHAR(ATN.ATTENDANCE_DT, 'YYYY-MM-DD') ATTENDANCE_DT,
                   TO_CHAR(ATN.IN_TIME, 'HH24:MI') IN_TIME,
                   TO_CHAR(ATN.OUT_TIME, 'HH24:MI') OUT_TIME,
                   ATN.LEAVE_ID,
                   LMS.LEAVE_ENAME,
                   ATN.HOLIDAY_ID,
                   HMS.HOLIDAY_ENAME,
                   ATN.TRAINING_ID,
                   TMS.TRAINING_NAME,
                   TO_CHAR(TMS.START_DATE, 'YYYY-MM-DD') TRAINING_START_DATE,
                   TO_CHAR(TMS.END_DATE, 'YYYY-MM-DD') TRAINING_END_DATE,
                   ATN.TRAVEL_ID,
                   ETR.TRAVEL_CODE,
                   TO_CHAR(ETR.FROM_DATE, 'YYYY-MM-DD') TRAVEL_FROM_DATE,
                   TO_CHAR(ETR.TO_DATE, 'YYYY-MM-DD') TRAVEL_TO_DATE,
                   TRIM(TO_CHAR(CAL.MONTH_DAY, 'DAY')) WEEK_DAY,
                   (CASE 
                      WHEN TO_DATE(CAL.MONTH_DAY, 'DD-MON-YY') > TRUNC(SYSDATE)
                        THEN 'NEXT'
                      ELSE 
                        CASE
                          WHEN (ATN.ATTENDANCE_DT IS NULL
                            AND ATN.IN_TIME IS NULL 
                            AND ATN.OUT_TIME IS NULL 
                            AND ATN.LEAVE_ID IS NULL 
                            AND ATN.HOLIDAY_ID IS NULL 
                            AND ATN.TRAINING_ID IS NULL 
                            AND ATN.TRAVEL_ID IS NULL 
                            --AND ATN.DAYOFF_FLAG = 'N'
                            AND TRIM(TO_CHAR(CAL.MONTH_DAY, 'DAY')) = 'SATURDAY'
                            )
                          THEN 'SATURDAY'
                          ELSE
                            CASE 
                              WHEN (ATN.ATTENDANCE_DT IS NULL
                                AND ATN.IN_TIME IS NULL 
                                AND ATN.OUT_TIME IS NULL
                                AND ATN.LEAVE_ID IS NULL
                                AND ATN.HOLIDAY_ID IS NULL
                                AND ATN.TRAINING_ID IS NULL 
                                AND ATN.TRAVEL_ID IS NULL 
                                AND TRIM(TO_CHAR(CAL.MONTH_DAY, 'DAY')) <> 'SATURDAY'
                                )
                              THEN 'ABSENT'
                            ELSE
                            'PRESENT'
                          END
                        END
                   END) ATTENDANCE_STATUS
            FROM (SELECT TRUNC(SYSDATE, 'MONTH') - 1 + ROWNUM AS MONTH_DAY
                  FROM ALL_OBJECTS
                  WHERE TRUNC(SYSDATE, 'MONTH') - 1 + ROWNUM <= LAST_DAY(SYSDATE)) CAL
            LEFT JOIN HRIS_ATTENDANCE_DETAIL ATN ON TRUNC(ATN.ATTENDANCE_DT) = CAL.MONTH_DAY AND ATN.EMPLOYEE_ID = {$employeeId}
            LEFT JOIN HRIS_LEAVE_MASTER_SETUP LMS ON LMS.LEAVE_ID = ATN.LEAVE_ID
            LEFT JOIN HRIS_HOLIDAY_MASTER_SETUP HMS ON HMS.HOLIDAY_ID = ATN.HOLIDAY_ID
            LEFT JOIN HRIS_TRAINING_MASTER_SETUP TMS ON TMS.TRAINING_ID = ATN.TRAINING_ID
            LEFT JOIN HRIS_EMPLOYEE_TRAVEL_REQUEST ETR ON ETR.TRAVEL_ID = ATN.TRAVEL_ID
            WHERE 1 = 1
            ORDER BY CAL.MONTH_DAY ASC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return Helper::extractDbData($result);
    }

    /*
     * END FOR EMPLOYEE DASHBOARD FUNCTIONS
     */

    /*
     * ADMIN DASHBOARD FUNCTIONS
     */

    public function fetchAdminDashboardDetail($employeeId, $date) {
        $sql = "
                  SELECT EMPLOYEE_TBL.*,
                  NVL(LATE_TBL.LATE_IN, 0) LATE_IN,
                  NVL(EARLY_TBL.EARLY_OUT, 0) EARLY_OUT,
                  NVL(MISSED_PUNCH_TBL.MISSED_PUNCH, 0) MISSED_PUNCH,
                  NVL(PRESENT_TBL.PRESENT_DAY, 0) PRESENT_DAY,
                  NVL(ABSENT_TBL.ABSENT_DAY, 0) ABSENT_DAY,
                  NVL(LEAVE_TBL.LEAVE, 0) LEAVE,
                  NVL(WOH_TBL.WOH, 0) WOH,
                  NVL(TOUR_TBL.TOUR, 0) TOUR,
                  NVL(TRAINING_TBL.TRAINING, 0) TRAINING,
                  NVL(JOINED_THIS_MONTH_TBL.JOINED_THIS_MONTH, 0) JOINED_THIS_MONTH
                FROM
                  (SELECT EMP.EMPLOYEE_ID,
                    (
                    CASE
                      WHEN MIDDLE_NAME IS NULL
                      THEN EMP.FIRST_NAME
                        || ' '
                        || EMP.LAST_NAME
                      ELSE EMP.FIRST_NAME
                        || ' '
                        || EMP.MIDDLE_NAME
                        || ' '
                        || EMP.LAST_NAME
                    END ) FULL_NAME,
                    EMP.GENDER_ID,
                    EMP.COMPANY_ID,
                    EMP.BRANCH_ID,
                    EMP.EMAIL_OFFICIAL,
                    EMP.EMAIL_PERSONAL,
                    TO_CHAR(EMP.JOIN_DATE, 'DD-MON-YYYY') JOIN_DATE,
                    TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE) / 12)                                        AS SERVICE_YEARS,
                    TRUNC(MOD(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE), 12))                                    AS SERVICE_MONTHS,
                    TRUNC(SYSDATE) - ADD_MONTHS(EMP.JOIN_DATE, TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE))) AS SERVICE_DAYS,
                    DSG.DESIGNATION_TITLE,
                    EFL.FILE_PATH
                  FROM HRIS_EMPLOYEES EMP,
                    HRIS_DESIGNATIONS DSG,
                    HRIS_EMPLOYEE_FILE EFL
                  WHERE EMP.DESIGNATION_ID   = DSG.DESIGNATION_ID(+)
                  AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                  AND EMP.RETIRED_FLAG       = 'N'
                    -- AND EMP.COMPANY_ID = 2
                  AND EMP.EMPLOYEE_ID = {$employeeId}
                  ) EMPLOYEE_TBL
                  -- JOINED THIS MONTH
                LEFT JOIN
                  (SELECT EMPLOYEE_ID,
                    COUNT (*) JOINED_THIS_MONTH
                  FROM HRIS_EMPLOYEES
                  WHERE TO_CHAR(JOIN_DATE, 'YYYYMM') = TO_CHAR(SYSDATE, 'YYYYMM')
                  AND STATUS                         = 'E'
                  GROUP BY EMPLOYEE_ID
                  ) JOINED_THIS_MONTH_TBL
                ON JOINED_THIS_MONTH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID,
                  -- PRESENT DAY
                  (
                  SELECT COUNT (*) PRESENT_DAY
                  FROM HRIS_ATTENDANCE_DETAIL
                  WHERE (IN_TIME   IS NOT NULL
                  OR LEAVE_ID      IS NOT NULL
                  OR HOLIDAY_ID    IS NOT NULL
                  OR TRAINING_ID   IS NOT NULL
                  OR TRAVEL_ID     IS NOT NULL)
                  AND DAYOFF_FLAG   = 'N'
                  AND ATTENDANCE_DT = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) PRESENT_TBL,
                  -- ABSENT DAY
                  (
                  SELECT COUNT (*) ABSENT_DAY
                  FROM HRIS_ATTENDANCE_DETAIL
                  WHERE IN_TIME    IS NULL
                  AND LEAVE_ID     IS NULL
                  AND HOLIDAY_ID   IS NULL
                  AND TRAINING_ID  IS NULL
                  AND TRAVEL_ID    IS NULL
                  AND DAYOFF_FLAG   = 'N'
                  AND ATTENDANCE_DT = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) ABSENT_TBL,
                  -- LATE IN
                  (
                  SELECT COUNT (*) LATE_IN
                  FROM HRIS_ATTENDANCE_DETAIL ATTEN
                  WHERE (ATTEN.LATE_STATUS = 'L'
                  OR ATTEN.LATE_STATUS     ='B')
                  AND ATTEN.ATTENDANCE_DT  = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) LATE_TBL,
                  -- EARLY OUT
                  (
                  SELECT COUNT (*) EARLY_OUT
                  FROM HRIS_ATTENDANCE_DETAIL ATTEN
                  WHERE (ATTEN.LATE_STATUS = 'E'
                  OR ATTEN.LATE_STATUS     ='B')
                  AND ATTEN.ATTENDANCE_DT  = TRUNC(SYSDATE-1)
                  ) EARLY_TBL,
                  -- MISSED PUNCH
                  (
                  SELECT COUNT(*) MISSED_PUNCH
                  FROM HRIS_ATTENDANCE_DETAIL ATTEN
                  WHERE ATTEN.ATTENDANCE_DT = TRUNC(SYSDATE-1)
                  AND ATTEN.OUT_TIME       IS NULL
                  AND ATTEN.IN_TIME        IS NOT NULL
                  ) MISSED_PUNCH_TBL,
                  -- LEAVE COUNT
                  (
                  SELECT COUNT (*) LEAVE
                  FROM HRIS_ATTENDANCE_DETAIL
                  WHERE LEAVE_ID   IS NOT NULL
                  AND DAYOFF_FLAG   = 'N'
                  AND ATTENDANCE_DT = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) LEAVE_TBL,
                  -- WOH
                  (
                  SELECT EMPLOYEE_ID,
                    COUNT (*) WOH
                  FROM HRIS_ATTENDANCE_DETAIL
                  WHERE (HOLIDAY_ID IS NOT NULL OR DAYOFF_FLAG= 'Y') 
                  AND IN_TIME      IS NOT NULL
                  AND OUT_TIME     IS NOT NULL
                  AND DAYOFF_FLAG   = 'N'
                  AND ATTENDANCE_DT = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) WOH_TBL,
                  -- ON TOUR
                  (
                  SELECT COUNT (*) TOUR
                  FROM HRIS_ATTENDANCE_DETAIL
                  WHERE TRAVEL_ID  IS NOT NULL
                  AND DAYOFF_FLAG   = 'N'
                  AND ATTENDANCE_DT = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) TOUR_TBL,
                  (SELECT COUNT (*) TRAINING
                  FROM HRIS_ATTENDANCE_DETAIL
                  WHERE HOLIDAY_ID IS NULL
                  AND TRAINING_ID  IS NOT NULL
                  AND ATTENDANCE_DT = TO_DATE('{$date}', 'DD-MON-YYYY')
                  ) TRAINING_TBL
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();
        return $result;
    }

    public function fetchAllEmployee($companyId = null, $branchId = null) {
        $sql = "SELECT EMP.EMPLOYEE_ID,
                  EMP.EMPLOYEE_CODE,
                  EMP.FIRST_NAME,
                  EMP.MIDDLE_NAME,
                  EMP.LAST_NAME,
                  ( CASE
                     WHEN MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                     ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                  END ) FULL_NAME,
                  EMP.DESIGNATION_ID,
                  DSG.DESIGNATION_TITLE,
                  EMP.DEPARTMENT_ID,
                  DPT.DEPARTMENT_NAME
                FROM HRIS_EMPLOYEES EMP, HRIS_DESIGNATIONS DSG, HRIS_DEPARTMENTS DPT
                WHERE 1 = 1
                AND EMP.DESIGNATION_ID = DSG.DESIGNATION_ID
                AND EMP.DEPARTMENT_ID = DPT.DEPARTMENT_ID
                AND EMP.STATUS = 'E'
                AND EMP.RETIRED_FLAG = 'N'";

        if ($companyId != null and $branchId != null) {
            $sql .= " AND EMP.COMPANY_ID=$companyId AND EMP.BRANCH_ID=$branchId";
        }

        $sql .= " AND EMP.IS_ADMIN='N'
                ORDER BY UPPER(EMP.FIRST_NAME), UPPER(EMP.MIDDLE_NAME), UPPER(EMP.LAST_NAME)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchGenderHeadCount() {
        $sql = "SELECT COUNT (*) HEAD_COUNT, HE.GENDER_ID, HG.GENDER_NAME
                    FROM HRIS_EMPLOYEES HE, HRIS_GENDERS HG
                   WHERE HE.GENDER_ID(+) = HG.GENDER_ID
                     AND HG.STATUS = 'E'
                     AND HE.RETIRED_FLAG = 'N'
                     --AND HE.COMPANY_ID = :V_COMPANY_ID
                GROUP BY HE.GENDER_ID, HG.GENDER_NAME
                ORDER BY UPPER(HG.GENDER_NAME)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchDepartmentHeadCount() {
        $sql = "SELECT COUNT (*) HEAD_COUNT, HD.DEPARTMENT_ID , HD.DEPARTMENT_NAME
                    FROM HRIS_EMPLOYEES HE, HRIS_DEPARTMENTS HD
                   WHERE HE.DEPARTMENT_ID(+) = HD.DEPARTMENT_ID
                   AND HD.STATUS = 'E'
                   AND HE.RETIRED_FLAG = 'N'
                   --AND HE.COMPANY_ID = :V_COMPANY_ID
                GROUP BY HD.DEPARTMENT_ID, HD.DEPARTMENT_NAME
                ORDER BY UPPER(HD.DEPARTMENT_NAME)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchLocationHeadCount() {
        $sql = "SELECT COUNT (*) HEAD_COUNT, HB.BRANCH_ID , HB.BRANCH_NAME
                    FROM HRIS_EMPLOYEES HE, HRIS_BRANCHES HB
                   WHERE HE.BRANCH_ID(+) = HB.BRANCH_ID
                   AND HB.STATUS = 'E'
                   AND HE.RETIRED_FLAG = 'N'
                   --AND HE.COMPANY_ID = :V_COMPANY_ID
                GROUP BY HB.BRANCH_ID, HB.BRANCH_NAME
                ORDER BY UPPER(HB.BRANCH_NAME)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchDepartmentAttendance() {
        $sql = "SELECT * FROM (
                    SELECT HD.DEPARTMENT_CODE,
                           HD.DEPARTMENT_NAME,
                           'PRESENT' AS PRESENT_STATUS,
                           COUNT(*) ATTN_COUNT
                    FROM HRIS_DEPARTMENTS HD,
                         HRIS_ATTENDANCE_DETAIL HAD,
                         HRIS_EMPLOYEES HE
                    WHERE HD.DEPARTMENT_ID = HE.DEPARTMENT_ID
                      AND HE.EMPLOYEE_ID = HAD.EMPLOYEE_ID
                      AND TRUNC(HAD.ATTENDANCE_DT) = TRUNC(SYSDATE)
                      AND HAD.IN_TIME IS NOT NULL
                    GROUP BY HD.DEPARTMENT_CODE,
                             HD.DEPARTMENT_NAME,
                             'PRESENT'
                    UNION ALL
                    SELECT HD.DEPARTMENT_CODE,
                           HD.DEPARTMENT_NAME,
                           'ABSENT' AS PRESENT_STATUS,
                           COUNT(*) ATTN_COUNT
                    FROM HRIS_DEPARTMENTS HD,
                         HRIS_ATTENDANCE_DETAIL HAD,
                         HRIS_EMPLOYEES HE
                    WHERE HD.DEPARTMENT_ID = HE.DEPARTMENT_ID
                      AND HE.EMPLOYEE_ID = HAD.EMPLOYEE_ID
                      AND TRUNC(HAD.ATTENDANCE_DT) = TRUNC(SYSDATE)
                      AND HAD.IN_TIME IS NULL
                      AND HAD.OUT_TIME IS NULL
                      AND LEAVE_ID IS NULL
                      AND TRAINING_ID IS NULL
                      AND TRAVEL_ID IS NULL
                      AND HOLIDAY_ID IS NULL
                      AND HE.STATUS = 'E'
                      AND HE.IS_ADMIN= 'N'
                    GROUP BY HD.DEPARTMENT_CODE,
                             HD.DEPARTMENT_NAME,
                             'ABSENT'
                )
                ORDER BY UPPER(DEPARTMENT_NAME)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    public function fetchPendingLeave($companyId = null, $branchId = null) {
        $sql = "SELECT COUNT(*)AS PENDING_LEAVE FROM HRIS_EMPLOYEE_LEAVE_REQUEST LA
                LEFT OUTER JOIN HRIS_LEAVE_MASTER_SETUP L
                ON L.LEAVE_ID=LA.LEAVE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E
                ON E.EMPLOYEE_ID=LA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES E1
                ON E1.EMPLOYEE_ID=LA.RECOMMENDED_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES E2
                ON E2.EMPLOYEE_ID=LA.APPROVED_BY
                LEFT OUTER JOIN HRIS_RECOMMENDER_APPROVER RA
                ON LA.EMPLOYEE_ID = RA.EMPLOYEE_ID
                LEFT OUTER JOIN HRIS_EMPLOYEES RECM
                ON RECM.EMPLOYEE_ID = RA.RECOMMEND_BY
                LEFT OUTER JOIN HRIS_EMPLOYEES APRV
                ON APRV.EMPLOYEE_ID = RA.APPROVED_BY
                LEFT OUTER JOIN HRIS_LEAVE_SUBSTITUTE LS
                ON LA.ID          = LS.LEAVE_REQUEST_ID
                WHERE L.STATUS    ='E'
                AND E.STATUS      ='E'
                AND E.RETIRED_FLAG='N'
                AND (E1.STATUS    =
                  CASE
                    WHEN E1.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR E1.STATUS  IS NULL)
                AND (E2.STATUS =
                  CASE
                    WHEN E2.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR E2.STATUS    IS NULL)
                AND (RECM.STATUS =
                  CASE
                    WHEN RECM.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR RECM.STATUS  IS NULL)
                AND (APRV.STATUS =
                  CASE
                    WHEN APRV.STATUS IS NOT NULL
                    THEN ('E')
                  END
                OR APRV.STATUS       IS NULL)
                AND (LS.APPROVED_FLAG =
                  CASE
                    WHEN LS.EMPLOYEE_ID IS NOT NULL
                    THEN ('Y')
                  END
                OR LS.EMPLOYEE_ID IS NULL)
                AND LA.STATUS ='RQ'";

        if ($companyId != null and $branchId != null) {
            $sql .= " AND LA.EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchEmployeeJoiningCurrentMonth($companyId = null, $branchId = null) {
        $sql = "SELECT count(*) AS ECMJ FROM HRIS_EMPLOYEES E,HRIS_MONTH_CODE MC
              WHERE E.JOIN_DATE BETWEEN MC.FROM_DATE AND MC.TO_DATE  
              AND SYSDATE BETWEEN MC.FROM_DATE AND MC.TO_DATE ";

        if ($companyId != null and $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchEmployeeContracts() {
        $sql = "
                SELECT EMP.EMPLOYEE_ID,
                  (
                  CASE
                    WHEN EMP.MIDDLE_NAME IS NULL
                    THEN EMP.FIRST_NAME
                      || ' '
                      || EMP.LAST_NAME
                    ELSE EMP.FIRST_NAME
                      || ' '
                      || EMP.MIDDLE_NAME
                      || ' '
                      || EMP.LAST_NAME
                  END ) AS FULL_NAME,
                  EF.FILE_PATH,
                  D.DESIGNATION_TITLE,
                  S.END_DATE,
                  S.TYPE
                FROM HRIS_EMPLOYEES EMP
                JOIN
                  (SELECT JH.EMPLOYEE_ID,
                    TO_CHAR(JH.START_DATE,'DD-MON-YYYY') AS START_DATE,
                    TO_CHAR(JH.END_DATE,'DD-MON-YYYY') AS END_DATE,
                    JH.TO_DEPARTMENT_ID,
                    JH.TO_DESIGNATION_ID,
                    (
                    CASE
                      WHEN TRUNC(SYSDATE) > JH.END_DATE
                      THEN 'EXPIRED'
                      ELSE 'EXPIRING'
                    END ) AS TYPE
                  FROM HRIS_JOB_HISTORY JH,
                    (SELECT EMPLOYEE_ID,
                      MAX(START_DATE) AS LATEST_START_DATE
                    FROM HRIS_JOB_HISTORY
                    WHERE END_DATE IS NOT NULL
                    AND ABS(TRUNC(END_DATE)-TRUNC(SYSDATE))<=15
                    GROUP BY EMPLOYEE_ID
                    ) LH
                  WHERE JH.EMPLOYEE_ID =LH.EMPLOYEE_ID
                  AND JH.START_DATE    = LH.LATEST_START_DATE
                  ) S
                ON (EMP.EMPLOYEE_ID=S.EMPLOYEE_ID)
                LEFT JOIN HRIS_EMPLOYEE_FILE EF
                ON (EMP.PROFILE_PICTURE_ID=EF.FILE_CODE)
                LEFT JOIN HRIS_DESIGNATIONS D
                ON (S.TO_DESIGNATION_ID=D.DESIGNATION_ID )
                ORDER BY S.END_DATE ASC
                ";


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        $employeeContract = array();
        foreach ($result as $rs) {
            if ('EXPIRED' == strtoupper($rs['TYPE'])) {
                $employeeContract['EXPIRED'][$rs['EMPLOYEE_ID']] = $rs;
            }
            if ('EXPIRING' == strtoupper($rs['TYPE'])) {
                $employeeContract['EXPIRING'][$rs['EMPLOYEE_ID']] = $rs;
            }
        }

        return $employeeContract;
    }

    public function fetchJoinedEmployees() {
        $sql = "
                    SELECT (
                      CASE
                        WHEN E.MIDDLE_NAME IS NULL
                        THEN E.FIRST_NAME
                          || ' '
                          || E.LAST_NAME
                        ELSE E.FIRST_NAME
                          || ' '
                          || E.MIDDLE_NAME
                          || ' '
                          || E.LAST_NAME
                      END ) AS FULL_NAME,
                      EF.FILE_PATH,
                      D.DESIGNATION_TITLE,
                      E.JOIN_DATE
                    FROM HRIS_EMPLOYEES E
                    LEFT JOIN HRIS_EMPLOYEE_FILE EF
                    ON (E.PROFILE_PICTURE_ID=EF.FILE_CODE)
                    LEFT JOIN HRIS_DESIGNATIONS D
                    ON (E.DESIGNATION_ID=D.DESIGNATION_ID )
                   ,
                   (SELECT *
                   FROM HRIS_MONTH_CODE
                   WHERE TRUNC(SYSDATE) BETWEEN FROM_DATE AND TO_DATE
                   ) M
                    WHERE E.JOIN_DATE BETWEEN M.FROM_DATE AND M.TO_DATE
                    ";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    public function fetchLeftEmployees() {
        $sql = "
                SELECT (
                  CASE
                    WHEN E.MIDDLE_NAME IS NULL
                    THEN E.FIRST_NAME
                      || ' '
                      || E.LAST_NAME
                    ELSE E.FIRST_NAME
                      || ' '
                      || E.MIDDLE_NAME
                      || ' '
                      || E.LAST_NAME
                  END ) AS FULL_NAME,
                  EF.FILE_PATH,
                  D.DESIGNATION_TITLE,
                  R.EXIT_DATE,
                  E.JOIN_DATE
                FROM HRIS_EMPLOYEES E
                LEFT JOIN HRIS_EMPLOYEE_FILE EF
                ON (E.PROFILE_PICTURE_ID=EF.FILE_CODE)
                LEFT JOIN HRIS_DESIGNATIONS D
                ON (E.DESIGNATION_ID=D.DESIGNATION_ID ),
                  (SELECT JH.EMPLOYEE_ID,
                    JH.START_DATE AS EXIT_DATE
                  FROM HRIS_JOB_HISTORY JH,
                    (SELECT *
                    FROM HRIS_MONTH_CODE
                    WHERE TRUNC(SYSDATE) BETWEEN FROM_DATE AND TO_DATE
                    ) M
                  WHERE (JH.START_DATE BETWEEN M.FROM_DATE AND M.TO_DATE)
                  AND JH.SERVICE_EVENT_TYPE_ID IN (14,5,8)
                  ) R
                WHERE E.EMPLOYEE_ID= R.EMPLOYEE_ID
";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result;
    }

    /*
     * END FOR ADMIN DASHBOARD FUNCTIONS
     */

    /*
     * MANAGER DASHBOARD FUNCTIONS
     */

    public function fetchPresentCount($companyId = null, $branchId = null) {
        $sql = "
                SELECT COUNT (*) AS PRESENT
                FROM HRIS_ATTENDANCE_DETAIL
                WHERE IN_TIME    IS NOT NULL
                AND ATTENDANCE_DT = TRUNC(SYSDATE) 
             ";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchLeaveCount($companyId = null, $branchId = null) {
        $sql = "SELECT COUNT (*) AS LEAVE
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE    LEAVE_ID IS NOT NULL
              AND ATTENDANCE_DT = TRUNC(SYSDATE) ";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchTrainingCount($companyId = null, $branchId = null) {
        $sql = " SELECT COUNT (*) AS TRAINING 
            FROM HRIS_ATTENDANCE_DETAIL 
            WHERE    TRAINING_ID IS NOT NULL 
            AND ATTENDANCE_DT = TRUNC(SYSDATE)";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchTravelCount($companyId = null, $branchId = null) {
        $sql = "SELECT COUNT (*) AS TRAVEL
              FROM HRIS_ATTENDANCE_DETAIL
             WHERE TRAVEL_ID IS NOT NULL 
             AND DAYOFF_FLAG = 'N' 
             AND ATTENDANCE_DT = TRUNC(SYSDATE)";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchWOHCount($companyId = null, $branchId = null) {
        $sql = "SELECT COUNT (*) AS WOH
              FROM HRIS_ATTENDANCE_DETAIL
              WHERE    HOLIDAY_ID IS NOT NULL
              AND IN_TIME IS NOT NULL 
              AND ATTENDANCE_DT = TRUNC(SYSDATE) ";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchManagerDashboardDetail($employeeId, $date) {
        $sql = "
                SELECT EMPLOYEE_TBL.*,
                  NVL(JOINED_THIS_MONTH_TBL.JOINED_THIS_MONTH, 0) JOINED_THIS_MONTH
                FROM
                  (SELECT EMP.EMPLOYEE_ID,
                    (
                    CASE
                      WHEN MIDDLE_NAME IS NULL
                      THEN EMP.FIRST_NAME
                        || ' '
                        || EMP.LAST_NAME
                      ELSE EMP.FIRST_NAME
                        || ' '
                        || EMP.MIDDLE_NAME
                        || ' '
                        || EMP.LAST_NAME
                    END ) FULL_NAME,
                    EMP.GENDER_ID,
                    EMP.COMPANY_ID,
                    EMP.BRANCH_ID,
                    EMP.EMAIL_OFFICIAL,
                    EMP.EMAIL_PERSONAL,
                    TO_CHAR(EMP.JOIN_DATE, 'DD-MON-YYYY') JOIN_DATE,
                    TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE) / 12)                                        AS SERVICE_YEARS,
                    TRUNC(MOD(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE), 12))                                    AS SERVICE_MONTHS,
                    TRUNC(SYSDATE) - ADD_MONTHS(EMP.JOIN_DATE, TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE))) AS SERVICE_DAYS,
                    DSG.DESIGNATION_TITLE,
                    EFL.FILE_PATH
                  FROM HRIS_EMPLOYEES EMP,
                    HRIS_DESIGNATIONS DSG,
                    HRIS_EMPLOYEE_FILE EFL
                  WHERE EMP.DESIGNATION_ID   = DSG.DESIGNATION_ID(+)
                  AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                  AND EMP.RETIRED_FLAG       = 'N'
                    -- AND EMP.COMPANY_ID = 2
                  AND EMP.EMPLOYEE_ID = {$employeeId}
                  ) EMPLOYEE_TBL
                  -- JOINED THIS MONTH
                LEFT JOIN
                  (SELECT EMPLOYEE_ID,
                    COUNT (*) JOINED_THIS_MONTH
                  FROM HRIS_EMPLOYEES
                  WHERE TO_CHAR(JOIN_DATE, 'YYYYMM') = TO_CHAR(SYSDATE, 'YYYYMM')
                  AND STATUS                         = 'E'
                  GROUP BY EMPLOYEE_ID
                  ) JOINED_THIS_MONTH_TBL
                ON JOINED_THIS_MONTH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID               
";
        $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();
        return $result;
    }

    public function fetchLateInCount($companyId = null, $branchId = null) {
        $sql = "
                SELECT COUNT (*) LATE_IN
                FROM HRIS_ATTENDANCE_DETAIL ATTEN
                WHERE (ATTEN.LATE_STATUS = 'L'
                OR ATTEN.LATE_STATUS     ='B')
                AND ATTEN.ATTENDANCE_DT  = TRUNC(SYSDATE)
";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchEarlyOutCount($companyId = null, $branchId = null) {
        $sql = "
                  SELECT COUNT (*) EARLY_OUT
                  FROM HRIS_ATTENDANCE_DETAIL ATTEN
                  WHERE (ATTEN.LATE_STATUS = 'E'
                  OR ATTEN.LATE_STATUS     ='B')
                  AND ATTEN.ATTENDANCE_DT  = TRUNC(SYSDATE)
";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    public function fetchMissedPunchCount($companyId = null, $branchId = null) {
        $sql = "
                  SELECT COUNT(*) MISSED_PUNCH
                  FROM HRIS_ATTENDANCE_DETAIL ATTEN
                  WHERE ATTEN.ATTENDANCE_DT = TRUNC(SYSDATE)
                  AND ATTEN.OUT_TIME       IS NULL
                  AND ATTEN.IN_TIME        IS NOT NULL
";

        if ($companyId != null && $branchId != null) {
            $sql .= " AND EMPLOYEE_ID IN (SELECT EMPLOYEE_ID FROM HRIS_EMPLOYEES WHERE COMPANY_ID = $companyId AND BRANCH_ID = $branchId)";
        }


        $statement = $this->adapter->query($sql);
        $result = $statement->execute();
        return $result->current();
    }

    /*
     * END FOR MANAGER DASHBOARD FUNCTIONS
     */
}
