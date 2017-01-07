DROP TABLE IF EXISTS `student_last_name_time`;
DROP TABLE IF EXISTS `student_last_name_value`;

CREATE TABLE `student_last_name_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			substring_index(`name`, ' ', 1) AS `last_name`
		FROM `student_person`
		WHERE `name` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			substring_index(`name`, ' ', 1) AS `last_name`
		FROM `student_person_20161116`
		WHERE `name` IS NOT NULL
	)
	ORDER BY `person_id`, `last_name`;

ALTER TABLE `student_last_name_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_last_name_time` DEFAULT CHARSET utf8 AS
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
					substring_index(`name`, ' ', 1) AS `last_name`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id`,
					`semester`,
					`year_min`,
					`year_max`,
					substring_index(`name`, ' ', 1) AS `last_name`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_last_name_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`last_name` = `s`.`last_name`
	ORDER BY `s`.`merged_id`, `s`.`last_name`, `s`.`semester`;

ALTER TABLE `student_last_name_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
