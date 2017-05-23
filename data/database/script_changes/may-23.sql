
--  first remove all check chnstraints from hris_dashboard_detail
alter table
   hris_dashboard_detail
add constraint
   check_role_types
   CHECK 
   (ROLE_TYPE IN ('A','B','D','E'));
