/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  itnepal
 * Created: May 10, 2017
 */


-- NEO_HRIS | SOMKALA PACHHAI | 11 MAY 2017
alter table 
   HRIS_OVERTIME_DETAIL
modify 
( 
   TOTAL_HOUR    TIMESTAMP(6)
);

  ALTER TABLE HRIS_OVERTIME
    ADD TOTAL_HOUR TIMESTAMP(6);
--