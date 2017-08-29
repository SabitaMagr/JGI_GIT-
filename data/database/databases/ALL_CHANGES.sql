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
    323,
    'Appraisal Final Review',
    5,
    NULL,
    'appraisal-final-review',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'index',
    18,
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
    324,
    'view',
    323,
    NULL,
    'appraisal-final-review',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'view',
    1,
    NULL,
    NULL,
    'N'
    );

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

CREATE TABLE HRIS_TRVL_RECOMMENDER_APPROVER AS (SELECT * FROM HRIS_RECOMMENDER_APPROVER);

ALTER TABLE HRIS_TRVL_RECOMMENDER_APPROVER ADD CONSTRAINT FK_RECM_APRV_TRVL_EMP_EMP_ID FOREIGN KEY(EMPLOYEE_ID) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID);
ALTER TABLE HRIS_TRVL_RECOMMENDER_APPROVER ADD CONSTRAINT FK_RECM_APRV_TRVL_EMP_EMP_ID1 FOREIGN KEY(CREATED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID);
ALTER TABLE HRIS_TRVL_RECOMMENDER_APPROVER ADD CONSTRAINT FK_RECM_APRV_TRVL_EMP_EMP_ID2 FOREIGN KEY(MODIFIED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID);
ALTER TABLE HRIS_TRVL_RECOMMENDER_APPROVER ADD CONSTRAINT FK_RECM_APRV_TRVL_EMP_EMP_ID3 FOREIGN KEY(RECOMMEND_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID);
ALTER TABLE HRIS_TRVL_RECOMMENDER_APPROVER ADD CONSTRAINT FK_RECM_APRV_TRVL_EMP_EMP_ID4 FOREIGN KEY(APPROVED_BY) REFERENCES HRIS_EMPLOYEES(EMPLOYEE_ID);

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
    325,
    'Travel Approval Level',
    301,
    NULL,
    'recommenderApprover',
    'E',
    TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'index',
    6,
    NULL,
    NULL,
    'Y'
  ) ; 
ALTER TABLE HRIS_EMPLOYEES
ADD IS_CEO CHAR (1 BYTE) CHECK (IS_CEO IN ('Y','N'));

ALTER TABLE HRIS_EMPLOYEES
ADD IS_HR CHAR (1 BYTE) CHECK (IS_HR IN ('Y','N'));

ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD ASSIGN_ON_EMPLOYEE_SETUP CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (ASSIGN_ON_EMPLOYEE_SETUP IN ('Y','N'));
ALTER TABLE HRIS_LEAVE_MASTER_SETUP ADD IS_PRODATA_BASIS CHAR(1 BYTE) DEFAULT 'Y'NOT NULL CHECK (IS_PRODATA_BASIS IN ('Y','N'));


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
  ( NULL,
    328,
    'Attendnace Report',
    5,
    NULL,
    'managerReport',
    'E',
    TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'index',
    19,
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
    'EMP',
    325,
    'Edit My Profile',
    53,
    NULL,
    'employee',
    'E',
    to_date('13-JUL-17','DD-MON-RR'),
    NULL,
    'fa fa-wrench',
    'edit',
    44,
    NULL,
    NULL,
    'N'
  );
  

  
  
INSERT INTO HRIS_ROLE_PERMISSIONS
  (MENU_ID,ROLE_ID,STATUS
  )
SELECT (325),ROLE_ID, ('E') FROM HRIS_ROLES;


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



ALTER TABLE HRIS_APPRAISAL_ASSIGN
ADD IS_EXECUTIVE CHAR(1 BYTE) NULL;

ALTER TABLE HRIS_APPRAISAL_ASSIGN
ADD CONSTRAINT CHECK_IS_EXECUTIVE CHECK (IS_EXECUTIVE IN ('Y','N'));
  
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
    (select max(menu_id+1) from HRIS_MENUS),
    'Subordinate Review',
    6,
    NULL,
    'subordinatesReview',
    'E',
      TRUNC(SYSDATE),
    NULL,
    'fa fa-pencil',
    'index',
    20,
    NULL,
    NULL,
    'Y'
    );

ALTER TABLE HRIS_APPRAISAL_ASSIGN 
ADD SUBORDINATES VARCHAR(300) NULL;


ALTER TABLE HRIS_APPRAISAL_STATUS
ADD SUBORdINATES_REVIEW VARCHAR(300) NULL;

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