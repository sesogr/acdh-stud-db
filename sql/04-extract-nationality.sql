DROP TABLE IF EXISTS `student_nationality_time`;
DROP TABLE IF EXISTS `student_nationality_value`;

CREATE TABLE `student_nationality_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`staatsbuergerschaft` AS `nationality`
		FROM `student_person`
		WHERE `staatsbuergerschaft` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`staatsbuergerschaft` AS `nationality`
		FROM `student_person_20161116`
		WHERE `staatsbuergerschaft` IS NOT NULL
	)
	ORDER BY `person_id`, `nationality`;

ALTER TABLE `student_nationality_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_nationality_time` DEFAULT CHARSET utf8 AS
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
					`staatsbuergerschaft` AS `nationality`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`staatsbuergerschaft` AS `nationality`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_nationality_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`nationality` = `s`.`nationality`
	ORDER BY `s`.`merged_id`, `s`.`nationality`, `s`.`semester`;

ALTER TABLE `student_nationality_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
