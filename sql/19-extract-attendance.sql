DROP TABLE IF EXISTS `student_attendance`;

CREATE TABLE `student_attendance` AS
	SELECT
		`person_id`,
		ifnull(`l`.`x_semester`, ifnull(`p2`.`semester`, `p1`.`semester`)) `semester_abs`,
		`semester_rel`,
		`lecturer`,
		`class`,
		`remarks`
	FROM (
			(
				SELECT DISTINCT
					`merged_id` AS `person_id`,
					`x_semester`,
					`x_semester_extra` `semester_rel`,
					`x_lecturer` `lecturer`,
					`x_class` `class`,
					concat_ws(';', nullif(`x_class_extra`, ''), nullif(`anmerkungen`, '')) `remarks`
				FROM `student_lecture`
			)
			UNION (
				SELECT DISTINCT
					`merged` AS `person_id`,
					`ws_ss`,
					`semester` `semester_rel`,
					`dozent` `lecturer`,
					`vorlesung` `class`,
					`anmerkung` `remarks`
				FROM `student_lecture_20161116`
			)
		) `l`
		LEFT JOIN `student_person` `p1` ON `p1`.`student_id` = `l`.`person_id`
		LEFT JOIN `student_person_20161116` `p2` ON `p2`.`id` = `l`.`person_id`;

ALTER TABLE `student_attendance`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);
