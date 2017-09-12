create or replace PROCEDURE HRIS_MANUAL_ATTENDANCE(
    P_ID HRIS_ATTENDANCE_DETAIL.ID%TYPE ,
    P_STATUS CHAR )
AS
BEGIN
  FOR attendance IN
  (SELECT A.EMPLOYEE_ID,
    A.ATTENDANCE_DT,
    S.START_TIME,
    S.END_TIME,
    A.OVERALL_STATUS
  FROM HRIS_ATTENDANCE_DETAIL A
  JOIN HRIS_SHIFTS S
  ON (A.SHIFT_ID=S.SHIFT_ID)
  WHERE ID      = P_ID
  )
  LOOP
    IF P_STATUS ='P' AND attendance.OVERALL_STATUS= 'AB' THEN
      INSERT
      INTO HRIS_ATTENDANCE
        (
          EMPLOYEE_ID,
          ATTENDANCE_DT,
          ATTENDANCE_TIME,
          ATTENDANCE_FROM
        )
        VALUES
        (
          attendance.EMPLOYEE_ID,
          attendance.ATTENDANCE_DT,
          TO_DATE(TO_CHAR(attendance.ATTENDANCE_DT,'DD-MON-YYYY')
          ||' '
          ||TO_CHAR(attendance.START_TIME,'HH24:MI'),'DD-MON-YYYY HH24:MI' ),
          'SYSTEM'
        );
      INSERT
      INTO HRIS_ATTENDANCE
        (
          EMPLOYEE_ID,
          ATTENDANCE_DT,
          ATTENDANCE_TIME,
          ATTENDANCE_FROM
        )
        VALUES
        (
          attendance.EMPLOYEE_ID,
          attendance.ATTENDANCE_DT,
          TO_DATE(TO_CHAR(attendance.ATTENDANCE_DT,'DD-MON-YYYY')
          ||' '
          ||TO_CHAR(attendance.END_TIME,'HH24:MI'),'DD-MON-YYYY HH24:MI' ),
          'SYSTEM'
        );
      HRIS_REATTENDANCE(attendance.ATTENDANCE_DT,attendance.EMPLOYEE_ID);
    END IF;
    

  END LOOP;
END;