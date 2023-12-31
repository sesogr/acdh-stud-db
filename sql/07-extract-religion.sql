DROP TABLE IF EXISTS `student_religion_time`;
DROP TABLE IF EXISTS `student_religion_value`;

CREATE TABLE `student_religion_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`rel` AS `religion`
		FROM `student_person`
		WHERE `rel` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`religion` AS `religion`
		FROM `student_person_20161116`
		WHERE `religion` IS NOT NULL
	)
	ORDER BY `person_id`, `religion`;

ALTER TABLE `student_religion_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_religion_time` DEFAULT CHARSET utf8 AS
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
					`rel` AS `religion`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`religion`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_religion_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`religion` = `s`.`religion`
	ORDER BY `s`.`merged_id`, `s`.`religion`, `s`.`semester`;

ALTER TABLE `student_religion_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
