DECLARE
  V_P_MENU_ID NUMBER(7,0);
BEGIN
  SELECT NVL(MAX(MENU_ID),0)+1 INTO V_P_MENU_ID FROM HRIS_MENUS ;
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
      V_P_MENU_ID,
      'Payroll Previous',
      6,
      'Link to Previous Payroll',
      'javascript::',
      'E',
      to_date('08-JAN-18','DD-MON-RR'),
      NULL,
      'fa fa-square-o',
      'javascript::',
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
      (SELECT NVL(MAX(MENU_ID),0)+1 FROM HRIS_MENUS
      ) ,
      'Tax Sheet',
      V_P_MENU_ID,
      NULL,
      'payslip-previous',
      'E',
      to_date('08-JAN-18','DD-MON-RR'),
      NULL,
      'fa fa-square-o',
      'taxsheet',
      2,
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
      (SELECT NVL(MAX(MENU_ID),0)+1 FROM HRIS_MENUS
      ),
      'Payslip',
      V_P_MENU_ID,
      NULL,
      'payslip-previous',
      'E',
      to_date('08-JAN-18','DD-MON-RR'),
      NULL,
      'fa fa-square-o',
      'payslip',
      1,
      NULL,
      NULL,
      'Y'
    );
END;
/
