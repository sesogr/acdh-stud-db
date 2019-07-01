update student_attendance
    set semester_abs = concat_ws(' ', 'W', substr(semester_abs, 2))
    where substr(semester_abs, 1, 2) = 'W1';
update student_birth_date_value
    set birth_date        = '25.09.1879'
      , born_on_or_after  = '1879-09-25'
      , born_on_or_before = '1879-09-25'
    where person_id = 8285;
create view v_most_precise_birth_date as
    select most_precise.*
        from student_birth_date_value most_precise
        left join student_birth_date_value more_precise
            on more_precise.person_id = most_precise.person_id
            and more_precise.born_on_or_after > most_precise.born_on_or_after
            and more_precise.born_on_or_before < most_precise.born_on_or_before
        where more_precise.person_id is null
        order by most_precise.person_id;
