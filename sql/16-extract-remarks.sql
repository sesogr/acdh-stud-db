DROP TABLE IF EXISTS `student_remarks_time`;
DROP TABLE IF EXISTS `student_remarks_value`;

CREATE TABLE `student_remarks_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`anmerkung` AS `remarks`
		FROM `student_person`
		WHERE `anmerkung` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`anmerkung` AS `remarks`
		FROM `student_person_20161116`
		WHERE `anmerkung` IS NOT NULL
	)
	ORDER BY `person_id`, `remarks`;

ALTER TABLE `student_remarks_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_remarks_time` DEFAULT CHARSET utf8 AS
	SELECT
		`v`.`id` AS `value_id`,
		`semester` AS `time`,
		`year_min`,
		`year_max`
	FROM
		(
			(
				SELECT DISTINCT
					`merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`anmerkung` AS `remarks`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`anmerkung` AS `remarks`
				FROM `student_person`
			)
		) AS `s`
		JOIN `student_remarks_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`remarks` = `s`.`remarks`
	ORDER BY `s`.`merged_id`, `s`.`remarks`, `s`.`semester`;

ALTER TABLE `student_remarks_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
