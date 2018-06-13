create or replace PROCEDURE HRIS_MIGRATE_ATTD_DATA
AS
  V_MAX_TIME       TIMESTAMP;
  V_ALREADY_EXISTS NUMBER;
BEGIN
  SELECT MAX(ATTENDANCE_TIME)
  INTO V_MAX_TIME
  FROM HRIS_ATTENDANCE
  WHERE ATTENDANCE_FROM='DATA_CARD';
  IF V_MAX_TIME       IS NULL THEN
    SELECT TO_TIMESTAMP ('01-01-17 14:10:10.123000', 'DD-MM-YY HH24:MI:SS.FF')
    INTO V_MAX_TIME
    FROM dual;
  END IF;
  FOR attd_data IN
  (SELECT        *
  FROM
    (SELECT dc.card_no AS thumb_id,
      d_card           AS attendance_dt,
      TO_TIMESTAMP (dc.D_CARD
      ||DC.T_CARD, 'DD-MON-YY HH24:MI:SS.FF') ATTENDANCE_TIME,
      ds.IP_ADDRESS
    FROM data_card dc
    JOIN device_setup ds
    ON dc.NODE_NO =ds.DEVICE_NO
    )
  WHERE ATTENDANCE_TIME > V_MAX_TIME ORDER BY ATTENDANCE_TIME
  )
  LOOP
    SELECT COUNT(*)
    INTO V_ALREADY_EXISTS
    FROM HRIS_ATTENDANCE
    WHERE THUMB_ID     =attd_data.thumb_id
    AND ATTENDANCE_DT  =attd_data.attendance_dt
    AND IP_ADDRESS     =attd_data.IP_ADDRESS
    AND ATTENDANCE_FROM='DATA_CARD'
    AND ATTENDANCE_TIME=attd_data.attendance_time;
    IF V_ALREADY_EXISTS=0 THEN
      HRIS_ATTENDANCE_INSERT( attd_data.thumb_id,attd_data.attendance_dt,attd_data.IP_ADDRESS,'DATA_CARD',attd_data.attendance_time,'from data_card');
    END IF;
  END LOOP attd_data;
END;