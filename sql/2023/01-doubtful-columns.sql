alter table student_birth_date_value add is_doubtful set ('true') default '' not null;
alter table student_birth_place_value add is_doubtful set ('true') default '' not null;
alter table student_father_value add is_doubtful set ('true') default '' not null;
alter table student_gender_value add is_doubtful set ('true') default '' not null;
alter table student_given_names_value add is_doubtful set ('true') default '' not null;
alter table student_guardian_value add is_doubtful set ('true') default '' not null;
alter table student_last_name_value add is_doubtful set ('true') default '' not null;
alter table student_last_school_value add is_doubtful set ('true') default '' not null;
alter table student_remarks_value add is_doubtful set ('true') default '' not null;

DROP VIEW IF EXISTS `v_student_complete`;

CREATE VIEW `v_student_complete` AS
(
    SELECT
        `person_id`,
        'biography' AS `property`,
        `v`.`id`,
        `biography` AS `value`,
        `is_from_supplemental_data_source` AS `value2`,
        NULL AS `value3`,
        NULL AS `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_biography_value` `v`
             LEFT JOIN `student_biography_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'birth_date' AS `property`,
        `v`.`id`,
        `birth_date` AS `value`,
        `is_from_supplemental_data_source` AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_birth_date_value` `v`
             LEFT JOIN `student_birth_date_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'birth_place' AS `property`,
        `v`.`id`,
        `birth_place` AS `value`,
        `birth_country_historic` AS `value2`,
        `birth_country_today` AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_birth_place_value` `v`
             LEFT JOIN `student_birth_place_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'ethnicity' AS `property`,
        `v`.`id`,
        `ethnicity` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL AS `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_ethnicity_value` `v`
             LEFT JOIN `student_ethnicity_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'father' AS `property`,
        `v`.`id`,
        `father` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_father_value` `v`
             LEFT JOIN `student_father_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'gender' AS `property`,
        `v`.`id`,
        `gender` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_gender_value` `v`
             LEFT JOIN `student_gender_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'given_names' AS `property`,
        `v`.`id`,
        `given_names` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_given_names_value` `v`
             LEFT JOIN `student_given_names_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'graduation' AS `property`,
        `v`.`id`,
        `graduation` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL as `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_graduation_value` `v`
             LEFT JOIN `student_graduation_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'guardian' AS `property`,
        `v`.`id`,
        `guardian` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_guardian_value` `v`
             LEFT JOIN `student_guardian_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'language' AS `property`,
        `v`.`id`,
        `language` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL as `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_language_value` `v`
             LEFT JOIN `student_language_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'last_name' AS `property`,
        `v`.`id`,
        `last_name` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_last_name_value` `v`
             LEFT JOIN `student_last_name_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'last_school' AS `property`,
        `v`.`id`,
        `last_school` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_last_school_value` `v`
             LEFT JOIN `student_last_school_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'literature' AS `property`,
        `v`.`id`,
        `literature` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL as `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_literature_value` `v`
             LEFT JOIN `student_literature_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'nationality' AS `property`,
        `v`.`id`,
        `nationality` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL as `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_nationality_value` `v`
             LEFT JOIN `student_nationality_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'religion' AS `property`,
        `v`.`id`,
        `religion` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL AS `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_religion_value` `v`
             LEFT JOIN `student_religion_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'remarks' AS `property`,
        `v`.`id`,
        `remarks` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_remarks_value` `v`
             LEFT JOIN `student_remarks_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
)
UNION (
    SELECT
        `person_id`,
        'studying_address' AS `property`,
        `v`.`id`,
        `studying_address` AS `value`,
        NULL AS `value2`,
        NULL AS `value3`,
        NULL AS `is_doubtful`,
        group_concat(DISTINCT `t`.`time` SEPARATOR '; ') AS `times`,
        `year_min`,
        `year_max`
    FROM `student_studying_address_value` `v`
             LEFT JOIN `student_studying_address_time` `t` ON `t`.`value_id` = `v`.`id`
    GROUP BY `v`.`id`
);

drop view if exists v_most_precise_birth_date;
create view v_most_precise_birth_date as
select most_precise.*
from student_birth_date_value most_precise
         left join student_birth_date_value more_precise
                   on more_precise.person_id = most_precise.person_id
                       and more_precise.born_on_or_after > most_precise.born_on_or_after
                       and more_precise.born_on_or_before < most_precise.born_on_or_before
where more_precise.person_id is null
order by most_precise.person_id;
