create or replace function lunch_in_time(p_employee_id in number, p_attendnace_dt in date,p_shift_id in number)    
return Char    
is     
-- variables
v_half_time char(20 byte):=null;
begin

select 
half_out_time
into v_half_time
from (select 
to_char(Attendance_Time,'HH:MI AM') as half_out_time
from  Hris_Attendance where employee_id=p_employee_id and Attendance_Dt=p_attendnace_dt
and attendance_time>(
select 
to_timestamp(TO_CHAR(p_attendnace_dt,'DD-MON-YY')||TO_CHAR(start_time,'HH:MI AM'),'DD-MON-YY HH:MI AM')+ INTERVAL '2' HOUR
 from hris_shifts where shift_id=p_shift_id
) 
and 
attendance_time<(
select 
to_timestamp(
case when two_day_shift='E'
then
TO_CHAR(p_attendnace_dt+1,'DD-MON-YY')
else
TO_CHAR(p_attendnace_dt,'DD-MON-YY')
end 
||TO_CHAR(end_time,'HH:MI AM'),'DD-MON-YY HH:MI AM')- INTERVAL '2' HOUR
 from hris_shifts where shift_id=p_shift_id
) 
order by Attendance_Time asc) where Rownum=1;

--select systimestamp into v_half_time from dual;


return v_half_time;    
end;  

