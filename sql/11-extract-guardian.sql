DROP TABLE IF EXISTS `student_guardian_time`;
DROP TABLE IF EXISTS `student_guardian_value`;

CREATE TABLE `student_guardian_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`vormund` AS `guardian`
		FROM `student_person`
		WHERE `vormund` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`vormund` AS `guardian`
		FROM `student_person_20161116`
		WHERE `vormund` IS NOT NULL
	)
	ORDER BY `person_id`, `guardian`;

ALTER TABLE `student_guardian_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_guardian_time` DEFAULT CHARSET utf8 AS
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
					`vormund` AS `guardian`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`vormund` AS `guardian`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_guardian_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`guardian` = `s`.`guardian`
	ORDER BY `s`.`merged_id`, `s`.`guardian`, `s`.`semester`;

ALTER TABLE `student_guardian_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
