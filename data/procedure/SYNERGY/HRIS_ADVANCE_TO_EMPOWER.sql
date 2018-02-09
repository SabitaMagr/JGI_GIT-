CREATE OR REPLACE PROCEDURE HRIS_ADVANCE_TO_EMPOWER(
    COMPANY_CODE_V    VARCHAR2,
    BRANCH_CODE_V     VARCHAR2,
    TRANSACTION_DATE  DATE,
    INSTALLMENT_DATE  DATE,
    CREATED_BY        VARCHAR2,
    ADVANCE_AMOUNT    NUMBER,
    NO_OF_INSTALLMENT NUMBER,
    PER_MONTH         VARCHAR2,
    vEmployeeCode     VARCHAR2,
    vDrAccount        VARCHAR2,
    vCrAccount        VARCHAR2)
IS
  iReqNo    NUMBER;
  dAdvPFrom DATE;
  dAdvPTo   DATE;
  iDays     NUMBER;
  vBSmonth  VARCHAR2(10);
  dDate     DATE;
BEGIN
  BEGIN
    SELECT MAX(NVL(TO_NUMBER(REQUEST_NO),0)) + 1
    INTO IREQNO
    FROM HR_ADVANCE_REQUEST
    WHERE COMPANY_CODE = COMPANY_CODE_V;
  EXCEPTION
  WHEN OTHERS THEN
    IREQNO := 1;
  END;
  BEGIN
    SELECT START_DATE ,
      END_DATE
    INTO dAdvPFrom,
      dAdvPTo
    FROM HR_PERIOD_DETAIL
    WHERE TO_DATE(TRANSACTION_DATE) BETWEEN START_DATE AND END_DATE
    AND COMPANY_CODE = COMPANY_CODE_V;
  EXCEPTION
  WHEN OTHERS THEN
    NULL;
  END;
  INSERT
  INTO HR_ADVANCE_REQUEST
    (
      REQUEST_NO,
      REQUEST_DATE,
      EMPLOYEE_CODE,
      ADVANCE_TYPE,
      REQUEST_AMOUNT,
      ACC_CODE,
      REPAYMENT_START_DATE,
      REPAYMENT_COUNT,
      REPAYMENT_PERIOD_FLAG,
      COMPANY_CODE,
      BRANCH_CODE,
      CREATED_BY,
      CREATED_DATE,
      DELETED_FLAG,
      PREMIUM_TYPE,
      ACC_CODE_CR
    )
    VALUES
    (
      IREQNO,
      TRANSACTION_DATE,
      vEmployeeCode ,
      'R019',
      ADVANCE_AMOUNT,
      vDrAccount,
      TRANSACTION_DATE ,
      NO_OF_INSTALLMENT,
      'M',
      COMPANY_CODE_V,
      BRANCH_CODE_V,
      CREATED_BY,
      SYSDATE,
      'N',
      0 ,
      vCrAccount
    );
  INSERT
  INTO HR_ADVANCE_REQUEST_DETAIL
    (
      REQUEST_NO,
      SERIAL_NO,
      FROM_DATE,
      TO_DATE,
      AMOUNT,
      PAID_FLAG,
      COMPANY_CODE,
      BRANCH_CODE,
      CREATED_BY,
      CREATED_DATE,
      DELETED_FLAG
    )
    VALUES
    (
      IREQNO,
      1,
      dAdvPFrom,
      dAdvPTo,
      PER_MONTH,
      'N',
      COMPANY_CODE_V ,
      BRANCH_CODE_V,
      CREATED_BY,
      SYSDATE,
      'N'
    );
  FOR I IN 2..NO_OF_INSTALLMENT
  LOOP
    dAdvPTo :=dAdvPTo + 1;
    SELECT BS_MONTH,
      AD_DATE,
      DAYS_NO
    INTO vBSmonth,
      dDate,
      iDays
    FROM CALENDAR_SETUP
    WHERE BS_MONTH = SUBSTR(BS_DATE(dAdvPTo),1,7);
    SELECT ad_date(vBSmonth||'-01') INTO dAdvPFrom FROM dual;
    SELECT ad_date(vBSmonth||'-'||iDays) INTO dAdvPTo FROM duaL;
    INSERT
    INTO HR_ADVANCE_REQUEST_DETAIL
      (
        REQUEST_NO,
        SERIAL_NO,
        FROM_DATE,
        TO_DATE,
        AMOUNT,
        PAID_FLAG,
        COMPANY_CODE,
        BRANCH_CODE,
        CREATED_BY,
        CREATED_DATE,
        DELETED_FLAG
      )
      VALUES
      (
        IREQNO,
        I,
        dAdvPFrom,
        dAdvPTo,
        PER_MONTH,
        'N',
        COMPANY_CODE_V ,
        BRANCH_CODE_V,
        CREATED_BY,
        SYSDATE,
        'N'
      );
  END LOOP;
EXCEPTION
WHEN OTHERS THEN
  raise_application_error(-20001,'An error was encountered - '||SQLCODE||' -ERROR- '||SQLERRM);
END;
