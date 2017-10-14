CREATE OR REPLACE PROCEDURE HRIS_PRELOAD_ATTENDANCE(
    V_ATTENDANCE_DATE DATE,
    P_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE:=NULL)
AS
  V_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE;
  V_DAYOFF  VARCHAR2(1 BYTE);
  V_HALFDAY CHAR(1 BYTE);
  V_HOLIDAY_ID HRIS_HOLIDAY_MASTER_SETUP.HOLIDAY_ID%TYPE;
  V_LEAVE_ID HRIS_LEAVE_MASTER_SETUP.LEAVE_ID%TYPE;
  V_TRAINING_ID HRIS_TRAINING_MASTER_SETUP.TRAINING_ID%TYPE;
  V_TRAVEL_ID HRIS_EMPLOYEE_TRAVEL_REQUEST.TRAVEL_ID%TYPE;
  V_MAX_ID                NUMBER;
  V_ATTENDANCE_DATA_COUNT NUMBER;
  V_SHIFT_ID HRIS_SHIFTS.SHIFT_ID%TYPE;
  V_WEEKDAY1 HRIS_SHIFTS.WEEKDAY1%TYPE;
  V_WEEKDAY2 HRIS_SHIFTS.WEEKDAY2%TYPE;
  V_WEEKDAY3 HRIS_SHIFTS.WEEKDAY3%TYPE;
  V_WEEKDAY4 HRIS_SHIFTS.WEEKDAY4%TYPE;
  V_WEEKDAY5 HRIS_SHIFTS.WEEKDAY5%TYPE;
  V_WEEKDAY6 HRIS_SHIFTS.WEEKDAY6%TYPE;
  V_WEEKDAY7 HRIS_SHIFTS.WEEKDAY7%TYPE;
  CURSOR CUR_EMPLOYEE
  IS
    SELECT EMPLOYEE_ID
    FROM HRIS_EMPLOYEES
    WHERE STATUS     ='E'
    AND RETIRED_FLAG ='N'
    AND IS_ADMIN     ='N'
    AND (EMPLOYEE_ID =
      CASE
        WHEN P_EMPLOYEE_ID IS NOT NULL
        THEN P_EMPLOYEE_ID
      END
    OR P_EMPLOYEE_ID IS NULL);
BEGIN
  OPEN CUR_EMPLOYEE;
  LOOP
    FETCH CUR_EMPLOYEE INTO V_EMPLOYEE_ID;
    EXIT
  WHEN CUR_EMPLOYEE%NOTFOUND;
    --
    V_DAYOFF     :='N';
    V_HALFDAY    :='N';
    V_HOLIDAY_ID :=NULL;
    V_LEAVE_ID   :=NULL;
    V_TRAINING_ID:=NULL;
    V_TRAVEL_ID  :=NULL;
    --
    SELECT COUNT (EMPLOYEE_ID)
    INTO V_ATTENDANCE_DATA_COUNT
    FROM HRIS_ATTENDANCE_DETAIL
    WHERE EMPLOYEE_ID          = V_EMPLOYEE_ID
    AND ATTENDANCE_DT          = V_ATTENDANCE_DATE;
    IF V_ATTENDANCE_DATA_COUNT > 0 THEN
      CONTINUE;
    END IF;
    BEGIN
      SELECT HS.SHIFT_ID,
        WEEKDAY1,
        WEEKDAY2,
        WEEKDAY3,
        WEEKDAY4,
        WEEKDAY5,
        WEEKDAY6,
        WEEKDAY7
      INTO V_SHIFT_ID,
        V_WEEKDAY1,
        V_WEEKDAY2,
        V_WEEKDAY3,
        V_WEEKDAY4,
        V_WEEKDAY5,
        V_WEEKDAY6,
        V_WEEKDAY7
      FROM
        (SELECT *
        FROM
          (SELECT *
          FROM HRIS_EMPLOYEE_SHIFT_ASSIGN
          WHERE EMPLOYEE_ID              = V_EMPLOYEE_ID
          AND (TRUNC(V_ATTENDANCE_DATE) >= START_DATE
          AND TRUNC(V_ATTENDANCE_DATE)  <=
            CASE
              WHEN END_DATE IS NOT NULL
              THEN END_DATE
              ELSE TRUNC(V_ATTENDANCE_DATE)
            END)
          ORDER BY START_DATE DESC,
            END_DATE ASC
          )
        WHERE ROWNUM=1
        ) ES,
        HRIS_SHIFTS HS
      WHERE ES.SHIFT_ID = HS.SHIFT_ID;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      BEGIN
        SELECT SHIFT_ID,
          WEEKDAY1,
          WEEKDAY2,
          WEEKDAY3,
          WEEKDAY4,
          WEEKDAY5,
          WEEKDAY6,
          WEEKDAY7
        INTO V_SHIFT_ID,
          V_WEEKDAY1,
          V_WEEKDAY2,
          V_WEEKDAY3,
          V_WEEKDAY4,
          V_WEEKDAY5,
          V_WEEKDAY6,
          V_WEEKDAY7
        FROM HRIS_SHIFTS
        WHERE V_ATTENDANCE_DATE BETWEEN START_DATE AND END_DATE
        AND DEFAULT_SHIFT = 'Y'
        AND STATUS        ='E'
        AND ROWNUM        =1 ;
      EXCEPTION
      WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20344, 'No default and normal shift defined for this time period');
      END;
    END;
    SELECT NVL(MAX (ID),0) + 1 INTO V_MAX_ID FROM HRIS_ATTENDANCE_DETAIL;
    BEGIN
      IF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='1') THEN
        IF V_WEEKDAY1                    = 'DAY_OFF' THEN
          V_DAYOFF                      := 'Y';
        ELSIF V_WEEKDAY1                 ='H' THEN
          V_HALFDAY                     := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='2') THEN
        IF V_WEEKDAY2                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY2                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='3') THEN
        IF V_WEEKDAY3                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY3                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='4') THEN
        IF V_WEEKDAY4                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY4                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='5') THEN
        IF V_WEEKDAY5                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY5                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='6') THEN
        IF V_WEEKDAY6                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY6                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      ELSIF (TO_CHAR(V_ATTENDANCE_DATE,'D') ='7') THEN
        IF V_WEEKDAY7                       = 'DAY_OFF' THEN
          V_DAYOFF                         := 'Y';
        ELSIF V_WEEKDAY7                    ='H' THEN
          V_HALFDAY                        := 'Y';
        END IF;
      END IF;
      IF (V_DAYOFF='Y') THEN
        INSERT
        INTO HRIS_ATTENDANCE_DETAIL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            ID ,
            SHIFT_ID,
            DAYOFF_FLAG,
            OVERALL_STATUS
          )
          VALUES
          (
            V_EMPLOYEE_ID,
            V_ATTENDANCE_DATE,
            V_MAX_ID,
            V_SHIFT_ID,
            'Y',
            'DO'
          );
        COMMIT;
        CONTINUE;
      END IF;
      IF (V_HALFDAY='Y') THEN
        INSERT
        INTO HRIS_ATTENDANCE_DETAIL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            ID ,
            SHIFT_ID,
            HALFDAY_FLAG,
            OVERALL_STATUS
          )
          VALUES
          (
            V_EMPLOYEE_ID,
            V_ATTENDANCE_DATE,
            V_MAX_ID,
            V_SHIFT_ID,
            'Y',
            'AB'
          );
        COMMIT;
        CONTINUE;
      END IF;
    END;
    BEGIN
      SELECT H.HOLIDAY_ID
      INTO V_HOLIDAY_ID
      FROM HRIS_HOLIDAY_MASTER_SETUP H
      JOIN HRIS_EMPLOYEE_HOLIDAY EH
      ON (H.HOLIDAY_ID=EH.HOLIDAY_ID)
      WHERE V_ATTENDANCE_DATE BETWEEN H.START_DATE AND H.END_DATE
      AND EH.EMPLOYEE_ID=V_EMPLOYEE_ID
      AND ROWNUM        <2;
      IF V_HOLIDAY_ID  IS NOT NULL THEN
        INSERT
        INTO HRIS_ATTENDANCE_DETAIL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            HOLIDAY_ID,
            ID,
            SHIFT_ID,
            OVERALL_STATUS
          )
          VALUES
          (
            V_EMPLOYEE_ID,
            V_ATTENDANCE_DATE,
            V_HOLIDAY_ID,
            V_MAX_ID,
            V_SHIFT_ID,
            'HD'
          );
        COMMIT;
        CONTINUE;
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      SELECT L.LEAVE_ID
      INTO V_LEAVE_ID
      FROM HRIS_EMPLOYEE_LEAVE_REQUEST L
      WHERE L.EMPLOYEE_ID = V_EMPLOYEE_ID
      AND V_ATTENDANCE_DATE BETWEEN L.START_DATE AND L.END_DATE
      AND L.STATUS   = 'AP'
      AND ROWNUM     =1;
      IF V_LEAVE_ID IS NOT NULL THEN
        INSERT
        INTO HRIS_ATTENDANCE_DETAIL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            LEAVE_ID,
            ID,
            SHIFT_ID,
            OVERALL_STATUS
          )
          VALUES
          (
            V_EMPLOYEE_ID,
            V_ATTENDANCE_DATE,
            V_LEAVE_ID,
            V_MAX_ID,
            V_SHIFT_ID,
            'LV'
          );
        COMMIT;
        CONTINUE;
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      SELECT TA.TRAINING_ID
      INTO V_TRAINING_ID
      FROM HRIS_EMPLOYEE_TRAINING_ASSIGN TA
      INNER JOIN HRIS_TRAINING_MASTER_SETUP T
      ON TA.TRAINING_ID       = T.TRAINING_ID
      WHERE TA.EMPLOYEE_ID    = V_EMPLOYEE_ID
      AND TA.STATUS           = 'E'
      AND T.IS_WITHIN_COMPANY ='N'
      AND V_ATTENDANCE_DATE BETWEEN T.START_DATE AND T.END_DATE;
      IF V_TRAINING_ID IS NOT NULL THEN
        INSERT
        INTO HRIS_ATTENDANCE_DETAIL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            TRAINING_ID,
            ID,
            SHIFT_ID,
            OVERALL_STATUS
          )
          VALUES
          (
            V_EMPLOYEE_ID,
            V_ATTENDANCE_DATE,
            V_TRAINING_ID,
            V_MAX_ID,
            V_SHIFT_ID,
            'TN'
          );
        COMMIT;
        CONTINUE;
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      SELECT TRAVEL_ID
      INTO V_TRAVEL_ID
      FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
      WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
      AND STATUS        = 'AP'
      AND (V_ATTENDANCE_DATE BETWEEN FROM_DATE AND TO_DATE)
      AND ROWNUM      =1;
      IF V_TRAVEL_ID IS NOT NULL THEN
        INSERT
        INTO HRIS_ATTENDANCE_DETAIL
          (
            EMPLOYEE_ID,
            ATTENDANCE_DT,
            TRAVEL_ID,
            ID,
            SHIFT_ID,
            OVERALL_STATUS
          )
          VALUES
          (
            V_EMPLOYEE_ID,
            V_ATTENDANCE_DATE,
            V_TRAVEL_ID,
            V_MAX_ID,
            V_SHIFT_ID,
            'TV'
          );
        COMMIT;
        CONTINUE;
      END IF;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      NULL;
    END;
    BEGIN
      INSERT
      INTO HRIS_ATTENDANCE_DETAIL
        (
          EMPLOYEE_ID,
          ATTENDANCE_DT,
          ID,
          SHIFT_ID,
          OVERALL_STATUS
        )
        VALUES
        (
          V_EMPLOYEE_ID,
          V_ATTENDANCE_DATE,
          V_MAX_ID,
          V_SHIFT_ID,
          'AB'
        );
      COMMIT;
    END;
  END LOOP;
  CLOSE CUR_EMPLOYEE;
END;