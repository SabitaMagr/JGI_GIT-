SELECT * FROM (
SELECT
    fact.*,
    ( la.total_days - la.balance ) AS actual_days
FROM
    hris_employee_leave_assign la
    JOIN (
        SELECT
            lr.employee_id,
            lr.leave_id,
            SUM(lr.no_of_days) AS days
        FROM
            hris_employee_leave_request lr
        WHERE
            lr.status = 'AP'
        GROUP BY
            lr.employee_id,
            lr.leave_id
    ) fact ON (
            la.employee_id = fact.employee_id
        AND
            la.leave_id = fact.leave_id
    )) WHERE days!=actual_days;
--    