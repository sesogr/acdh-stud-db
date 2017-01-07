ALTER TABLE `student_last_name_value`
	DEFAULT CHARSET utf8,
	ADD `ascii_last_name` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `student_given_names_value`
	DEFAULT CHARSET utf8,
	ADD `ascii_given_names` VARCHAR(255) DEFAULT NULL;

ALTER TABLE `student_attendance`
	DEFAULT CHARSET utf8,
	ADD `ascii_lecturer` VARCHAR(255) DEFAULT NULL;

UPDATE `student_last_name_value`
	SET `ascii_last_name` = lower(`last_name`);

UPDATE `student_given_names_value`
	SET `ascii_given_names` = lower(`given_names`);

UPDATE `student_attendance`
	SET `ascii_lecturer` = lower(`lecturer`);
