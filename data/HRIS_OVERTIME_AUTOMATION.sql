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
  V_ACTUAL_WORKING_HR HRIS_SHIFTS.ACTUAL_WORKING_HR%TYPE;
  V_IN_TIME HRIS_ATTENDANCE_DETAIL.IN_TIME%TYPE;
  V_OUT_TIME HRIS_ATTENDANCE_DETAIL.OUT_TIME%TYPE;
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
  V_PREF_CONDITION           CHAR(1 BYTE);
  V_OVERTIME_ID HRIS_OVERTIME.OVERTIME_ID%TYPE;
  V_OVERTIME_IN_HR     VARCHAR2(10 BYTE);
  V_COUNTER            NUMBER:=1;
  V_PRE_OVERTIME       VARCHAR2(10 BYTE);
  V_PRE_OVERTIME_MIN   NUMBER;
  V_POST_OVERTIME      VARCHAR2(10 BYTE);
  V_POST_OVERTIME_MIN  NUMBER;
  V_OVERTIME_DETAIL_ID NUMBER;
BEGIN
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
  END;
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
        (SELECT E.EMPLOYEE_ID AS EMPLOYEE_ID,
          S.START_TIME        AS START_TIME,
          S.END_TIME          AS END_TIME,
          S.LATE_IN           AS LATE_IN ,
          S.EARLY_OUT         AS EARLY_OUT,
          S.TOTAL_WORKING_HR  AS TOTAL_WORKING_HR,
          S.ACTUAL_WORKING_HR AS ACTUAL_WORKING_HR,
          S.SHIFT_ID          AS SHIFT_ID,
          AD.IN_TIME          AS IN_TIME,
          AD.OUT_TIME         AS OUT_TIME
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
        WHERE AD.ATTENDANCE_DT = V_DATE
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
            IF CUR_EMP.SHIFT_ID IS NULL THEN
              BEGIN
                SELECT SHIFT_ID ,
                  START_TIME,
                  END_TIME,
                  LATE_IN,
                  EARLY_OUT,
                  TOTAL_WORKING_HR,
                  ACTUAL_WORKING_HR
                INTO V_SHIFT_ID,
                  V_START_TIME,
                  V_END_TIME,
                  V_LATE_IN,
                  V_EARLY_OUT,
                  V_TOTAL_WORKING_HR,
                  V_ACTUAL_WORKING_HR
                FROM HRIS_SHIFTS
                WHERE STATUS     ='E'
                AND DEFAULT_SHIFT='Y'
                AND ROWNUM       <2;
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
              BEGIN
                FOR CUR_OVERTIME       IN
                (SELECT TRUNC(TOTAL_MINS/60,0)
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
              IF V_COUNTER != 3 THEN
                CONTINUE;
              END IF;
              V_COUNTER:=1;
              --
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_ACTUAL_WORKING_HR ))*60 + ABS(EXTRACT( MINUTE FROM V_ACTUAL_WORKING_HR )))
              INTO V_ACTUAL_WORKING_HR_IN_MIN
              FROM DUAL;
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_TOTAL_WORKING_HR ))*60 + ABS(EXTRACT( MINUTE FROM V_TOTAL_WORKING_HR )))
              INTO V_TOTAL_WORKING_HR_IN_MIN
              FROM DUAL;
              V_BREAK_TIME_IN_MIN := (V_TOTAL_WORKING_HR_IN_MIN-V_ACTUAL_WORKING_HR_IN_MIN);
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_LATE_IN ))   *60 + ABS(EXTRACT( MINUTE FROM V_LATE_IN )))
              INTO V_LATE_IN_MIN
              FROM DUAL;
              SELECT SUM(ABS(EXTRACT( HOUR FROM V_EARLY_OUT ))*60 + ABS(EXTRACT( MINUTE FROM V_EARLY_OUT )))
              INTO V_EARLY_OUT_IN_MIN
              FROM DUAL;
              IF V_WORKING_HR               > V_ACTUAL_WORKING_HR_IN_MIN THEN
                IF V_BREAK_TIME_IN_MIN      > V_NON_WORKING_HR THEN
                  V_NON_CONSIDERED_OVERTIME:=(V_BREAK_TIME_IN_MIN - V_NON_WORKING_HR);
                ELSE
                  V_NON_CONSIDERED_OVERTIME:=0;
                END IF;
                V_OVERTIME:= ( V_WORKING_HR                                   -V_ACTUAL_WORKING_HR_IN_MIN)-V_NON_CONSIDERED_OVERTIME;
                SELECT SUM(ABS(EXTRACT( HOUR FROM CUR_PREF.CONSTRAINT_VALUE ))*60 + ABS(EXTRACT( MINUTE FROM CUR_PREF.CONSTRAINT_VALUE )))
                INTO V_CONSTRAINT_VAL_IN_MIN
                FROM DUAL;
                V_OVERTIME      :=V_OVERTIME+NVL(V_LATE_IN_MIN,0)+NVL(V_EARLY_OUT_IN_MIN,0);
                V_PREF_CONDITION:=
                CASE
                WHEN ((CUR_PREF.PREFERENCE_CONDITION = 'LESS_THAN') AND (V_OVERTIME <V_CONSTRAINT_VAL_IN_MIN)) OR ((CUR_PREF.PREFERENCE_CONDITION = 'GREATER_THAN') AND (V_OVERTIME >V_CONSTRAINT_VAL_IN_MIN)) OR ((CUR_PREF.PREFERENCE_CONDITION = 'EQUAL') AND (V_OVERTIME =V_CONSTRAINT_VAL_IN_MIN)) THEN
                  'Y'
                ELSE
                  'N'
                END;
                IF V_PREF_CONDITION = 'Y' THEN
                  SELECT NVL(MAX (OVERTIME_ID),0) + 1 INTO V_OVERTIME_ID FROM HRIS_OVERTIME;
                  DBMS_OUTPUT.PUT_LINE(''||V_OVERTIME);
                  SELECT CONCAT( TRUNC(V_OVERTIME/60,0), CONCAT(':',MOD(V_OVERTIME,60)))
                  INTO V_OVERTIME_IN_HR
                  FROM DUAL;
                  INSERT
                  INTO HRIS_OVERTIME
                    (
                      OVERTIME_ID,
                      EMPLOYEE_ID,
                      OVERTIME_DATE,
                      REQUESTED_DATE,
                      DESCRIPTION,
                      REMARKS,
                      STATUS,
                      RECOMMENDED_BY,
                      RECOMMENDED_DATE,
                      RECOMMENDED_REMARKS,
                      APPROVED_BY,
                      APPROVED_DATE,
                      APPROVED_REMARKS,
                      MODIFIED_DATE,
                      TOTAL_HOUR
                    )
                    VALUES
                    (
                      V_OVERTIME_ID ,
                      CUR_EMP.EMPLOYEE_ID,
                      V_DATE,
                      V_DATE,
                      'Overtime Request',
                      NULL,
                      CUR_PREF.REQUEST_TYPE,
                      (
                      CASE CUR_PREF.REQUEST_TYPE
                        WHEN 'AP'
                        THEN V_ADMIN_ID
                        ELSE NULL
                      END),
                      (
                      CASE CUR_PREF.REQUEST_TYPE
                        WHEN 'AP'
                        THEN V_DATE
                        ELSE NULL
                      END),
                      NULL,
                      (
                      CASE CUR_PREF.REQUEST_TYPE
                        WHEN 'AP'
                        THEN V_ADMIN_ID
                        ELSE NULL
                      END),
                      (
                      CASE CUR_PREF.REQUEST_TYPE
                        WHEN 'AP'
                        THEN V_DATE
                        ELSE NULL
                      END),
                      NULL,
                      NULL,
                      TO_DATE(V_OVERTIME_IN_HR,'HH24:MI')
                    );
                  IF (V_IN_TIME != V_START_TIME) AND (V_IN_TIME < V_START_TIME) THEN
                    SELECT NVL(MAX (DETAIL_ID),0) + 1
                    INTO V_OVERTIME_DETAIL_ID
                    FROM HRIS_OVERTIME_DETAIL;
                    SELECT SUM(ABS(EXTRACT( HOUR FROM PRE_OVERTIME ))*60 + ABS(EXTRACT( MINUTE FROM PRE_OVERTIME )))
                    INTO V_PRE_OVERTIME_MIN
                    FROM
                      (SELECT (V_START_TIME-V_IN_TIME) AS PRE_OVERTIME FROM DUAL
                      );
                    SELECT CONCAT( TRUNC(V_PRE_OVERTIME_MIN/60,0), CONCAT(':',MOD(V_PRE_OVERTIME_MIN,60)))
                    INTO V_PRE_OVERTIME
                    FROM DUAL;
                    INSERT
                    INTO HRIS_OVERTIME_DETAIL
                      (
                        DETAIL_ID,
                        OVERTIME_ID,
                        START_TIME,
                        END_TIME,
                        STATUS,
                        CREATED_BY,
                        CREATED_DATE,
                        MODIFIED_BY,
                        MODIFIED_DATE,
                        TOTAL_HOUR
                      )
                      VALUES
                      (
                        V_OVERTIME_DETAIL_ID ,
                        V_OVERTIME_ID,
                        V_IN_TIME,
                        V_START_TIME,
                        'E',
                        CUR_EMP.EMPLOYEE_ID,
                        V_DATE,
                        NULL,
                        NULL,
                        TO_DATE(V_PRE_OVERTIME,'HH24:MI')
                      );
                  END IF;
                  IF (V_OUT_TIME != V_END_TIME) AND (V_END_TIME < V_OUT_TIME) THEN
                    SELECT NVL(MAX (DETAIL_ID),0) + 1
                    INTO V_OVERTIME_DETAIL_ID
                    FROM HRIS_OVERTIME_DETAIL;
                    SELECT SUM(ABS(EXTRACT( HOUR FROM POST_OVERTIME ))*60 + ABS(EXTRACT( MINUTE FROM POST_OVERTIME )))
                    INTO V_POST_OVERTIME_MIN
                    FROM
                      (SELECT (V_OUT_TIME-V_END_TIME) AS POST_OVERTIME FROM DUAL
                      );
                    SELECT CONCAT( TRUNC(V_POST_OVERTIME_MIN/60,0), CONCAT(':',MOD(V_POST_OVERTIME_MIN,60)))
                    INTO V_POST_OVERTIME
                    FROM DUAL;
                    INSERT
                    INTO HRIS_OVERTIME_DETAIL
                      (
                        DETAIL_ID,
                        OVERTIME_ID,
                        START_TIME,
                        END_TIME,
                        STATUS,
                        CREATED_BY,
                        CREATED_DATE,
                        MODIFIED_BY,
                        MODIFIED_DATE,
                        TOTAL_HOUR
                      )
                      VALUES
                      (
                        V_OVERTIME_DETAIL_ID ,
                        V_OVERTIME_ID,
                        V_END_TIME,
                        V_OUT_TIME,
                        'E',
                        CUR_EMP.EMPLOYEE_ID,
                        V_DATE,
                        NULL,
                        NULL,
                        TO_DATE(V_POST_OVERTIME,'HH24:MI')
                      );
                  END IF;
                END IF;
              END IF;
              --
              --
            ELSE
              dbms_output.put_line(' MISS PUNCH ');
            END IF;
            --
          END;
        END LOOP;
      EXCEPTION
      WHEN NO_DATA_FOUND THEN
        DBMS_OUTPUT.put_line ( 'FAILED ON PREFERENCE_ID : ' || CUR_PREF.PREFERENCE_ID );
      END;
    END LOOP;
  END;
END HRIS_OVERTIME_AUTOMATION;
/

