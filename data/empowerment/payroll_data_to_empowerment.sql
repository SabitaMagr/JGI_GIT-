BEGIN
  FOR report IN
  (SELECT C.COMPANY_CODE,
    C.COMPANY_CODE
    ||'.01' AS BRANCH_CODE,
    C.COMPANY_NAME,
    D.DEPARTMENT_NAME,
    A.EMPLOYEE_ID,
    E.FULL_NAME,
    A.DAYOFF,
    A.PRESENT,
    A.HOLIDAY,
    A.LEAVE,
    A.PAID_LEAVE,
    A.UNPAID_LEAVE,
    A.ABSENT,
    A.DAYOFF              +A.PRESENT+A.HOLIDAY+A.UNPAID_LEAVE+A.PAID_LEAVE+A.ABSENT AS TOTAL_DAYS,
    NVL(ROUND(OT.TOTAL_MIN/60,2),0)                                                 AS OVERTIME_HOUR,
    A.TRAVEL,
    A.TRAINING,
    A.WORK_ON_HOLIDAY,
    A.WORK_ON_DAYOFF
  FROM
    (SELECT A.EMPLOYEE_ID,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS IN( 'DO','WD')
        THEN 1
        ELSE 0
      END) AS DAYOFF,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS IN ('PR','BA','LA','TV','VP','TN','TP')
        THEN 1
        ELSE 0
      END) AS PRESENT,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS IN ('HD','WH')
        THEN 1
        ELSE 0
      END) AS HOLIDAY,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS IN ('LV','LP')
        THEN 1
        ELSE 0
      END) AS LEAVE,
      SUM(
      CASE
        WHEN L.PAID = 'Y'
        THEN 1
        ELSE 0
      END) AS PAID_LEAVE,
      SUM(
      CASE
        WHEN L.PAID = 'N'
        THEN 1
        ELSE 0
      END) AS UNPAID_LEAVE,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS = 'AB'
        THEN 1
        ELSE 0
      END) AS ABSENT,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS= 'TV'
        THEN 1
        ELSE 0
      END) AS TRAVEL,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS ='TN'
        THEN 1
        ELSE 0
      END) AS TRAINING,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS = 'WH'
        THEN 1
        ELSE 0
      END) WORK_ON_HOLIDAY,
      SUM(
      CASE
        WHEN A.OVERALL_STATUS ='WD'
        THEN 1
        ELSE 0
      END) WORK_ON_DAYOFF
    FROM HRIS_ATTENDANCE_DETAIL A
    LEFT JOIN HRIS_LEAVE_MASTER_SETUP L
    ON (A.LEAVE_ID= L.LEAVE_ID)
    WHERE (A.ATTENDANCE_DT BETWEEN '16-JUL-17' AND '16-AUG-17')
    GROUP BY A.EMPLOYEE_ID
    ) A
  LEFT JOIN HRIS_EMPLOYEES E
  ON(A.EMPLOYEE_ID = E.EMPLOYEE_ID)
  LEFT JOIN HRIS_COMPANY C
  ON(E.COMPANY_ID= C.COMPANY_ID)
  LEFT JOIN HRIS_DEPARTMENTS D
  ON (E.DEPARTMENT_ID= D.DEPARTMENT_ID)
  LEFT JOIN
    (SELECT EMPLOYEE_ID,
      SUM(TOTAL_HOUR) AS TOTAL_MIN
    FROM HRIS_OVERTIME
    WHERE (OVERTIME_DATE BETWEEN '16-JUL-17' AND '16-AUG-17')
    AND STATUS= 'AP'
    GROUP BY EMPLOYEE_ID
    ) OT
  ON (A.EMPLOYEE_ID = OT.EMPLOYEE_ID)
  ORDER BY C.COMPANY_NAME,
    D.DEPARTMENT_NAME
  )
  LOOP
    INSERT
    INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
      (
        PAY_CODE,
        EMPLOYEE_CODE,
        PERIOD_DT_CODE,
        MODIFY_VALUE,
        COMPANY_CODE,
        BRANCH_CODE,
        CREATED_BY,
        CREATED_DATE,
        SALARY_TYPE
      )
      VALUES
      (
        'TD',
        report.EMPLOYEE_ID,
        '1',
        report.DAYOFF+report.PRESENT+report.HOLIDAY+report.UNPAID_LEAVE+report.PAID_LEAVE+report.ABSENT,
        report.COMPANY_CODE,
        report.BRANCH_CODE,
        'SYSTEM',
        SYSDATE,
        0
      );
    --
    INSERT
    INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
      (
        PAY_CODE,
        EMPLOYEE_CODE,
        PERIOD_DT_CODE,
        MODIFY_VALUE,
        COMPANY_CODE,
        BRANCH_CODE,
        CREATED_BY,
        CREATED_DATE,
        SALARY_TYPE
      )
      VALUES
      (
        'PD',
        report.EMPLOYEE_ID,
        '1',
        report.PRESENT,
        report.COMPANY_CODE,
        report.BRANCH_CODE,
        'SYSTEM',
        SYSDATE,
        0
      );
    INSERT
    INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
      (
        PAY_CODE,
        EMPLOYEE_CODE,
        PERIOD_DT_CODE,
        MODIFY_VALUE,
        COMPANY_CODE,
        BRANCH_CODE,
        CREATED_BY,
        CREATED_DATE,
        SALARY_TYPE
      )
      VALUES
      (
        'AD',
        report.EMPLOYEE_ID,
        '1',
        report.ABSENT,
        report.COMPANY_CODE,
        report.BRANCH_CODE,
        'SYSTEM',
        SYSDATE,
        0
      );
    INSERT
    INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
      (
        PAY_CODE,
        EMPLOYEE_CODE,
        PERIOD_DT_CODE,
        MODIFY_VALUE,
        COMPANY_CODE,
        BRANCH_CODE,
        CREATED_BY,
        CREATED_DATE,
        SALARY_TYPE
      )
      VALUES
      (
        'HD',
        report.EMPLOYEE_ID,
        '1',
        report.HOLIDAY+report.DAYOFF,
        report.COMPANY_CODE,
        report.BRANCH_CODE,
        'SYSTEM',
        SYSDATE,
        0
      );
    IF(report.PAID_LEAVE !=0) THEN
      INSERT
      INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
        (
          PAY_CODE,
          EMPLOYEE_CODE,
          PERIOD_DT_CODE,
          MODIFY_VALUE,
          COMPANY_CODE,
          BRANCH_CODE,
          CREATED_BY,
          CREATED_DATE,
          SALARY_TYPE
        )
        VALUES
        (
          'PL',
          report.EMPLOYEE_ID,
          '1',
          report.PAID_LEAVE,
          report.COMPANY_CODE,
          report.BRANCH_CODE,
          'SYSTEM',
          SYSDATE,
          0
        );
    END IF;
    IF(report.UNPAID_LEAVE !=0) THEN
      INSERT
      INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
        (
          PAY_CODE,
          EMPLOYEE_CODE,
          PERIOD_DT_CODE,
          MODIFY_VALUE,
          COMPANY_CODE,
          BRANCH_CODE,
          CREATED_BY,
          CREATED_DATE,
          SALARY_TYPE
        )
        VALUES
        (
          'UL',
          report.EMPLOYEE_ID,
          '1',
          report.UNPAID_LEAVE,
          report.COMPANY_CODE,
          report.BRANCH_CODE,
          'SYSTEM',
          SYSDATE,
          0
        );
    END IF;
    IF(report.OVERTIME_HOUR !=0) THEN
      INSERT
      INTO JGIERP.HR_MONTHLY_MODIFIED_PAY_VALUE
        (
          PAY_CODE,
          EMPLOYEE_CODE,
          PERIOD_DT_CODE,
          MODIFY_VALUE,
          COMPANY_CODE,
          BRANCH_CODE,
          CREATED_BY,
          CREATED_DATE,
          SALARY_TYPE
        )
        VALUES
        (
          'OT',
          report.EMPLOYEE_ID,
          '1',
          report.OVERTIME_HOUR,
          report.COMPANY_CODE,
          report.BRANCH_CODE,
          'SYSTEM',
          SYSDATE,
          0
        );
    END IF;
  END LOOP;
END;


