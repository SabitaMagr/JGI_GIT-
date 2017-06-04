ALTER TABLE HRIS_ACADEMIC_COURSES MODIFY ACADEMIC_COURSE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_ACADEMIC_DEGREES MODIFY ACADEMIC_DEGREE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_ACADEMIC_PROGRAMS MODIFY ACADEMIC_PROGRAM_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_ACADEMIC_UNIVERSITY MODIFY ACADEMIC_UNIVERSITY_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_ADVANCE_MASTER_SETUP MODIFY ADVANCE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_APPRAISAL_HEADING MODIFY HEADING_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_APPRAISAL_QUESTION MODIFY QUESTION_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_APPRAISAL_SETUP MODIFY APPRAISAL_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_APPRAISAL_STAGE MODIFY STAGE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_APPRAISAL_TYPE MODIFY APPRAISAL_TYPE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_ASSET_GROUP MODIFY ASSET_GROUP_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_ASSET_SETUP MODIFY ASSET_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_BRANCHES MODIFY BRANCH_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_COMPANY MODIFY COMPANY_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_DEPARTMENTS MODIFY DEPARTMENT_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_DESIGNATIONS MODIFY DESIGNATION_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_EMPLOYEES MODIFY EMPLOYEE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_FLAT_VALUE_SETUP MODIFY FLAT_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_HOLIDAY_MASTER_SETUP MODIFY HOLIDAY_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_INSTITUTE_MASTER_SETUP MODIFY INSTITUTE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_LEAVE_MASTER_SETUP MODIFY LEAVE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_LOAN_MASTER_SETUP MODIFY LOAN_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_MENUS MODIFY MENU_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_MONTHLY_VALUE_SETUP MODIFY MTH_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_PAY_SETUP MODIFY PAY_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_SERVICE_EVENT_TYPES MODIFY SERVICE_EVENT_TYPE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_SERVICE_TYPES MODIFY SERVICE_TYPE_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_SHIFTS MODIFY SHIFT_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_TRAINING_MASTER_SETUP MODIFY TRAINING_CODE VARCHAR2(15 BYTE) NULL;

ALTER TABLE HRIS_TRAINING_MASTER_SETUP MODIFY INSTRUCTOR_NAME VARCHAR2(200 BYTE) NULL;

ALTER TABLE HRIS_TRAINING_MASTER_SETUP MODIFY INSTITUTE_ID NUMBER(7,0) NULL;
