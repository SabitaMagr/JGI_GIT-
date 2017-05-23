
--  first remove all check chnstraints from hris_dashboard_detail
alter table
   hris_dashboard_detail
add constraint
   check_role_types
   CHECK 
   (ROLE_TYPE IN ('A','B','D','E'));

-- 2

INSERT INTO HRIS_SERVICE_EVENT_TYPES (SERVICE_EVENT_TYPE_ID,SERVICE_EVENT_TYPE_NAME,STATUS,CREATED_DT) VALUES(18,'Contract Extension','E',TRUNC(SYSDATE));

INSERT INTO HRIS_SERVICE_EVENT_TYPES (SERVICE_EVENT_TYPE_ID,SERVICE_EVENT_TYPE_NAME,STATUS,CREATED_DT) VALUES(19,'Service Extension','E',TRUNC(SYSDATE));

