CREATE OR REPLACE TRIGGER DEVICE_ATTENDANCE_TRIGGER AFTER
  INSERT ON HRIS_ATTENDANCE FOR EACH ROW DECLARE temp_in_time TIMESTAMP (6) := NULL;
  start_time HRIS_SHIFTS.START_TIME%TYPE;
  end_time HRIS_SHIFTS.END_TIME%TYPE;
  grace_start_time HRIS_SHIFTS.GRACE_START_TIME%TYPE;
  grace_end_time HRIS_SHIFTS.GRACE_END_TIME%TYPE;
  late_in HRIS_SHIFTS.LATE_IN%TYPE;
  early_out HRIS_SHIFTS.EARLY_OUT%TYPE;
  BEGIN
    BEGIN
      SELECT IN_TIME
      INTO temp_in_time
      FROM HRIS_ATTENDANCE_DETAIL
      WHERE ATTENDANCE_DT = TO_DATE (:new.ATTENDANCE_DT, 'DD-MON-YY')
      AND EMPLOYEE_ID     = :new.EMPLOYEE_ID;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      DBMS_OUTPUT.PUT_LINE ('Attendance Job for '||:new.ATTENDANCE_DT||' not excecuted');
    END;
    IF (temp_in_time IS NULL) THEN
      UPDATE HRIS_ATTENDANCE_DETAIL
      SET IN_TIME         = TO_DATE ( TO_CHAR (:new.ATTENDANCE_TIME, 'DD-MON-YY HH:MI AM'), 'DD-MON-YY HH:MI AM')
      WHERE ATTENDANCE_DT = TO_DATE (:new.ATTENDANCE_DT, 'DD-MON-YY')
      AND EMPLOYEE_ID     = :new.EMPLOYEE_ID;
    ELSE
      UPDATE HRIS_ATTENDANCE_DETAIL
      SET OUT_TIME        = TO_DATE ( TO_CHAR (:new.ATTENDANCE_TIME, 'DD-MON-YY HH:MI AM'), 'DD-MON-YY HH:MI AM')
      WHERE ATTENDANCE_DT = TO_DATE (:new.ATTENDANCE_DT, 'DD-MON-YY')
      AND EMPLOYEE_ID     = :new.EMPLOYEE_ID;
    END IF;
    BEGIN
      SELECT START_TIME,
        END_TIME,
        GRACE_START_TIME,
        GRACE_END_TIME,
        LATE_IN,
        EARLY_OUT
      INTO start_time,
        end_time,
        grace_start_time,
        grace_end_time,
        late_in,
        early_out
      FROM HRIS_SHIFTS S
      JOIN HRIS_EMPLOYEE_SHIFT_ASSIGN SA
      ON (SA.SHIFT_ID     = S.SHIFT_ID)
      WHERE SA.EMPLOYEE_ID=:new.EMPLOYEE_ID
      AND (:new.ATTENDANCE_DT BETWEEN S.START_DATE AND S.END_DATE)
      AND S.CURRENT_SHIFT = 'Y'
      AND S.STATUS        = 'E'
      AND SA.STATUS       ='E'
      AND ROWNUM          <2;
    EXCEPTION
    WHEN NO_DATA_FOUND THEN
      BEGIN
        SELECT START_TIME,
          END_TIME,
          GRACE_START_TIME,
          GRACE_END_TIME,
          LATE_IN,
          EARLY_OUT
        INTO start_time,
          end_time,
          grace_start_time,
          grace_end_time,
          late_in,
          early_out
        FROM HRIS_SHIFTS S
        WHERE S.CURRENT_SHIFT='Y'
        AND S.DEFAULT_SHIFT  ='Y'
        AND S.STATUS         ='E'
        AND :new.ATTENDANCE_DT BETWEEN S.START_DATE AND S.END_DATE;
      EXCEPTION
      WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20344, 'No default and normal shift defined for this time period');
      END;
    END;
    IF start_time IS NOT NULL THEN
      NULL;
    END IF;
  END;