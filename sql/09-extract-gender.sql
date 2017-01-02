DROP TABLE IF EXISTS `student_gender_time`;
DROP TABLE IF EXISTS `student_gender_value`;

CREATE TABLE `student_gender_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`geschl` AS `gender`
		FROM `student_person`
		WHERE `geschl` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`geschlecht` AS `gender`
		FROM `student_person_20161116`
		WHERE `geschlecht` IS NOT NULL
	)
	ORDER BY `person_id`, `gender`;

ALTER TABLE `student_gender_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_gender_time` DEFAULT CHARSET utf8 AS
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
					`geschl` AS `gender`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`geschlecht` AS `gender`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_gender_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`gender` = `s`.`gender`
	ORDER BY `s`.`merged_id`, `s`.`gender`, `s`.`semester`;

ALTER TABLE `student_gender_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
