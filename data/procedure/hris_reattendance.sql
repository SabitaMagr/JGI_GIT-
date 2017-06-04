create or replace PROCEDURE HRIS_REATTENDANCE(
    P_ATTENDANCE_DT DATE)
AS
  V_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE;
  V_IN_TIME HRIS_ATTENDANCE_DETAIL.IN_TIME%TYPE;
  V_OUT_TIME HRIS_ATTENDANCE_DETAIL.OUT_TIME%TYPE;
  CURSOR CUR_EMPLOYEE
  IS
    SELECT EMPLOYEE_ID
    FROM HRIS_EMPLOYEES
    WHERE STATUS    ='E'
    AND RETIRED_FLAG='N'
    AND IS_ADMIN    ='N';
BEGIN
  DELETE
  FROM HRIS_ATTENDANCE_DETAIL
  WHERE ATTENDANCE_DT= TRUNC(P_ATTENDANCE_DT);
  HRIS_PRELOAD_ATTENDANCE(P_ATTENDANCE_DT);
  OPEN CUR_EMPLOYEE;
  LOOP
    FETCH CUR_EMPLOYEE INTO V_EMPLOYEE_ID;
    EXIT
  WHEN CUR_EMPLOYEE%NOTFOUND;
    SELECT MIN(ATTENDANCE_TIME) AS IN_TIME,
      MAX(ATTENDANCE_TIME) OUT_TIME
    INTO V_IN_TIME,
      V_OUT_TIME
    FROM HRIS_ATTENDANCE
    WHERE ATTENDANCE_DT =TRUNC(P_ATTENDANCE_DT)
    AND EMPLOYEE_ID     = V_EMPLOYEE_ID ;
    IF V_IN_TIME       IS NULL THEN
      CONTINUE;
    END IF ;
    IF V_IN_TIME  = V_OUT_TIME THEN
      V_OUT_TIME := NULL;
    END IF;
    UPDATE HRIS_ATTENDANCE_DETAIL
    SET IN_TIME      = V_IN_TIME ,
      OUT_TIME       =V_OUT_TIME
    WHERE EMPLOYEE_ID= V_EMPLOYEE_ID
    AND ATTENDANCE_DT= TRUNC(P_ATTENDANCE_DT);
  END LOOP;
  CLOSE CUR_EMPLOYEE;
END;