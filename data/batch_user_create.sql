DECLARE
  C        NUMBER :=20;
  USERNAME VARCHAR2(255 BYTE);
BEGIN
  FOR CUR_EMP IN
  (SELECT EMPLOYEE_ID,
    FIRST_NAME,
    MIDDLE_NAME,
    LAST_NAME
  FROM JWL_HRIS_APR5.HRIS_EMPLOYEES
  )
  LOOP
    BEGIN
      USERNAME := CONCAT(CONCAT(CONCAT(LOWER(CUR_EMP.FIRST_NAME),'_'),CONCAT(LOWER(CUR_EMP.MIDDLE_NAME), '_')),LOWER(CUR_EMP.LAST_NAME));
      INSERT
      INTO JWL_HRIS_APR5.HRIS_USERS
        (
          USER_ID,
          EMPLOYEE_ID,
          USER_NAME,
          PASSWORD,
          ROLE_ID,
          STATUS,
          CREATED_DT,
          MODIFIED_DT,
          CREATED_BY,
          MODIFIED_BY
        )
        VALUES
        (
          C,
          CUR_EMP.EMPLOYEE_ID,
          USERNAME,
          'password@123',
          3,
          'E',
          TRUNC(SYSDATE),
          NULL,
          7000195,
          NULL
        );
      C:=C+1;
    EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.put_line ( 'FAILED: DROP ' || CUR_EMP.EMPLOYEE_ID );
    END;
  END LOOP;
END;