DROP TABLE IF EXISTS `student_attendance`;

CREATE TABLE `student_attendance` DEFAULT CHARSET utf8 AS
	SELECT
		ifnull(p1.merged_id, ifnull(p2.id, p3.id)) `person_id`,
		ifnull(`l`.`x_semester`, if(`l`.`student_id` IS NULL, ifnull(`p2`.`semester`, `p3`.`semester`), `p1`.`semester`)) `semester_abs`,
		`semester_rel`,
		ifnull(`l`.`faculty`, ifnull(`p2`.`fakultaet`, `p3`.`fakultaet`)) `faculty`,
		`lecturer`,
		`class`,
		`remarks`
	FROM (
			(
				SELECT DISTINCT
					`merged_id` AS `person_id`,
					`student_id`,
					null `seq_no`,
					`x_semester`,
					`x_semester_extra` `semester_rel`,
					`x_lecturer` `lecturer`,
					`x_class` `class`,
					concat_ws(';', nullif(`x_class_extra`, ''), nullif(`anmerkungen`, '')) `remarks`,
					'Phil. Fak.' `faculty`
				FROM `student_lecture`
			)
			UNION (
				SELECT DISTINCT
					`merged` AS `person_id`,
					null `student_id`,
					`id` `seq_no`,
					`ws_ss`,
					`semester` `semester_rel`,
					`dozent` `lecturer`,
					`vorlesung` `class`,
					`anmerkung` `remarks`,
					null
				FROM `student_lecture_20161116`
			)
		) `l`
		LEFT JOIN `student_person` `p1`
			ON `l`.`seq_no` IS NULL # restrict matches to first lecture table only
			AND ( # do not use IN; otherwise any null matches will be included as well
				`p1`.`merged_id` = `l`.`person_id`
				OR `p1`.`merged_id` = `l`.`student_id`
				OR `p1`.`student_id` = `l`.`person_id`
				OR `p1`.`student_id` = `l`.`student_id`
			)
		LEFT JOIN `student_person_20161116` `p2` ON `p2`.`lfd_nr` = `l`.`seq_no`
		LEFT JOIN `student_person_20161116` `p3` ON `p3`.`id` = `l`.`person_id`;

ALTER TABLE `student_attendance`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);
