create or replace FUNCTION LEAVE_STATUS_DESC(
    P_STATUS HRIS_EMPLOYEE_LEAVE_REQUEST.STATUS%TYPE)
  RETURN VARCHAR2
IS
  V_STATUS_DESC VARCHAR2(50 BYTE);
BEGIN
  V_STATUS_DESC:=
  (
    CASE P_STATUS
    WHEN 'RQ' THEN
      'Pending'
    WHEN 'RC'THEN
      'Recommended'
    WHEN 'R' THEN
      'Rejected'
    WHEN 'AP' THEN
      'Approved'
    WHEN 'C' THEN
      'Cancelled'
    END);
  RETURN V_STATUS_DESC;
END;