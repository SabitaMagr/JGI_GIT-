<?php

namespace Application\Repository;

use Application\Helper\Helper;
use Application\Model\Model;

class DashboardRepository implements RepositoryInterface {

    private $adapter;

    public function __construct(\Zend\Db\Adapter\AdapterInterface $adapter) {
        $this->adapter = $adapter;
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

    /**
     * Fetches all the data related to display on dashboard related to EMPLOYEE_ID
     *
     * @param int $employeeId
     * @param  string $startDate
     * @param  string $endDate
     * @return array
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
                       NVL(TRAINING_TBL.TRAINING, 0) TRAINING,
                       NVL(AVERAGE_OFFICE_HRS_TBL.AVG_HOURS, 0) AVG_HOURS,
                       NVL(AVERAGE_OFFICE_HRS_TBL.AVG_MINUTES, 0) AVG_MINUTES,
                       NVL(CUR_MONTH_WOH_TBL.CUR_MONTH_WOH, 0) CUR_MONTH_WOH,
                       NVL(PREV_MONTH_WOH_TBL.PREV_MONTH_WOH, 0) PREV_MONTH_WOH,
                       NVL(JOINED_THIS_MONTH_TBL.JOINED_THIS_MONTH, 0) JOINED_THIS_MONTH
                FROM
                  ( SELECT EMP.EMPLOYEE_ID,
                           ( CASE
                                 WHEN MIDDLE_NAME IS NULL THEN EMP.FIRST_NAME || ' ' || EMP.LAST_NAME
                                 ELSE EMP.FIRST_NAME || ' ' || EMP.MIDDLE_NAME || ' ' || EMP.LAST_NAME
                             END ) FULL_NAME,
                           EMP.GENDER_ID,
                           EMP.COMPANY_ID,
                           EMP.BRANCH_ID,
                           EMP.EMAIL_OFFICIAL,
                           EMP.EMAIL_PERSONAL,
                           TO_CHAR(EMP.JOIN_DATE, 'DD-MON-YYYY') JOIN_DATE,
                           TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE) / 12) AS SERVICE_YEARS,
                           TRUNC(MOD(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE), 12)) AS SERVICE_MONTHS,
                           TRUNC(SYSDATE) - ADD_MONTHS(EMP.JOIN_DATE, TRUNC(MONTHS_BETWEEN(SYSDATE, EMP.JOIN_DATE))) AS SERVICE_DAYS,
                           DSG.DESIGNATION_TITLE,
                           EFL.FILE_PATH
                   FROM HRIS_EMPLOYEES EMP,
                        HRIS_DESIGNATIONS DSG,
                        HRIS_EMPLOYEE_FILE EFL
                   WHERE EMP.DEPARTMENT_ID = DSG.DESIGNATION_ID(+)
                     AND EMP.PROFILE_PICTURE_ID = EFL.FILE_CODE(+)
                     AND EMP.RETIRED_FLAG = 'N'
                     -- AND EMP.COMPANY_ID = 2
                     AND EMP.EMPLOYEE_ID = {$employeeId} ) EMPLOYEE_TBL 
                -- LATE IN
                LEFT JOIN
                  ( SELECT ATTEN.EMPLOYEE_ID, COUNT (*) LATE_IN
                   FROM
                     ( SELECT A.EMPLOYEE_ID,
                              S.START_TIME,
                              A.IN_TIME,
                              (((TRUNC (S.START_TIME) - S.START_TIME)) - (TRUNC (A.IN_TIME) - A.IN_TIME) ) LATE_HRS,
                              S.LATE_IN - TRUNC (S.LATE_IN) LATE_GRACE
                      FROM HRIS_ATTENDANCE_DETAIL A,
                           HRIS_SHIFTS S
                      WHERE 1 = 1
                        AND A.EMPLOYEE_ID = {$employeeId}
                        AND A.SHIFT_ID = S.SHIFT_ID
                        AND A.IN_TIME BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY') ) ATTEN
                   WHERE ATTEN.LATE_HRS > LATE_GRACE
                   GROUP BY ATTEN.EMPLOYEE_ID ) LATE_TBL ON LATE_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- EARLY OUT
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) EARLY_OUT
                   FROM
                     ( SELECT A.EMPLOYEE_ID,
                              S.END_TIME,
                              A.OUT_TIME,
                              ((TRUNC (A.OUT_TIME) - A.OUT_TIME) - ((TRUNC (S.END_TIME) - S.END_TIME)) ) EARLY_HRS,
                              S.EARLY_OUT - TRUNC (S.EARLY_OUT) EARLY_GRACE
                      FROM HRIS_ATTENDANCE_DETAIL A,
                           HRIS_SHIFTS S
                      WHERE 1 = 1
                        AND A.EMPLOYEE_ID = {$employeeId}
                        AND A.SHIFT_ID = S.SHIFT_ID
                        AND A.IN_TIME BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY') ) ATTEN
                   WHERE ATTEN.EARLY_HRS > EARLY_GRACE
                   GROUP BY EMPLOYEE_ID ) EARLY_TBL ON EARLY_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- MISSED PUNCH
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT(*) MISSED_PUNCH
                   FROM
                     ( SELECT EMPLOYEE_ID, ATTENDANCE_DT, COUNT (*)
                      FROM HRIS_ATTENDANCE
                      WHERE EMPLOYEE_ID = {$employeeId}
                        AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                      GROUP BY EMPLOYEE_ID,
                               ATTENDANCE_DT
                      HAVING MOD(COUNT(*), 2) <> 0)
                   GROUP BY EMPLOYEE_ID ) MISSED_PUNCH_TBL ON MISSED_PUNCH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- PRESENT DAY
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) PRESENT_DAY
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE IN_TIME IS NOT NULL
                     OR LEAVE_ID IS NOT NULL
                     OR HOLIDAY_ID IS NOT NULL
                     OR TRAINING_ID IS NOT NULL
                     OR TRAVEL_ID IS NOT NULL
                     AND DAYOFF_FLAG = 'N'
                     AND EMPLOYEE_ID = {$employeeId}
                     AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                   GROUP BY EMPLOYEE_ID ) PRESENT_TBL ON PRESENT_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- ABSENT DAY
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) ABSENT_DAY
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE IN_TIME IS NULL
                     AND LEAVE_ID IS NULL
                     AND HOLIDAY_ID IS NULL
                     AND TRAINING_ID IS NULL
                     AND TRAVEL_ID IS NULL
                     AND DAYOFF_FLAG = 'N'
                     AND EMPLOYEE_ID = {$employeeId}
                     AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                   GROUP BY EMPLOYEE_ID ) ABSENT_TBL ON ABSENT_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- LEAVE COUNT
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) LEAVE
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE LEAVE_ID IS NOT NULL
                     AND DAYOFF_FLAG = 'N'
                     AND EMPLOYEE_ID = {$employeeId}
                     AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                   GROUP BY EMPLOYEE_ID ) LEAVE_TBL ON LEAVE_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- WOH
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) WOH
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE HOLIDAY_ID IS NOT NULL
                     AND IN_TIME IS NOT NULL
                     AND OUT_TIME IS NOT NULL
                     AND DAYOFF_FLAG = 'N'
                     AND EMPLOYEE_ID = {$employeeId}
                     AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                   GROUP BY EMPLOYEE_ID ) WOH_TBL ON WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID 
                -- ON TOUR
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) TOUR
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE TRAVEL_ID IS NOT NULL
                     AND DAYOFF_FLAG = 'N'
                     AND EMPLOYEE_ID = {$employeeId}
                     AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                   GROUP BY EMPLOYEE_ID ) TOUR_TBL ON TOUR_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
                LEFT JOIN
                  ( SELECT EMPLOYEE_ID, COUNT (*) TRAINING
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE HOLIDAY_ID IS NULL
                     AND TRAINING_ID IS NOT NULL
                     AND EMPLOYEE_ID = {$employeeId}
                     AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                   GROUP BY EMPLOYEE_ID ) TRAINING_TBL ON TRAINING_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
                -- AVERAGE OFFICE HOURS
                LEFT JOIN
                  (SELECT EMPLOYEE_ID,
                          FLOOR(AVERAGE_TOTAL_OFFICE_HRS/3600) AVG_HOURS,
                          (MOD(AVERAGE_TOTAL_OFFICE_HRS,3600)/60) AVG_MINUTES,
                          AVERAGE_TOTAL_OFFICE_HRS
                   FROM
                     (SELECT EMPLOYEE_ID,
                             AVG(SYSDATE + (OUT_TIME - IN_TIME)*24*60*60 - SYSDATE) AVERAGE_TOTAL_OFFICE_HRS
                      FROM HRIS_ATTENDANCE_DETAIL
                      WHERE EMPLOYEE_ID = {$employeeId}
                        AND ATTENDANCE_DT BETWEEN TO_DATE('{$startDate}', 'DD-MON-YYYY') AND TO_DATE('{$endDate}', 'DD-MON-YYYY')
                      GROUP BY EMPLOYEE_ID)) AVERAGE_OFFICE_HRS_TBL ON AVERAGE_OFFICE_HRS_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
                -- CURRENT MONTH WOH
                LEFT JOIN
                  (SELECT EMPLOYEE_ID, COUNT(*) CUR_MONTH_WOH
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE TO_CHAR (ATTENDANCE_DT, 'MM') = TO_CHAR (SYSDATE, 'MM')
                     AND IN_TIME IS NOT NULL
                     AND (DAYOFF_FLAG = 'Y' OR HOLIDAY_ID IS NOT NULL)
                     AND EMPLOYEE_ID = {$employeeId}
                   GROUP BY EMPLOYEE_ID) CUR_MONTH_WOH_TBL ON CUR_MONTH_WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
                -- PREVIOUS MONTH WOH
                LEFT JOIN
                  (SELECT EMPLOYEE_ID, COUNT(*) PREV_MONTH_WOH
                   FROM HRIS_ATTENDANCE_DETAIL
                   WHERE ADD_MONTHS (TRUNC (SYSDATE, 'MM'), -1) = ADD_MONTHS (TRUNC (ATTENDANCE_DT, 'MM'), 0)
                     AND IN_TIME IS NOT NULL
                     AND (DAYOFF_FLAG = 'Y' OR HOLIDAY_ID IS NOT NULL)
                     AND EMPLOYEE_ID = {$employeeId}
                   GROUP BY EMPLOYEE_ID) PREV_MONTH_WOH_TBL ON PREV_MONTH_WOH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID
                -- JOINED THIS MONTH
                LEFT JOIN
                   (SELECT EMPLOYEE_ID, COUNT (*) JOINED_THIS_MONTH
                    FROM HRIS_EMPLOYEES
                   WHERE TO_CHAR(JOIN_DATE, 'YYYYMM') = TO_CHAR(SYSDATE, 'YYYYMM')
                   AND STATUS = 'E'
                   GROUP BY EMPLOYEE_ID) JOINED_THIS_MONTH_TBL ON JOINED_THIS_MONTH_TBL.EMPLOYEE_ID = EMPLOYEE_TBL.EMPLOYEE_ID";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute()->current();

        return $result;
    }

    /**
     * Fetches all the upcoming holidays
     *
     * @param int $genderId
     * @param int $branchId
     * @return array
     */
    public function fetchUpcomingHolidays($genderId, $branchId) {
        $sql = "SELECT HM.HOLIDAY_ID,
               HM.HOLIDAY_ENAME,
               HM.GENDER_ID,
               HM.BRANCH_ID,
               TO_CHAR(HM.START_DATE,'Day, dth Month') START_DATE,
               TO_CHAR(HM.END_DATE,'Day, dth Month') END_DATE,
               HM.HALFDAY,
               TO_CHAR(HM.START_DATE, 'DAY') WEEK_DAY,
               HM.START_DATE - TRUNC(SYSDATE) DAYS_REMAINING
        FROM HRIS_HOLIDAY_MASTER_SETUP HM
        WHERE 1 = 1
          AND TRUNC(SYSDATE)-1 < HM.START_DATE
          AND (HM.GENDER_ID IS NULL
               OR HM.GENDER_ID = {$genderId})
          AND (HM.BRANCH_ID IS NULL
               OR HM.BRANCH_ID = {$branchId})
        ORDER BY HM.START_DATE";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

    /**
     * @return mixed
     */
    public function fetchEmployeeNotice() {
        $sql = "SELECT NEWS_ID,
                       NEWS_DATE,
                       TO_CHAR(NEWS_DATE, 'DD') NEWS_DAY,
                       TO_CHAR(NEWS_DATE, 'Mon YYYY') NEWS_MONTH_YEAR,
                       NEWS_TITLE,
                       NEWS_EDESC
                FROM HRIS_NEWS
                WHERE NEWS_TYPE = 'NOTICE'
                  AND NEWS_DATE > TRUNC(SYSDATE) - 1
                  -- AND COMPANY_ID = :V_COMPANY_CODE
                  -- AND BRANCH_ID = :V_BRANCH_CODE
                  -- AND DESIGNATION_ID = :V_DESIGNATION_ID
                  -- AND DEPARTMENT_ID = :V_DEPARTMENT_ID
                ORDER BY NEWS_DATE ASC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
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
                                  TO_CHAR(EMP.BIRTH_DATE, 'DD Month') EMP_BIRTH_DATE, 
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
                                  TO_CHAR(EMP.BIRTH_DATE, 'DD Month') EMP_BIRTH_DATE, 
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
        foreach($result as $rs) {
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
        $sql = "SELECT AD.EMPLOYEE_ID,
                       TO_CHAR(ATTENDANCE_DT, 'YYYY-MM-DD') ATTENDANCE_DT,
                       TO_CHAR(IN_TIME, 'HH24:MI') IN_TIME,
                       TO_CHAR(OUT_TIME, 'HH24:MI') OUT_TIME,
                       AD.LEAVE_ID,
                       LMS.LEAVE_ENAME,
                       AD.HOLIDAY_ID,
                       HMS.HOLIDAY_ENAME,
                       AD.TRAINING_ID,
                       TMS.TRAINING_NAME,
                       TR.TRAVEL_ID,
                       TR.TRAVEL_CODE      
                  FROM HRIS_ATTENDANCE_DETAIL AD, HRIS_LEAVE_MASTER_SETUP LMS, HRIS_HOLIDAY_MASTER_SETUP HMS, HRIS_TRAINING_MASTER_SETUP TMS, HRIS_EMPLOYEE_TRAVEL_REQUEST TR
                WHERE AD.EMPLOYEE_ID = {$employeeId}
                {$rangeClause}
                AND AD.LEAVE_ID = LMS.LEAVE_ID(+)
                AND AD.HOLIDAY_ID = HMS.HOLIDAY_ID(+)
                AND AD.TRAINING_ID = TMS.TRAINING_ID(+)
                AND AD.TRAVEL_ID = TR.TRAVEL_ID(+)
                ORDER BY ATTENDANCE_DT DESC";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return Helper::extractDbData($result);
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

    public function fetchAllEmployee() {
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
                AND EMP.RETIRED_FLAG = 'N'
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
                      AND TRUNC(HAD.ATTENDANCE_DT) = TO_DATE(SYSDATE, 'DD-MON-YYYY')
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
                      AND TRUNC(HAD.ATTENDANCE_DT) = TO_DATE(SYSDATE, 'DD-MON-YYYY')
                      AND HAD.IN_TIME IS NULL
                      AND HAD.OUT_TIME IS NULL
                      AND LEAVE_ID IS NULL
                      AND TRAINING_ID IS NULL
                      AND TRAVEL_ID IS NULL
                      AND HOLIDAY_ID IS NULL
                    GROUP BY HD.DEPARTMENT_CODE,
                             HD.DEPARTMENT_NAME,
                             'ABSENT'
                )
                ORDER BY UPPER(DEPARTMENT_NAME)";

        $statement = $this->adapter->query($sql);
        $result = $statement->execute();

        return $result;
    }

}
