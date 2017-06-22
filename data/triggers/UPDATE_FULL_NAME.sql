create or replace TRIGGER UPDATE_FULL_NAME BEFORE
  UPDATE OR
  INSERT ON HRIS_EMPLOYEES FOR EACH ROW BEGIN IF (:old.FIRST_NAME !=:new.FIRST_NAME
  OR :old.MIDDLE_NAME                                             !=:new.MIDDLE_NAME
  OR :old.LAST_NAME                                               !=:new.LAST_NAME) THEN :new.FULL_NAME := CONCAT(CONCAT(CONCAT(TRIM(:new.FIRST_NAME),' '),
    CASE
      WHEN :new.MIDDLE_NAME IS NOT NULL
      THEN CONCAT(TRIM(:new.MIDDLE_NAME), ' ')
      ELSE ''
    END ),TRIM(:new.LAST_NAME));
END IF;
END;