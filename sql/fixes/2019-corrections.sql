update student_attendance
    set semester_abs = 'W 1886/87'
    where semester_abs = 'W 1886/67';
update student_attendance
    set semester_abs = 'S 1881'
    where semester_abs = 'W 1881/82' and semester_rel is null;
update student_last_name_value
    set last_name = 'Porumbescu [Gołęmbiowski, Galembiawski]', ascii_last_name = 'porumbescu golembiowski galembiawski'
    where person_id = 3819;
update student_given_names_value
    set given_names = 'Cyprian', ascii_given_names= 'cyprian'
    where person_id = 3819;
