BEGIN
  FOR cur_rec IN
  (SELECT object_name,
    object_type
  FROM user_objects
  WHERE object_type = 'TABLE' AND object_name IN('HRIS_COMPANY','HRIS_BRANCHES','HRIS_DEPARTMENTS','HRIS_DESIGNATIONS','HRIS_POSITIONS')
  )
  LOOP
    BEGIN
       EXECUTE IMMEDIATE 'ALTER ' || cur_rec.object_type || ' "' || cur_rec.object_name || '" MODIFY CREATED_BY NUMBER(7,0) NULL';
    EXCEPTION
    WHEN OTHERS THEN
      DBMS_OUTPUT.put_line ( 'FAILED: DROP ' || cur_rec.object_type || ' "' || cur_rec.object_name || '"' );
    END;
  END LOOP;
END;

-- SQL SCRIPT RUN ON HRIS