SELECT C.COMPANY_NAME,
  D.DEPARTMENT_NAME,
  A.EMPLOYEE_ID,
  E.FULL_NAME,
  A.DAYOFF,
  A.PRESENT,
  A.HOLIDAY,
  A.LEAVE,
  A.ABSENT,
  NVL(ROUND(OT.TOTAL_MIN/60,2),0) AS OVERTIME_HOUR,
  A.TRAVEL,
  A.TRAINING,
  A.WORK_ON_HOLIDAY,
  A.WORK_ON_DAYOFF
FROM
  (SELECT EMPLOYEE_ID,
    SUM(
    CASE
      WHEN OVERALL_STATUS IN( 'DO','WD')
      THEN 1
      ELSE 0
    END) AS DAYOFF,
    SUM(
    CASE
      WHEN OVERALL_STATUS IN ('PR','BA','LA','TV','VP','TN','TP')
      THEN 1
      ELSE 0
    END) AS PRESENT,
    SUM(
    CASE
      WHEN OVERALL_STATUS IN ('HD','WH')
      THEN 1
      ELSE 0
    END) AS HOLIDAY,
    SUM(
    CASE
      WHEN OVERALL_STATUS IN ('LV','LP')
      THEN 1
      ELSE 0
    END) AS LEAVE,
    SUM(
    CASE
      WHEN OVERALL_STATUS = 'AB'
      THEN 1
      ELSE 0
    END) AS ABSENT,
    SUM(
    CASE
      WHEN OVERALL_STATUS= 'TV'
      THEN 1
      ELSE 0
    END) AS TRAVEL,
    SUM(
    CASE
      WHEN OVERALL_STATUS ='TN'
      THEN 1
      ELSE 0
    END) AS TRAINING,
    SUM(
    CASE
      WHEN OVERALL_STATUS = 'WH'
      THEN 1
      ELSE 0
    END) WORK_ON_HOLIDAY,
    SUM(
    CASE
      WHEN OVERALL_STATUS ='WD'
      THEN 1
      ELSE 0
    END) WORK_ON_DAYOFF
  FROM HRIS_ATTENDANCE_DETAIL
  WHERE (ATTENDANCE_DT BETWEEN '16-JUL-17' AND '16-AUG-17')
  GROUP BY EMPLOYEE_ID
  ) A
JOIN HRIS_EMPLOYEES E
ON(A.EMPLOYEE_ID = E.EMPLOYEE_ID)
JOIN HRIS_COMPANY C
ON(E.COMPANY_ID= C.COMPANY_ID)
JOIN HRIS_DEPARTMENTS D
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
  D.DEPARTMENT_NAME;