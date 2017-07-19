ALTER TABLE HRIS_EMPLOYEE_TRAVEL_REQUEST ADD ADVANCE_AMOUNT FLOAT;

INSERT
INTO HRIS_MENUS
  (
    MENU_CODE,
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    MENU_DESCRIPTION,
    ROUTE,
    STATUS,
    CREATED_DT,
    MODIFIED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    CREATED_BY,
    MODIFIED_BY,
    IS_VISIBLE
  )
  VALUES
  (
  NULL,
    329,
    'Expence Detail',
    105,
    NULL,
    'travelStatus',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'expenseDetail',
    2,
    NULL,
    NULL,
    'N'
    );
    
    
    
    
    INSERT
INTO HRIS_MENUS
  (
    MENU_CODE,
    MENU_ID,
    MENU_NAME,
    PARENT_MENU,
    MENU_DESCRIPTION,
    ROUTE,
    STATUS,
    CREATED_DT,
    MODIFIED_DT,
    ICON_CLASS,
    ACTION,
    MENU_INDEX,
    CREATED_BY,
    MODIFIED_BY,
    IS_VISIBLE
  )
  VALUES
  (
  NULL,
    330,
    'Check Settlement',
    105,
    NULL,
    'travelStatus',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'checkSettlement',
    3,
    NULL,
    NULL,
    'Y'
  );

ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP 
ADD ASSIGN_ON_EMPLOYEE_SETUP CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (ASSIGN_ON_EMPLOYEE_SETUP IN ('Y','N'));


ALTER TABLE HRIS_TRAINING_MASTER_SETUP ADD IS_WITHIN_COMPANY CHAR( 1 BYTE) DEFAULT 'Y' NOT NULL CHECK (IS_WITHIN_COMPANY IN ('Y','N'));
