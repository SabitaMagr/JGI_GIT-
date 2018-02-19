ALTER TABLE HRIS_ATTENDANCE_DETAIL
DROP COLUMN LATE_STATUS;
-- 
ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD LATE_STATUS CHAR(1 BYTE) CHECK (LATE_STATUS IN ('L','E','B','N','X','Y'));

ALTER TABLE HRIS_ATTENDANCE ADD REMARKS VARCHAR(255 BYTE);

ALTER TABLE HRIS_APPRAISAL_STATUS
ADD (
REVIEW_PERIOD     VARCHAR2(255 BYTE),
PREVIOUS_REVIEW_PERIOD    VARCHAR2(255 BYTE),
PREVIOUS_RATING         VARCHAR2(255 BYTE)
);

ALTER TABLE HRIS_APPRAISAL_KPI MODIFY SUCCESS_CRITERIA VARCHAR2(4000 BYTE) NULL;

ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD ALLOW_GRACE_LEAVE CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(ALLOW_GRACE_LEAVE IN ('Y','N'));
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD IS_MONTHLY CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(IS_MONTHLY IN ('Y','N'));

ALTER TABLE HRIS_EMPLOYEE_LEAVE_REQUEST ADD GRACE_PERIOD CHAR(1 BYTE) DEFAULT NULL CHECK(GRACE_PERIOD IN ('E','L'));

ALTER TABLE HRIS_TASK
DROP CONSTRAINT FK_TASK_BRA_BRA_ID;
ALTER TABLE HRIS_TASK
DROP CONSTRAINT FK_TASK_COMP_COMP_ID;

ALTER TABLE HRIS_APPRAISAL_SETUP 
ADD HR_FEEDBACK_ENABLE CHAR(1 BYTE) CHECK (HR_FEEDBACK_ENABLE IN ('Y','N'));

ALTER TABLE HRIS_APPRAISAL_STATUS
ADD HR_FEEDBACK VARCHAR2(255 BYTE)

ALTER TABLE HRIS_APPRAISAL_ASSIGN
ADD SUPER_REVIEWER_ID NUMBER(7,0);

ALTER TABLE HRIS_APPRAISAL_ASSIGN ADD CONSTRAINT FK_APP_ASN_EMP_EMP_ID FOREIGN KEY(SUPER_REVIEWER_ID) REFERENCES
HRIS_EMPLOYEES(EMPLOYEE_ID);


ALTER TABLE HRIS_APPRAISAL_STATUS 
ADD SUPER_REVIEWER_AGREE CHAR(1 BYTE) CHECK (SUPER_REVIEWER_AGREE IN ('Y','N'));

ALTER TABLE HRIS_APPRAISAL_STATUS
ADD SUPER_REVIEWER_FEEDBACK VARCHAR2(255 BYTE)

ALTER TABLE HRIS_FISCAL_YEARS ADD FISCAL_YEAR_NAME VARCHAR2(10 BYTE);

DECLARE
  FISCAL_YEAR_ID NUMBER;
  START_DATE     DATE;
  END_DATE       DATE;
  CURSOR YEARS
  IS
    SELECT FISCAL_YEAR_ID,START_DATE,END_DATE FROM HRIS_FISCAL_YEARS;
BEGIN
  OPEN YEARS;
  LOOP
    FETCH YEARS INTO FISCAL_YEAR_ID,START_DATE,END_DATE;
    EXIT
  WHEN YEARS%NOTFOUND;
    UPDATE HRIS_FISCAL_YEARS
    SET FISCAL_YEAR_NAME = CONCAT(TO_CHAR(START_DATE,'YYYY')||'/',TO_CHAR(END_DATE,'YYYY'));
  END LOOP;
  CLOSE YEARS;
END;

ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD IS_SUBSTITUTE_MANDATORY CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (IS_SUBSTITUTE_MANDATORY IN ('Y','N'));


ALTER TABLE HRIS_EMPLOYEES
ADD IS_CEO CHAR (1 BYTE) CHECK (IS_CEO IN ('Y','N'));

ALTER TABLE HRIS_EMPLOYEES
ADD IS_HR CHAR (1 BYTE) CHECK (IS_HR IN ('Y','N'));

ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD ASSIGN_ON_EMPLOYEE_SETUP CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (ASSIGN_ON_EMPLOYEE_SETUP IN ('Y','N'));
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD IS_PRODATA_BASIS CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (IS_PRODATA_BASIS IN ('Y','N'));




-- to isnert new fiscal year

INSERT INTO HRIS_FISCAL_YEARS (FISCAL_YEAR_ID,START_DATE,END_DATE,CREATED_DT,STATUS,FISCAL_YEAR_NAME)
VALUES (3,TO_DATE('16-07-2017','DD-MM-YYYY'),TO_DATE('16-07-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E','2017/2018');

-- TO INSERT NEW MONTH CODE

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,25,'Shrawan','Shrawan',TO_DATE('16-07-2017','DD-MM-YYYY'),TO_DATE('16-08-2017','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,26,'Bhadra','Bhadra',TO_DATE('17-08-2017','DD-MM-YYYY'),TO_DATE('16-09-2017','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,27,'Ashwin','Ashwin',TO_DATE('17-09-2017','DD-MM-YYYY'),TO_DATE('17-10-2017','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,28,'Kartik','Kartik',TO_DATE('18-10-2017','DD-MM-YYYY'),TO_DATE('16-11-2017','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,29,'Mangsir','Mangsir',TO_DATE('17-11-2017','DD-MM-YYYY'),TO_DATE('15-12-2017','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,30,'Paush','Paush',TO_DATE('16-12-2017','DD-MM-YYYY'),TO_DATE('14-01-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,31,'Magh','Magh',TO_DATE('15-01-2018','DD-MM-YYYY'),TO_DATE('12-02-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,32,'Falgun','Falgun',TO_DATE('13-02-2018','DD-MM-YYYY'),TO_DATE('14-03-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,33,'Chaitra','Chaitra',TO_DATE('15-03-2018','DD-MM-YYYY'),TO_DATE('13-04-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,34,'Baishakh','Baishakh',TO_DATE('14-04-2018','DD-MM-YYYY'),TO_DATE('14-05-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,35,'Jestha','Jestha',TO_DATE('15-05-2018','DD-MM-YYYY'),TO_DATE('14-06-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');

INSERT INTO HRIS_MONTH_CODE (FISCAL_YEAR_ID,MONTH_ID,MONTH_EDESC,MONTH_NDESC,FROM_DATE,TO_DATE,CREATED_DT,STATUS)
VALUES (3,36,'Ashadh','Ashadh',TO_DATE('15-06-2018','DD-MM-YYYY'),TO_DATE('16-07-2018','DD-MM-YYYY'),TRUNC(SYSDATE),'E');


ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP 
ADD ASSIGN_ON_EMPLOYEE_SETUP CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (ASSIGN_ON_EMPLOYEE_SETUP IN ('Y','N'));


ALTER TABLE HRIS_TRAINING_MASTER_SETUP ADD IS_WITHIN_COMPANY CHAR( 1 BYTE) DEFAULT 'Y' NOT NULL CHECK (IS_WITHIN_COMPANY IN ('Y','N'));

ALTER TABLE HRIS_APPRAISAL_QUESTION
ADD (HR_FLAG CHAR(1 BYTE) DEFAULT 'N' CHECK (HR_FLAG IN ('Y','N')),
    HR_RATING CHAR(1 BYTE) DEFAULT 'N' CHECK (HR_RATING IN ('Y','N')));

ALTER TABLE HRIS_EMPLOYEES ADD ADDR_PERM_DISTRICT_ID NUMBER(7,0) ;
ALTER TABLE HRIS_EMPLOYEES ADD CONSTRAINT FK_EMP_DISTRICT_ID_1 FOREIGN KEY(ADDR_PERM_DISTRICT_ID) REFERENCES HRIS_DISTRICTS(DISTRICT_ID);
ALTER TABLE HRIS_EMPLOYEES ADD ADDR_PERM_ZONE_ID NUMBER(7,0);
ALTER TABLE HRIS_EMPLOYEES ADD CONSTRAINT FK_EMP_ZONE_ID FOREIGN KEY(ADDR_PERM_ZONE_ID) REFERENCES HRIS_ZONES(ZONE_ID);
ALTER TABLE HRIS_EMPLOYEES ADD ADDR_TEMP_DISTRICT_ID NUMBER(7,0);
ALTER TABLE HRIS_EMPLOYEES ADD CONSTRAINT FK_EMP_DISTRICT_ID_2 FOREIGN KEY (ADDR_TEMP_DISTRICT_ID) REFERENCES HRIS_DISTRICTS(DISTRICT_ID);
ALTER TABLE HRIS_EMPLOYEES ADD ADDR_TEMP_ZONE_ID NUMBER(7,0);
ALTER TABLE HRIS_EMPLOYEES ADD CONSTRAINT FK_EMP_ZONE_ID_2 FOREIGN KEY (ADDR_TEMP_ZONE_ID) REFERENCES HRIS_ZONES(ZONE_ID);

CREATE TABLE HRIS_PAY_EMPLOYEE_SETUP
  (
    PAY_ID      NUMBER(7,0) NOT NULL,
    EMPLOYEE_ID NUMBER(7,0) NOT NULL,
    CONSTRAINT FK_PAY_EMP_PAY_ID FOREIGN KEY(PAY_ID) REFERENCES HRIS_PAY_SETUP(PAY_ID),
    CONSTRAINT FK_PAY_EMP_EMP_ID FOREIGN KEY(EMPLOYEE_ID) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID)
  );




DROP TABLE HRIS_PAY_POSITION_SETUP;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL DROP COLUMN COMPANY_ID ;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL DROP COLUMN BRANCH_ID;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL DROP COLUMN STATUS;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL DROP COLUMN REMARKS;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL ADD FISCAL_YEAR_ID NUMBER(7,0) NOT NULL;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL ADD CONSTRAINT FK_MTH_VAL_DET_FIS_YR_ID  FOREIGN KEY(FISCAL_YEAR_ID) REFERENCES HRIS_FISCAL_YEARS(FISCAL_YEAR_ID);

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL ADD MONTH_ID NUMBER(7,0) NOT NULL;

ALTER TABLE HRIS_MONTHLY_VALUE_DETAIL ADD CONSTRAINT FK_MTH_VAL_DET_MON_ID FOREIGN KEY(MONTH_ID) REFERENCES HRIS_MONTH_CODE(MONTH_ID);


TRUNCATE TABLE HRIS_FLAT_VALUE_DETAIL;

ALTER TABLE HRIS_FLAT_VALUE_DETAIL
DROP COLUMN COMPANY_ID ;
ALTER TABLE HRIS_FLAT_VALUE_DETAIL
DROP COLUMN BRANCH_ID;
ALTER TABLE HRIS_FLAT_VALUE_DETAIL
DROP COLUMN STATUS;
ALTER TABLE HRIS_FLAT_VALUE_DETAIL
DROP COLUMN REMARKS;

ALTER TABLE HRIS_FLAT_VALUE_DETAIL ADD FISCAL_YEAR_ID NUMBER(7,0) NOT NULL;
ALTER TABLE HRIS_FLAT_VALUE_DETAIL ADD CONSTRAINT FK_FLAT_VAL_DET_FIS_YR_ID FOREIGN KEY(FISCAL_YEAR_ID) REFERENCES HRIS_FISCAL_YEARS(FISCAL_YEAR_ID);
ALTER TABLE HRIS_FLAT_VALUE_DETAIL ADD MONTH_ID NUMBER(7,0) NOT NULL;
ALTER TABLE HRIS_FLAT_VALUE_DETAIL ADD CONSTRAINT FK_FLAT_VAL_DET_MON_ID FOREIGN KEY(MONTH_ID) REFERENCES HRIS_MONTH_CODE(MONTH_ID);


ALTER TABLE HRIS_EMPLOYEES ADD PERMANENT_DATE DATE;

ALTER TABLE HRIS_FLAT_VALUE_DETAIL DROP COLUMN MONTH_ID;

ALTER TABLE HRIS_JOB_HISTORY ADD FROM_SALARY NUMBER(11,2);

ALTER TABLE HRIS_JOB_HISTORY ADD TO_SALARY NUMBER(11,2);


CREATE TABLE HRIS_BIRTHDAY_MESSAGES
   (	BIRTHDAY_ID NUMBER(7,0) PRIMARY KEY, 
	BIRTHDAY_DATE DATE NOT NULL, 
	FROM_EMPLOYEE NUMBER(7,0) NOT NULL, 
	TO_EMPLOYEE NUMBER(7,0) NOT NULL, 
	MESSAGE VARCHAR2(2000 BYTE) NOT NULL, 
	CREATED_DT DATE NOT NULL, 
	STATUS CHAR(1 BYTE) DEFAULT 'E' NOT NULL CHECK (STATUS IN ('E','D')), 
	MODIFIED_DT DATE
   ) ;
ALTER TABLE HRIS_BIRTHDAY_MESSAGES ADD CONSTRAINT FK_BIRTHDAY_MESS_EMP_ID_1 FOREIGN KEY (FROM_EMPLOYEE)
	  REFERENCES HRIS_EMPLOYEES (EMPLOYEE_ID);
ALTER TABLE HRIS_BIRTHDAY_MESSAGES ADD CONSTRAINT FK_BIRTHDAY_MESS_EMP_ID_2 FOREIGN KEY (TO_EMPLOYEE)
	  REFERENCES HRIS_EMPLOYEES (EMPLOYEE_ID);


ALTER TABLE HRIS_NEWS MODIFY (NEWS_LDESC VARCHAR2(3000));


ALTER TABLE HRIS_USERS
MODIFY PASSWORD VARCHAR2(64);

ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD HALFDAY_FLAG CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(HALFDAY_FLAG IN ('Y','N'));

ALTER TABLE HRIS_USERS
ADD IS_LOCKED CHAR(1 BYTE) DEFAULT ('N') NOT NULL CHECK(IS_LOCKED IN ('Y','N'));


ALTER TABLE HRIS_EMPLOYEE_TRAVEL_REQUEST MODIFY( REQUESTED_AMOUNT NULL); 
CREATE TABLE HRIS_PREFERENCES
  (
    KEY   VARCHAR2(100 BYTE) PRIMARY KEY,
    VALUE VARCHAR2(100 BYTE)
  );
--
SELECT *
FROM
  (SELECT * FROM HRIS_PREFERENCES
  ) PIVOT ( MAX(VALUE) FOR KEY IN (10,20,30,40) );

ALTER TABLE HRIS_EMPLOYEE_SHIFT_ASSIGN ADD START_DATE DATE DEFAULT TO_DATE('16-JUL-2017','DD-MON-YYYY') NOT NULL;
--
ALTER TABLE HRIS_EMPLOYEE_SHIFT_ASSIGN ADD END_DATE DATE ;
--
ALTER TABLE HRIS_EMPLOYEE_SHIFT_ASSIGN ADD ID NUMBER(7,0);

ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD ENABLE_SUBSTITUTE CHAR(1 BYTE) DEFAULT 'Y' NOT NULL CHECK(ENABLE_SUBSTITUTE IN ('Y','N'));
-- 

ALTER TABLE HRIS_EMPLOYEES
DROP CONSTRAINT EMP_EMAIL_OFF_UN;
--
ALTER TABLE HRIS_EMPLOYEES
DROP CONSTRAINT EMP_EMAIL_PER_UN;


ALTER TABLE HRIS_JOB_HISTORY ADD RETIRED_FLAG CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(RETIRED_FLAG IN ('Y','N'));
--
ALTER TABLE HRIS_JOB_HISTORY ADD DISABLED_FLAG CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(DISABLED_FLAG IN ('Y','N'));
-- 
ALTER TABLE HRIS_JOB_HISTORY ADD EVENT_DATE DATE DEFAULT SYSDATE NOT NULL;

ALTER TABLE HRIS_ROLES ADD CONTROL CHAR(1 BYTE) DEFAULT 'F' NOT NULL CHECK(CONTROL IN ('F','C','U'));

ALTER TABLE HRIS_ROLES ADD ALLOW_ADD CHAR(1 BYTE) DEFAULT 'Y' NOT NULL CHECK(ALLOW_ADD IN ('Y','N'));

ALTER TABLE HRIS_ROLES ADD ALLOW_UPDATE CHAR(1 BYTE) DEFAULT 'Y' NOT NULL CHECK(ALLOW_UPDATE IN ('Y','N'));

ALTER TABLE HRIS_ROLES ADD ALLOW_DELETE CHAR(1 BYTE) DEFAULT 'Y' NOT NULL CHECK(ALLOW_DELETE IN ('Y','N'));

  CREATE TABLE "HRIS_EMPLOYEE_PENALTY_DAYS" 
   (	"EMPLOYEE_ID" NUMBER(7,0), 
	"ATTENDANCE_DT" DATE
   )

CREATE TABLE HRIS_EMPLOYEE_SHIFT_ROASTER
  (
    EMPLOYEE_ID NUMBER(7,0),
    SHIFT_ID    NUMBER(7,0),
    FOR_DATE    DATE NOT NULL,
    CREATED_BY  NUMBER(7,0),
    MODIFIED_BY NUMBER(7,0),
    CREATED_DT  NUMBER(7,0),
    MODIFED_DT  NUMBER(7,0),
    CONSTRAINT FK_ROASTER_EMPLOYEE_EMP_ID FOREIGN KEY(EMPLOYEE_ID) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID),
    CONSTRAINT FK_ROASTER_SHIFT_SHIFT_ID FOREIGN KEY(SHIFT_ID) REFERENCES HRIS_SHIFTS(SHIFT_ID)
  );


-- HRIS_PRELOAD_ATTENANCE PROCEDURE IS CHANGED
ALTER TABLE HRIS_USERS ADD IS_LOCKED CHAR(1 BYTE) DEFAULT ('N') NOT NULL;

ALTER TABLE HRIS_ATTENDANCE_REQUEST
MODIFY IN_TIME NULL;

ALTER TABLE HRIS_ATTENDANCE_REQUEST
MODIFY OUT_TIME NULL;


-- HRIS_BACKDATE_ATTENDANCE PROCEDURE IS CHANGED




-- HRIS_PRELOAD_ATTENDANCE PROCEDURE IS CHANGED

CREATE TABLE HRIS_EMPLOYEE_SHIFTS
  (
    EMPLOYEE_ID NUMBER(7,0),
    SHIFT_ID    NUMBER(7,0),
    START_DATE  DATE,
    END_DATE    DATE
  );

-- HRIS_PRELOAD_ATTENDANCE PROCEDURE IS CHANGED
-- HRIS_REATTENDANCE PROCEDURE IS CHANGED
-- DEVICE_ATTENDANCE_TRIGGER IS CHANGED


CREATE TABLE HRIS_EMPLOYEE_LEAVE_ADDITION
  (
    EMPLOYEE_ID NUMBER(7,0),
    LEAVE_ID    NUMBER(7,0),
    NO_OF_DAYS FLOAT,
    REMARKS      VARCHAR2(512 BYTE),
    CREATED_DATE DATE,
    WOD_ID NUMBER(7,0),
    WOH_ID NUMBER(7,0)
  );

-- HRIS_AFTER_LEAVE_ADDITION TRIGGER IS ADDED


DROP TABLE HRIS_EMPLOYEE_PENALTY_DAYS;
--
CREATE TABLE HRIS_EMPLOYEE_PENALTY_DAYS
  (
    EMPLOYEE_ID   NUMBER(7,0),
    ATTENDANCE_DT DATE,
    LEAVE_ID      NUMBER(7,0),
    NO_OF_DAYS FLOAT,
    REMARKS      VARCHAR2(512 BYTE),
    CREATED_DATE DATE
  );

ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD GRACE_PERIOD   CHAR(1 BYTE) DEFAULT NULL CHECK(GRACE_PERIOD   IN ('E','L'));
ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD HALFDAY_PERIOD CHAR(1 BYTE) DEFAULT NULL CHECK(HALFDAY_PERIOD IN ('F','S'));

-- HRIS_PRELOAD_ATTENDANCE CHANGED
-- HRIS_POST_ATTENDANCE_CHECK
-- HRIS_REATTENDANCE
-- DEVICE _ATTENDANCE_TRIGGER


ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD DEDUCTION_PRIORITY_NO NUMBER(7,0);


-- HRIS_AFTER_EMPLOYEE_PENALTY
-- HRIS_AFTER_LEAVE_ADDITION


ALTER TABLE HRIS_MONTH_CODE ADD YEAR NUMBER DEFAULT 2073 NOT NULL ;
ALTER TABLE HRIS_MONTH_CODE ADD MONTH_NO NUMBER DEFAULT 1 NOT NULL;


CREATE TABLE HRIS_PENALIZED_MONTHS
  (
    YEAR         NUMBER NOT NULL,
    MONTH_NO     NUMBER NOT NULL,
    NO_OF_DAYS   NUMBER NOT NULL,
    CREATED_DATE DATE,
    CREATED_BY NUMBER(7,0),
    MODIFIED_DATE DATE,
    MODIFIED_BY NUMBER(7,0)
  );

-- HRIS_LATE_LEAVE_DEDUCTION


UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=1 WHERE MONTH_ID = 22;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=2 WHERE MONTH_ID = 23;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=3 WHERE MONTH_ID = 24;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=4 WHERE MONTH_ID = 25;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=5 WHERE MONTH_ID = 26;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=6 WHERE MONTH_ID = 27;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=7 WHERE MONTH_ID = 28;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=8 WHERE MONTH_ID = 29;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=9 WHERE MONTH_ID = 30;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=10 WHERE MONTH_ID = 31;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=11 WHERE MONTH_ID = 32;
UPDATE HRIS_MONTH_CODE SET YEAR = 2074,MONTH_NO=12 WHERE MONTH_ID = 33;
UPDATE HRIS_MONTH_CODE SET YEAR = 2075,MONTH_NO=1 WHERE MONTH_ID = 34;
UPDATE HRIS_MONTH_CODE SET YEAR = 2075,MONTH_NO=2 WHERE MONTH_ID = 35;
UPDATE HRIS_MONTH_CODE SET YEAR = 2075,MONTH_NO=3 WHERE MONTH_ID = 36;

-- DEVICE ATTENDANCE TRIGGER

-- HRIS_REATTENDANCE
-- HRIS_WOD_LEAVE_ADDITION
-- HRIS_WOD_OT_ADDITION
-- HRIS_WOH_LEAVE_ADDITION
-- HRIS_WOH_OT_ADDITION


ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD WOD_ID NUMBER(7,0);
ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD WOH_ID NUMBER(7,0);

-- HRIS_PRELOAD_ATTENDANCE
-- HRIS_REATTENDANCE
-- DEVICE_ATTENDANCE_TRIGGER

ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD TWO_DAY_SHIFT CHAR(1 BYTE) DEFAULT 'D' NOT NULL CHECK(TWO_DAY_SHIFT IN ('E','D'));
-- HRIS_PRELOAD_ATTENDANCE
-- DEVICE_ATTENDANCE_TRIGGER
-- HRIS_REATTENDANCE
-- HRIS_REATTENDANCE_TWO_DAY


-- HRIS_RECALCULATE_LEAVE
-- HRIS_WOD_LEAVE_ADDITION
-- HRIS_WOD_OT_ADDITION
-- HRIS_WOH_LEAVE_ADDITION
-- HRIS_WOH_OT_ADDITION
-- HRIS_MENU_ROLE_ASSIGN ADDED

CREATE TABLE HRIS_POSITION_MONTHLY_VALUE
  (
    MTH_ID         NUMBER(7,0),
    POSITION_ID    NUMBER(7,0),
    MONTH_ID       NUMBER(7,0),
    ASSIGNED_VALUE NUMBER
  );
--
ALTER TABLE HRIS_MONTHLY_VALUE_SETUP
DROP COLUMN COMPANY_ID;
--
ALTER TABLE HRIS_MONTHLY_VALUE_SETUP
DROP COLUMN BRANCH_ID;
--
ALTER TABLE HRIS_MONTHLY_VALUE_SETUP
DROP COLUMN SHOW_AT_RULE;
--
ALTER TABLE HRIS_MONTHLY_VALUE_SETUP
DROP COLUMN SH_INDEX_NO;
--
ALTER TABLE HRIS_MONTHLY_VALUE_SETUP
ADD CREATED_BY NUMBER(7,0);
--
ALTER TABLE HRIS_MONTHLY_VALUE_SETUP
ADD MODIFIED_BY NUMBER(7,0);
--
ALTER TABLE HRIS_EMPLOYEE_LEAVE_REQUEST MODIFY( REMARKS VARCHAR2(255 ));

CREATE TABLE HRIS_HOLIDAY_ASSIGN
  (
    HOLIDAY_ID            NUMBER(7,0) NOT NULL,
    COMPANY_ID            NUMBER(7,0),
    BRANCH_ID             NUMBER(7,0),
    DEPARTMENT_ID         NUMBER(7,0),
    DESIGNATION_ID        NUMBER(7,0),
    POSITION_ID           NUMBER(7,0),
    SERVICE_TYPE_ID       NUMBER(7,0),
    SERVICE_EVENT_TYPE_ID NUMBER(7,0),
    EMPLOYEE_TYPE         CHAR(1 BYTE),
    GENDER_ID             NUMBER(7,0),
    EMPLOYEE_ID           NUMBER(7,0)
  );

-- HRIS_HOLIDAY_ASSIGN_AUTO PROCEDURE CREATED

ALTER TABLE HRIS_SALARY_SHEET ADD YEAR      NUMBER;
ALTER TABLE HRIS_SALARY_SHEET ADD MONTH_NO  NUMBER;
ALTER TABLE HRIS_SALARY_SHEET ADD START_DATE DATE;
ALTER TABLE HRIS_SALARY_SHEET ADD END_DATE   DATE;
ALTER TABLE HRIS_SALARY_SHEET_DETAIL DROP COLUMN MONTH_ID;
ALTER TABLE HRIS_SALARY_SHEET_DETAIL DROP COLUMN TOTAL_VAL;




ALTER TABLE HRIS_PAY_SETUP ADD IS_MONTHLY CHAR(1 BYTE) NOT NULL CHECK(IS_MONTHLY IN ('Y','N'));

ALTER TABLE HRIS_PAY_SETUP ADD FORMULA LONG NOT NULL;

DROP TABLE HRIS_PAY_DETAIL_SETUP;

DROP TABLE HRIS_PAY_EMPLOYEE_SETUP;

ALTER TABLE HRIS_PAY_SETUP DROP COLUMN REF_PAY_ID;

ALTER TABLE HRIS_PAY_SETUP DROP COLUMN REF_RULE_FLAG;

ALTER TABLE HRIS_PAY_SETUP DROP COLUMN COMPANY_ID;

ALTER TABLE HRIS_PAY_SETUP DROP COLUMN BRANCH_ID;


ALTER TABLE HRIS_FLAT_VALUE_SETUP DROP COLUMN SHOW_AT_RULE;
ALTER TABLE HRIS_FLAT_VALUE_SETUP DROP COLUMN FLAT_FORMULA;

ALTER TABLE HRIS_FLAT_VALUE_SETUP DROP COLUMN COMPANY_ID;

ALTER TABLE HRIS_FLAT_VALUE_SETUP DROP COLUMN BRANCH_ID;

ALTER TABLE HRIS_MONTHLY_VALUE_SETUP ADD REMARKS VARCHAR2(255 BYTE);

ALTER TABLE HRIS_FLAT_VALUE_SETUP MODIFY(FLAT_LDESC NULL);
ALTER TABLE HRIS_PAY_SETUP MODIFY(PAY_LDESC NULL);


CREATE TABLE HRIS_NEWS_TYPE
  (
    NEWS_TYPE_ID   NUMBER(7,0) PRIMARY KEY,
    NEWS_TYPE_DESC VARCHAR2(255) NOT NULL,
    UPLOAD_FLAG    CHAR(1 BYTE) DEFAULT 'Y' CHECK (UPLOAD_FLAG IN ('Y','N')),
    STATUS         CHAR(1 BYTE) DEFAULT 'E' CHECK(STATUS       IN ('E','D')),
    CREATED_DT     DATE DEFAULT TRUNC(SYSDATE) NOT NULL,
    CREATED_BY     NUMBER(7,0),
    MODIFIED_DT    DATE,
    MODIFIED_BY    NUMBER(7,0)
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
    'nto1',
    (SELECT MAX(menu_id)+1 FROM hris_menus
    ),
    'News Type',
    9,
    'News Type',
    'news-type',
    'E',
    TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'index',
    15,NULL,
    NULL,
    'Y'
  );
-- drop TWO constaInt in hris news for news_type ONLY

ALTER TABLE HRIS_NEWS DROP COLUMN NEWS_TYPE;
ALTER TABLE HRIS_NEWS ADD NEWS_TYPE NUMBER(7,0);


CREATE TABLE HRIS_NEWS_FILE
  (
    NEWS_FILE_ID NUMBER(7,0) PRIMARY KEY,
    NEWS_ID      NUMBER(7,0) NOT NULL,
    FILE_PATH    VARCHAR2(255),
    STATUS       CHAR(1 BYTE) DEFAULT 'E' CHECK(STATUS IN ('E','D')),
    CREATED_DT   DATE DEFAULT TRUNC(SYSDATE) NOT NULL,
    CREATED_BY   NUMBER(7,0),
    MODIFIED_DT  DATE,
    MODIFIED_BY  NUMBER(7,0),
    REMARKS      VARCHAR2(255),
    FILE_NAME    VARCHAR2(255)
  );
ALTER TABLE HRIS_NEWS ADD NEWS_EXPIRY_DT DATE DEFAULT TRUNC(SYSDATE) NOT NULL;

CREATE TABLE HRIS_NEWS_TO
  (
    NEWS_ID               NUMBER(7,0),
    COMPANY_ID            NUMBER(7,0),
    BRANCH_ID             NUMBER(7,0),
    DEPARTMENT_ID         NUMBER(7,0),
    DESIGNATION_ID        NUMBER(7,0),
    POSITION_ID           NUMBER(7,0),
    SERVICE_TYPE_ID       NUMBER(7,0),
    SERVICE_EVENT_TYPE_ID NUMBER(7,0),
    EMPLOYEE_TYPE         CHAR(1 BYTE),
    GENDER_ID             NUMBER(7,0),
    EMPLOYEE_ID           NUMBER(7,0)
  );

ALTER TABLE HRIS_NEWS DROP COLUMN COMPANY_ID;

ALTER TABLE HRIS_NEWS DROP COLUMN BRANCH_ID;
ALTER TABLE HRIS_NEWS DROP COLUMN DEPARTMENT_ID;
ALTER TABLE HRIS_NEWS DROP COLUMN DESIGNATION_ID;

CREATE TABLE HRIS_NEWS_EMPLOYEE
  (NEWS_ID NUMBER(7,0),EMPLOYEE_ID NUMBER(7,0)
  );



ALTER TABLE HRIS_MONTH_CODE ADD FISCAL_YEAR_MONTH_NO NUMBER(2,0);

ALTER TABLE HRIS_COMPULSORY_OVERTIME ADD COMPULSORY_OT_DESC VARCHAR2(255 BYTE);

ALTER TABLE HRIS_SHIFTS ADD HALF_DAY_IN_TIME TIMESTAMP;

ALTER TABLE HRIS_SHIFTS ADD HALF_DAY_OUT_TIME TIMESTAMP;

ALTER TABLE HRIS_LEAVE_MASTER_SETUP DROP COLUMN COMPANY_ID ;
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD COMPANY_ID VARCHAR2(4000 BYTE);

ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD BRANCH_ID       VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD DEPARTMENT_ID   VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD DESIGNATION_ID  VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD POSITION_ID     VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD SERVICE_TYPE_ID VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD EMPLOYEE_TYPE   VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD GENDER_ID       VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD EMPLOYEE_ID     VARCHAR2(4000 BYTE);



insert into hris_menus
(
menu_id,
MENU_NAME,
PARENT_MENU,
route,
status,
created_dt,
icon_class,
action,
menu_index,
is_visible
)values
(
(select max(menu_id)+1 from hris_menus),
'Branch Wise Daily',
148,
'allreport',
'E',
TRUNC(SYSDATE),
'fa fa-pencil',
'branchWiseDaily',
(select max(MENU_INDEX)+1 from hris_menus where parent_menu=148),
'Y'
);


ALTER TABLE HRIS_COMPANY ADD LINK_TRAVEL_TO_SYNERGY CHAR(1 BYTE) CHECK(LINK_TRAVEL_TO_SYNERGY IN ('Y','N'));
ALTER TABLE HRIS_COMPANY ADD DR_ACC_CODE            VARCHAR2(255 BYTE);
ALTER TABLE HRIS_COMPANY ADD CR_ACC_CODE            VARCHAR2(255 BYTE);
ALTER TABLE HRIS_COMPANY ADD EXCESS_CR_ACC_CODE     VARCHAR2(255 BYTE);
ALTER TABLE HRIS_COMPANY ADD LESS_DR_ACC_CODE       VARCHAR2(255 BYTE);
ALTER TABLE HRIS_COMPANY ADD FORM_CODE              VARCHAR2(255 BYTE);
ALTER TABLE HRIS_COMPANY ADD EQUAL_CR_ACC_CODE VARCHAR2(255 BYTE);

ALTER TABLE HRIS_ROLES MODIFY (CONTROL VARCHAR2(2 BYTE));


CREATE TABLE HRIS_JOBS
  (
    JOB_ID      NUMBER(7,0) PRIMARY KEY,
    WHAT        VARCHAR2(4000 BYTE),
    EXECUTED    CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(EXECUTED IN ('Y','N')),
    EXECUTED_AT DATE,
    STATUS      CHAR(1 BYTE) CHECK(STATUS IN ('S','F')),
    CAUSE       VARCHAR2(4000 BYTE)
  );

ALTER TABLE HRIS_EMPLOYEE_TRAINING_REQUEST ADD IS_WITHIN_COMPANY CHAR(1 BYTE) DEFAULT 'N' NOT NULL CHECK(IS_WITHIN_COMPANY IN ('Y','N'));

DELETE
FROM HRIS_ROLE_PERMISSIONS
WHERE MENU_ID IN
  (SELECT MENU_ID
  FROM HRIS_MENUS
  WHERE LOWER(ROUTE) LIKE LOWER('%trainingapply%')
  );
DELETE
FROM HRIS_MENUS
WHERE MENU_ID IN
  (SELECT MENU_ID
  FROM HRIS_MENUS
  WHERE LOWER(ROUTE) LIKE LOWER('%trainingapply%')
  );

ALTER TABLE HRIS_ATTENDANCE_DETAIL ADD TRAINING_TYPE CHAR(1 BYTE) CHECK(TRAINING_TYPE IN ('R','A'));

CREATE TABLE HRIS_LOCATIONS
  (
    LOCATION_ID        NUMBER(7,0) PRIMARY KEY,
    LOCATION_CODE      VARCHAR2(15 BYTE),
    LOCATION_EDESC     VARCHAR2(255 BYTE) NOT NULL,
    LOCATION_LDESC     VARCHAR2(255 BYTE),
    PARENT_LOCATION_ID NUMBER(7,0),
    CREATED_DT         DATE,
    CREATED_BY         NUMBER(7,0),
    MODIFIED_DT        DATE,
    MODIFIED_BY        NUMBER(7,0),
    DELETED_DT         DATE,
    DELETED_BY         NUMBER(7,0),
    STATUS             CHAR(1 BYTE) DEFAULT 'E' NOT NULL CHECK(STATUS IN ('E','D'))
  );
CREATE TABLE HRIS_FUNCTIONAL_TYPES
  (
    FUNCTIONAL_TYPE_ID    NUMBER(7,0) PRIMARY KEY,
    FUNCTIONAL_TYPE_CODE  VARCHAR2(15 BYTE) ,
    FUNCTIONAL_TYPE_EDESC VARCHAR2(255 BYTE) NOT NULL,
    FUNCTIONAL_TYPE_LDESC VARCHAR2(255 BYTE),
    CREATED_DT            DATE,
    CREATED_BY            NUMBER(7,0),
    MODIFIED_DT           DATE,
    MODIFIED_BY           NUMBER(7,0),
    DELETED_DT            DATE,
    DELETED_BY            NUMBER(7,0),
    STATUS                CHAR(1 BYTE) DEFAULT 'E' NOT NULL CHECK(STATUS IN ('E','D'))
  );
CREATE TABLE HRIS_FUNCTIONAL_LEVELS
  (
    FUNCTIONAL_LEVEL_ID    NUMBER(7,0) PRIMARY KEY,
    FUNCTIONAL_LEVEL_NO    VARCHAR2(15 BYTE) NOT NULL,
    FUNCTIONAL_LEVEL_EDESC VARCHAR2(255 BYTE) NOT NULL,
    FUNCTIONAL_LEVEL_LDESC VARCHAR2(255 BYTE),
    FUNCTIONAL_TYPE_ID     NUMBER(7,0),
    CREATED_DT             DATE,
    CREATED_BY             NUMBER(7,0),
    MODIFIED_DT            DATE,
    MODIFIED_BY            NUMBER(7,0),
    DELETED_DT             DATE,
    DELETED_BY             NUMBER(7,0),
    STATUS                 CHAR(1 BYTE) DEFAULT 'E' NOT NULL CHECK(STATUS IN ('E','D'))
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    ),
    'Functional Level',
    1,
    NULL,
    'functionalLevels',
    'E',
    to_date('22-JAN-18','DD-MON-RR'),
    NULL,
    'fa fa-square-o',
    'index',
    100,
    NULL,
    NULL,
    'Y'
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    ),
    'Functional Type',
    1,
    NULL,
    'functionalTypes',
    'E',
    to_date('22-JAN-18','DD-MON-RR'),
    to_date('22-JAN-18','DD-MON-RR'),
    'fa fa-square-o',
    'index',
    99,
    NULL,
    NULL,
    'Y'
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
    (SELECT MAX(MENU_ID)+1 FROM HRIS_MENUS
    ),
    'Location',
    1,
    NULL,
    'location',
    'E',
    to_date('22-JAN-18','DD-MON-RR'),
    NULL,
    'fa fa-square-o',
    'index',
    89,
    NULL,
    NULL,
    'Y'
  );



ALTER TABLE HRIS_EMPLOYEES ADD LOCATION_ID         NUMBER(7,0);
ALTER TABLE HRIS_EMPLOYEES ADD FUNCTIONAL_TYPE_ID  NUMBER(7,0);
ALTER TABLE HRIS_EMPLOYEES ADD FUNCTIONAL_LEVEL_ID NUMBER(7,0);

ALTER TABLE HRIS_EMPLOYEES ADD RESIGNED_FLAG CHAR(1 BYTE) DEFAULT 'N' CHECK(RESIGNED_FLAG IN ('Y','N'));
ALTER TABLE HRIS_EMPLOYEES ADD RESIGNED_DATE DATE;
--
ALTER TABLE HRIS_EMPLOYEES ADD PERMANENT_FLAG CHAR(1 BYTE) DEFAULT 'N' CHECK(PERMANENT_FLAG IN ('Y','N'));

CREATE TABLE HRIS_FILES
  (
    FILE_ID          NUMBER(7,0),
    FILE_NAME        VARCHAR2(255 BYTE),
    FILE_IN_DIR_NAME VARCHAR2(4000 BYTE),
    UPLOADED_DATE    DATE,
    UPLOADED_BY      NUMBER(7,0)
  );
ALTER TABLE HRIS_JOB_HISTORY ADD FILE_ID NUMBER(7,0);

ALTER TABLE HRIS_EMPLOYEE_LEAVE_ASSIGN ADD FISCAL_YEAR_MONTH_NO NUMBER(2,0);

ALTER TABLE HRIS_ATTENDANCE ADD THUMB_ID NUMBER;

CREATE TABLE HRIS_ATTENDANCE_DATA_LOG
  (
    THUMB_ID     NUMBER,
    STATUS       VARCHAR2(255 BYTE),
    CREATED_DATE DATE
  );
  
DROP TABLE HRIS_ATTENDANCE_COPY;
CREATE TABLE HRIS_ATTENDANCE_COPY AS
SELECT * FROM HRIS_ATTENDANCE;
TRUNCATE TABLE HRIS_ATTENDANCE;
ALTER TABLE HRIS_ATTENDANCE MODIFY(EMPLOYEE_ID NULL);
INSERT INTO HRIS_ATTENDANCE
SELECT * FROM HRIS_ATTENDANCE_COPY;
DROP TABLE HRIS_ATTENDANCE_COPY;




DROP TABLE HRIS_EMP_THUMB_COPY;
CREATE TABLE HRIS_EMP_THUMB_COPY AS
SELECT EMPLOYEE_ID, ID_THUMB_ID FROM HRIS_EMPLOYEES;
UPDATE HRIS_EMPLOYEES
SET ID_THUMB_ID =NULL;
ALTER TABLE HRIS_EMPLOYEES MODIFY(ID_THUMB_ID NUMBER);
BEGIN
  FOR employee IN
  (SELECT * FROM HRIS_EMP_THUMB_COPY
  )
  LOOP
    UPDATE HRIS_EMPLOYEES
    SET ID_THUMB_ID  = employee.ID_THUMB_ID
    WHERE EMPLOYEE_ID= employee.EMPLOYEE_ID;
  END LOOP;
END;
/
DROP TABLE HRIS_EMP_THUMB_COPY;


ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP DROP COLUMN COMPANY_ID ;
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD COMPANY_ID VARCHAR2(4000 BYTE);

ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD BRANCH_ID       VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD DEPARTMENT_ID   VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD DESIGNATION_ID  VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD POSITION_ID     VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD SERVICE_TYPE_ID VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD EMPLOYEE_TYPE   VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD GENDER_ID       VARCHAR2(4000 BYTE);
ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP ADD EMPLOYEE_ID     VARCHAR2(4000 BYTE);


ALTER TABLE HRIS_COMPANY ADD ADVANCE_DR_ACC_CODE            VARCHAR2(255 BYTE);
ALTER TABLE HRIS_COMPANY ADD ADVANCE_CR_ACC_CODE            VARCHAR2(255 BYTE);

ALTER TABLE HRIS_EMPLOYEES
MODIFY ID_DRIVING_LICENCE_TYPE VARCHAR(30);


DROP TABLE HRIS_PENALIZED_MONTHS;
CREATE TABLE "HRIS_PENALIZED_MONTHS"
  (
    "NO_OF_DAYS"           NUMBER NOT NULL,
    "CREATED_DATE"         DATE,
    "CREATED_BY"           NUMBER(7,0),
    "MODIFIED_DATE"        DATE,
    "MODIFIED_BY"          NUMBER(7,0),
    "COMPANY_ID"           NUMBER(7,0),
    "FISCAL_YEAR_ID"       NUMBER(7,0),
    "FISCAL_YEAR_MONTH_NO" NUMBER(2,0)
  );
INSERT INTO HRIS_BLOOD_GROUPS VALUES (9,'N/A',NULL,'E');

insert into hris_menus
(
menu_id,
MENU_NAME,
PARENT_MENU,
route,
status,
created_dt,
icon_class,
action,
menu_index,
is_visible
)values
(
(select max(menu_id)+1 from hris_menus),
'Report with Location',
4,
'attendancebyhr',
'E',
TRUNC(SYSDATE),
'fa fa-pencil',
'attendnaceReportWithLocation',
(select max(MENU_INDEX)+1 from hris_menus where parent_menu=4),
'Y'
);
