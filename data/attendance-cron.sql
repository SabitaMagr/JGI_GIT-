create or replace PROCEDURE HRIS_PRELOAD_ATTENDANCE(
    V_ATTENDANCE_DATE DATE)
AS
  V_EMPLOYEE_ID HRIS_EMPLOYEES.EMPLOYEE_ID%TYPE;
  V_GENDER_ID HRIS_EMPLOYEES.GENDER_ID%TYPE;
  V_BRANCH_ID HRIS_EMPLOYEES.BRANCH_ID%TYPE;
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
  V_DAYOFF VARCHAR2(1 BYTE);
  CURSOR CUR_EMPLOYEE
  IS
    SELECT EMPLOYEE_ID, GENDER_ID, BRANCH_ID FROM HRIS_EMPLOYEES WHERE STATUS='E' AND RETIRED_FLAG='N' AND IS_ADMIN='N';
BEGIN
  OPEN CUR_EMPLOYEE;
  LOOP
    FETCH CUR_EMPLOYEE INTO V_EMPLOYEE_ID, V_GENDER_ID, V_BRANCH_ID;
    EXIT
  WHEN CUR_EMPLOYEE%NOTFOUND;
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
      FROM HRIS_EMPLOYEE_SHIFT_ASSIGN ES,
        HRIS_SHIFTS HS
      WHERE 1            = 1
      AND ES.EMPLOYEE_ID = V_EMPLOYEE_ID
      AND V_ATTENDANCE_DATE BETWEEN HS.START_DATE AND HS.END_DATE
      AND HS.CURRENT_SHIFT = 'Y'
      AND HS.STATUS        = 'E'
      AND ES.SHIFT_ID      = HS.SHIFT_ID
      AND ROWNUM           <2;
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
        AND CURRENT_SHIFT = 'Y'
        AND ROWNUM        <2 ;
      EXCEPTION
      WHEN TOO_MANY_ROWS THEN
        RAISE_APPLICATION_ERROR (-20343, 'Many default and normal shifts');
      WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20344, 'No default and normal shift defined for this time period');
      END;
    END;
    IF V_WEEKDAY1    = 'DAY_OFF' THEN
      V_DAYOFF      := '1';
    ELSIF V_WEEKDAY2 = 'DAY_OFF' THEN
      V_DAYOFF      :='2';
    ELSIF V_WEEKDAY3 = 'DAY_OFF' THEN
      V_DAYOFF      :='3';
    ELSIF V_WEEKDAY4 = 'DAY_OFF' THEN
      V_DAYOFF      :='4';
    ELSIF V_WEEKDAY5 = 'DAY_OFF' THEN
      V_DAYOFF      :='5';
    ELSIF V_WEEKDAY6 = 'DAY_OFF' THEN
      V_DAYOFF      :='6';
    ELSE
      V_DAYOFF:='7';
    END IF;
    BEGIN
      SELECT COUNT (EMPLOYEE_ID)
      INTO V_ATTENDANCE_DATA_COUNT
      FROM HRIS_ATTENDANCE_DETAIL
      WHERE EMPLOYEE_ID          = V_EMPLOYEE_ID
      AND ATTENDANCE_DT          = V_ATTENDANCE_DATE;
      IF V_ATTENDANCE_DATA_COUNT = 0 THEN
        BEGIN
          SELECT NVL(MAX (ID),0) + 1 INTO V_MAX_ID FROM HRIS_ATTENDANCE_DETAIL;
          IF (TO_CHAR(V_ATTENDANCE_DATE,'D') = V_DAYOFF) THEN
            INSERT
            INTO HRIS_ATTENDANCE_DETAIL
              (
                EMPLOYEE_ID,
                ATTENDANCE_DT,
                ID ,
                SHIFT_ID,
                DAYOFF_FLAG
              )
              VALUES
              (
                V_EMPLOYEE_ID,
                V_ATTENDANCE_DATE,
                V_MAX_ID,
                V_SHIFT_ID,
                'Y'
              );
            COMMIT;
            CONTINUE;
          END IF;
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
                  SHIFT_ID
                )
                VALUES
                (
                  V_EMPLOYEE_ID,
                  V_ATTENDANCE_DATE,
                  V_HOLIDAY_ID,
                  V_MAX_ID,
                  V_SHIFT_ID
                );
              COMMIT;
            END IF;
          EXCEPTION
          WHEN NO_DATA_FOUND THEN
            BEGIN
              SELECT L.LEAVE_ID
              INTO V_LEAVE_ID
              FROM HRIS_EMPLOYEE_LEAVE_REQUEST L
              WHERE L.EMPLOYEE_ID = V_EMPLOYEE_ID
              AND V_ATTENDANCE_DATE BETWEEN L.START_DATE AND L.END_DATE
              AND L.STATUS   = 'AP';
              IF V_LEAVE_ID IS NOT NULL THEN
                INSERT
                INTO HRIS_ATTENDANCE_DETAIL
                  (
                    EMPLOYEE_ID,
                    ATTENDANCE_DT,
                    LEAVE_ID,
                    ID,
                    SHIFT_ID
                  )
                  VALUES
                  (
                    V_EMPLOYEE_ID,
                    V_ATTENDANCE_DATE,
                    V_LEAVE_ID,
                    V_MAX_ID,
                    V_SHIFT_ID
                  );
                COMMIT;
              END IF;
            EXCEPTION
            WHEN NO_DATA_FOUND THEN
              BEGIN
                SELECT TA.TRAINING_ID
                INTO V_TRAINING_ID
                FROM HRIS_EMPLOYEE_TRAINING_ASSIGN TA
                INNER JOIN HRIS_TRAINING_MASTER_SETUP T
                ON TA.TRAINING_ID    = T.TRAINING_ID
                WHERE TA.EMPLOYEE_ID = V_EMPLOYEE_ID
                AND TA.STATUS        = 'E'
                AND V_ATTENDANCE_DATE BETWEEN T.START_DATE AND T.END_DATE;
                IF V_TRAINING_ID IS NOT NULL THEN
                  INSERT
                  INTO HRIS_ATTENDANCE_DETAIL
                    (
                      EMPLOYEE_ID,
                      ATTENDANCE_DT,
                      TRAINING_ID,
                      ID,
                      SHIFT_ID
                    )
                    VALUES
                    (
                      V_EMPLOYEE_ID,
                      V_ATTENDANCE_DATE,
                      V_TRAINING_ID,
                      V_MAX_ID,
                      V_SHIFT_ID
                    );
                  COMMIT;
                END IF;
              EXCEPTION
              WHEN NO_DATA_FOUND THEN
                BEGIN
                  SELECT TRAVEL_ID
                  INTO V_TRAVEL_ID
                  FROM HRIS_EMPLOYEE_TRAVEL_REQUEST
                  WHERE EMPLOYEE_ID = V_EMPLOYEE_ID
                  AND STATUS        = 'AP'
                  AND V_ATTENDANCE_DATE BETWEEN FROM_DATE AND TO_DATE;
                  IF V_TRAVEL_ID IS NOT NULL THEN
                    INSERT
                    INTO HRIS_ATTENDANCE_DETAIL
                      (
                        EMPLOYEE_ID,
                        ATTENDANCE_DT,
                        TRAVEL_ID,
                        ID,
                        SHIFT_ID
                      )
                      VALUES
                      (
                        V_EMPLOYEE_ID,
                        V_ATTENDANCE_DATE,
                        V_TRAVEL_ID,
                        V_MAX_ID,
                        V_SHIFT_ID
                      );
                    COMMIT;
                  END IF;
                EXCEPTION
                WHEN NO_DATA_FOUND THEN
                  INSERT
                  INTO HRIS_ATTENDANCE_DETAIL
                    (
                      EMPLOYEE_ID,
                      ATTENDANCE_DT,
                      ID,
                      SHIFT_ID
                    )
                    VALUES
                    (
                      V_EMPLOYEE_ID,
                      V_ATTENDANCE_DATE,
                      V_MAX_ID,
                      V_SHIFT_ID
                    );
                  COMMIT;
                END;
              END;
            END;
          END;
        END;
      END IF;
    END;
  END LOOP;
  CLOSE CUR_EMPLOYEE;
END;