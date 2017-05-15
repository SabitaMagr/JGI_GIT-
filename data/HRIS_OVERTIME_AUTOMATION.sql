CREATE OR REPLACE PROCEDURE HRIS_OVERTIME_AUTOMATION(
    V_DATE DATE)
AS
  S_OVERTIME_REQUEST VARCHAR(255 BYTE):= 'OVERTIME_REQUEST';
  V_ADMIN_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE;
  V_SHIFT_ID HRIS_SHIFTS.SHIFT_ID%TYPE;
  V_START_TIME HRIS_SHIFTS.START_TIME%TYPE;
  V_END_TIME HRIS_SHIFTS.END_TIME%TYPE;
  V_LATE_IN HRIS_SHIFTS.LATE_IN%TYPE;
  V_EARLY_OUT HRIS_SHIFTS.EARLY_OUT%TYPE;
  V_TOTAL_WORKING_HR HRIS_SHIFTS.TOTAL_WORKING_HR%TYPE;
  V_ACTUAL_WORKING_HR HRIS_SHIRTS.ACTUAL_WORKING_HR%TYPE;
  V_IN_TIME HRIS_ATTENDANCE_DETAIL.IN_TIME%TYPE;
  V_OUT_TIME HRIS_ATTENDANCE_DETAIL.OUT_TIME%TYPE;
  V_BREAK_TIME               TIMESTAMP;
  V_PUNCH_COUNT              NUMBER;
  V_NON_WORKING_HR           NUMBER;
  V_WORKING_HR               NUMBER;
  V_ACTUAL_WORKING_HR_IN_MIN NUMBER;
  V_TOTAL_WORKING_HR_IN_MIN  NUMBER;
  V_LATE_IN_MIN              NUMBER;
  V_EARLY_OUT_IN_MIN         NUMBER;
  V_BREAK_TIME_IN_MIN        NUMBER;
  V_NON_CONSIDERED_OVERTIME  NUMBER;
  V_OVERTIME                 NUMBER;
  V_CONSTRAINT_VAL_IN_MIN    NUMBER;
BEGIN
  SELECT EMPLOYEE_ID
  INTO V_ADMIN_ID
  FROM HRIS_EMPLOYEES
  WHERE IS_ADMIN  ='Y'
  AND STATUS      ='E'
  AND RETIRED_FLAG='N'
  AND ROWNUM      <2 ;
EXCEPTION
WHEN NO_DATA_FOUND THEN
  RAISE_APPLICATION_ERROR(-20344, 'NO EMPLOYEE IS DEFINED AS ADMIN');
  BEGIN
    FOR CUR_PREF IN
    (SELECT       *
    FROM HRIS_PREFERENCE_SETUP
    WHERE PREFERENCE_NAME=S_OVERTIME_REQUEST
    AND STATUS           ='E'
    )
    LOOP
      BEGIN
        FOR CUR_EMP IN
        (SELECT E.EMPLOYEE_ID                        AS EMPLOYEE_ID,
          S.START_TIME                               AS START_TIME,
          S.END_TIME                                 AS END_TIME,
          S.LATE_IN                                  AS LATE_IN ,
          S.EARLY_OUT                                AS EARLY_OUT,
          S.TOTAL_WORKING_HR                         AS TOTAL_WORKING_HR,
          S.ACTUAL_WORKING_HR                        AS ACUTAL_WORKING_HR,
          S.SHIFT_ID                                 AS SHIFT_ID,
          (S.TOTAL_WORKING_HR - S.ACTUAL_WORKING_HR) AS BREAK_TIME
        FROM HRIS_EMPLOYEES E
        JOIN
          (SELECT * FROM HRIS_EMPLOYEE_SHIFT_ASSIGN WHERE STATUS='E'
          ) SA
        ON (E.EMPLOYEE_ID =SA.EMPLOYEE_ID)
        LEFT JOIN
          (SELECT * FROM HRIS_SHIFTS WHERE STATUS='E'
          ) S
        ON (S.SHIFT_ID=SA.SHIFT_ID)
        LEFT JOIN HRIS_ATTENDANCE_DETAIL AD
        ON (E.EMPLOYEE_ID      =AD.EMPLOYEE_ID)
        WHERE AD.ATTENDANCE_DT = TRUNC(SYSDATE-1)
        AND (V_DATE BETWEEN S.START_DATE AND S.END_DATE)
        AND E.STATUS        = 'E'
        AND E.RETIRED_FLAG  ='N'
        AND E.EMPLOYEE_TYPE = CUR_PREF.EMPLOYEE_TYPE
        )
        LOOP
          BEGIN
            V_SHIFT_ID          := CUR_EMP.SHIFT_ID;
            V_START_TIME        := CUR_EMP.START_TIME;
            V_END_TIME          :=CUR_EMP.END_TIME;
            V_LATE_IN           :=CUR_EMP.LATE_IN;
            V_EARLY_OUT         :=CUR_EMP.EARLY_OUT;
            V_TOTAL_WORKING_HR  := CUR_EMP.TOTAL_WORKING_HR;
            V_ACTUAL_WORKING_HR :=CUR_EMP.ACTUAL_WORKING_HR;
            V_IN_TIME           := CUR_EMP.IN_TIME;
            V_OUT_TIME          :=CUR_EMP.OUT_TIME;
            V_BREAK_TIME        := CUR_EMP.BREAK_TIME;
            IF CUR_EMP.SHIFT_ID IS NULL THEN
              BEGIN
                SELECT SHIFT_ID ,
                  START_TIME,
                  END_TIME,
                  LATE_IN,
                  EARLY_OUT,
                  TOTAL_WORKING_HR,
                  ACTUAL_WORKING_HR,
                  (TOTAL_WORKING_HR-ACTUAL_WORKING_HR)
                INTO V_SHIFT_ID,
                  V_START_TIME,
                  V_END_DATE,
                  V_LATE_IN,
                  V_EARLY_OUT,
                  V_TOTAL_WORKING_HR,
                  V_ACTUAL_WORKING_HR,
                  V_BREAK_TIME
                FROM HRIS_SHIFTS
                WHERE S.STATUS     ='E'
                AND S.DEFAULT_SHIFT='Y'
                AND ROWNUM         <2;
              EXCEPTION
              WHEN NO_DATA_FOUND THEN
                RAISE_APPLICATION_ERROR(-20344, 'NO DEFAULT IS FOUND');
              END;
            END IF;
            --
            SELECT COUNT(*)
            INTO V_PUNCH_COUNT
            FROM HRIS_ATTENDANCE
            WHERE EMPLOYEE_ID      = CUR_EMP.EMPLOYEE_ID
            AND ATTENDANCE_DT      =V_DATE;
            IF MOD(V_PUNCH_COUNT,2)=0 THEN
              --
              DECLARE
                V_COUNTER NUMBER:=1;
              BEGIN
                FOR CUR_OVERTIME       IN
                (SELECT ROUND(TOTAL_MINS/60,0)
                  ||':'
                  ||MOD(TOTAL_MINS,60) TOTAL_HRS,
                  TOTAL_MINS,
                  HR_TYPE
                FROM
                  (SELECT
                    CASE MOD(RNUM,2)
                      WHEN 0
                      THEN 'WORKING'
                      ELSE 'NON-WORKING'
                    END AS HR_TYPE,
                    SUM(ABS(EXTRACT( HOUR FROM DIFF ))*60 + ABS(EXTRACT( MINUTE FROM DIFF ))) TOTAL_MINS
                  FROM
                    (SELECT ROW_NUMBER() OVER ( ORDER BY A.ATTENDANCE_TIME )    AS RNUM,
                      MOD((ROW_NUMBER() OVER ( ORDER BY A.ATTENDANCE_TIME )),2) AS NUM,
                      A.EMPLOYEE_ID,
                      A.IP_ADDRESS,
                      A.ATTENDANCE_DT,
                      A.ATTENDANCE_TIME,
                      (A.ATTENDANCE_TIME - LAG(A.ATTENDANCE_TIME) OVER (ORDER BY A.ATTENDANCE_TIME)) AS DIFF
                    FROM HRIS_ATTENDANCE A
                    WHERE A.EMPLOYEE_ID = CUR_EMP.EMPLOYEE_ID
                    AND A.ATTENDANCE_DT = V_DATE
                    )
                  GROUP BY MOD(RNUM,2)
                  )
                )
                LOOP
                  IF V_COUNTER       = 1 THEN
                    V_NON_WORKING_HR:=CUR_OVERTIME.TOTAL_MINS;
                  ELSE
                    V_WORKING_HR:=CUR_OVERTIME.TOTAL_MINS;
                  END IF;
                  -- NO EXCEPTION HANDLED HERE YET;
                  V_COUNTER:=V_COUNTER+1;
                END LOOP;
              END;
              --
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_ACTUAL_WORKING_HR ))*60 + ABS(EXTRACT( MINUTE FROM V_ACTUAL_WORKING_HR )))
              INTO V_ACTUAL_WORKING_HR_IN_MIN
              FROM DUAL;
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_TOTAL_WORKING_HR ))*60 + ABS(EXTRACT( MINUTE FROM V_TOTAL_WORKING_HR )))
              INTO V_TOTAL_WORKING_HR_IN_MIN
              FROM DUAL;
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_LATE_IN ))*60 + ABS(EXTRACT( MINUTE FROM V_LATE_IN )))
              INTO V_LATE_IN_MIN
              FROM DUAL;
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_EARLY_OUT ))*60 + ABS(EXTRACT( MINUTE FROM V_EARLY_OUT )))
              INTO V_EARLY_OUT_MIN
              FROM DUAL;
              IF V_WORKING_HR > V_ACTUAL_WORKING_HR_IN_MIN THEN
                SELECT SUM(ABS(EXTRACT( HOUR FROM V_BREAK_TIME ))*60 + ABS(EXTRACT( MINUTE FROM V_BREAK_TIME )))
                INTO V_BREAK_TIME_IN_MIN
                FROM DUAL;
                IF V_BREAK_TIME_IN_MIN      > V_NON_WORKING_HR THEN
                  V_NON_CONSIDERED_OVERTIME:=(V_BREAK_TIME_IN_MIN - V_NON_WORKING_HR);
                ELSE
                  V_NON_CONSIDERED_OVERTIME:=0;
                END IF;
                V_OVERTIME:= ( V_WORKING_HR               -V_ACTUAL_WORKING_HR_IN_MIN)-V_NON_CONSIDERED_OVERTIME;
                SELECT SUM(ABS(EXTRACT( HOUR FROM CUR_PREF.CONSTRAINT_VALUE ))*60 + ABS(EXTRACT( MINUTE FROM CUR_PREF.CONSTRAINT_VALUE )))
                INTO V_CONSTRAINT_VAL_IN_MIN
                FROM DUAL;
                
                
                
              END IF;
              --
              --
            ELSE
              dbms_output.put_line(n ||' odd number');
            END IF;
            --
          END;
        END LOOP;
        NULL;
      EXCEPTION
      WHEN OTHERS THEN
        DBMS_OUTPUT.put_line ( 'FAILED ON PREFERENCE_ID : ' || CUR_PREF.PREFERENCE_ID );
      END;
    END LOOP;
  END;
  NULL;
END HRIS_OVERTIME_AUTOMATION;
