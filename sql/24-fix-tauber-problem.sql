UPDATE `student_lecture`
SET `merged_id` = 381
WHERE `merged_id` IN (1477, 2027, 2266, 1801);

UPDATE `student_attendance`
SET `person_id` = 381
WHERE `person_id` IN (1477, 2027, 2266, 1801);
