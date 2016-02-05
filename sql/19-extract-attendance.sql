DROP TABLE IF EXISTS `student_attendance`;

CREATE TABLE `student_attendance` AS
	SELECT DISTINCT
		`p`.`merged_id` `person_id`,
		ifnull(`l`.`x_semester`, `p`.`semester`) `semester_abs`,
		`l`.`x_semester_extra` `semester_rel`,
		`l`.`x_lecturer` `lecturer`,
		`l`.`x_class` `class`,
		`l`.`x_class_extra` `class_extra`,
		`l`.`anmerkungen` `remarks`
	FROM `student_lecture` `l`
		JOIN `student_person` `p` ON p.student_id = l.student_id;

ALTER TABLE `student_attendance`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);
