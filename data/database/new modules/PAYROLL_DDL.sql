CREATE TABLE "HRIS_SALARY_SHEET_EMP_DETAIL"
  (
    "SHEET_NO"            NUMBER(7,0),
    "MONTH_ID"            NUMBER(7,0),
    "YEAR"                NUMBER,
    "MONTH_NO"            NUMBER,
    "START_DATE"          DATE,
    "END_DATE"            DATE,
    "TOTAL_DAYS"          NUMBER,
    "EMPLOYEE_ID"         NUMBER(7,0),
    "FULL_NAME"           VARCHAR2(255 BYTE),
    "DAYOFF"              NUMBER,
    "PRESENT"             NUMBER,
    "HOLIDAY"             NUMBER,
    "LEAVE"               NUMBER,
    "PAID_LEAVE"          NUMBER,
    "UNPAID_LEAVE"        NUMBER,
    "ABSENT"              NUMBER,
    "OVERTIME_HOUR"       NUMBER,
    "TRAVEL"              NUMBER,
    "TRAINING"            NUMBER,
    "WORK_ON_HOLIDAY"     NUMBER,
    "WORK_ON_DAYOFF"      NUMBER,
    "SALARY"              NUMBER(9,0),
    "MARITAL_STATUS"      CHAR(1 BYTE),
    "MARITAL_STATUS_DESC" VARCHAR2(9 BYTE),
    "GENDER_ID"           NUMBER(7,0),
    "GENDER_CODE"         CHAR(1 BYTE),
    "GENDER_NAME"         VARCHAR2(20 BYTE),
    "JOIN_DATE"           DATE,
    "COMPANY_ID"          NUMBER(7,0),
    "COMPANY_NAME"        VARCHAR2(255 BYTE),
    "BRANCH_ID"           NUMBER(7,0),
    "BRANCH_NAME"         VARCHAR2(255 BYTE),
    "DEPARTMENT_ID"       NUMBER(7,0),
    "DEPARTMENT_NAME"     VARCHAR2(255 BYTE),
    "DESIGNATION_ID"      NUMBER(7,0),
    "DESIGNATION_TITLE"   VARCHAR2(255 BYTE),
    "POSITION_ID"         NUMBER(7,0),
    "POSITION_NAME"       VARCHAR2(255 BYTE),
    "LEVEL_NO"            NUMBER(7,0),
    "SERVICE_TYPE_ID"     NUMBER(7,0),
    "SERVICE_TYPE_NAME"   VARCHAR2(255 BYTE),
    "SERVICE_TYPE"        VARCHAR2(64 BYTE)
  ) ;


CREATE TABLE HRIS_POSITION_FLAT_VALUE
  (
    FLAT_ID        NUMBER(7,0),
    POSITION_ID    NUMBER(7,0),
    FISCAL_YEAR_ID NUMBER(7,0),
    ASSIGNED_VALUE NUMBER
  );

ALTER TABLE HRIS_FLAT_VALUE_SETUP ADD ASSIGN_TYPE CHAR(1 BYTE) DEFAULT 'P' NOT NULL CHECK(ASSIGN_TYPE IN ('P','E'));

BEGIN
  HRIS_INSERT_MENU('Flat Val Assign(Position)','flatValue','position-wise',35,3,'fa fa-file-text-o','Y');
END;
/