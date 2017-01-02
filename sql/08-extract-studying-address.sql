DROP TABLE IF EXISTS `student_studying_address_time`;
DROP TABLE IF EXISTS `student_studying_address_value`;

CREATE TABLE `student_studying_address_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`wohnadr_stud` AS `studying_address`
		FROM `student_person`
		WHERE `wohnadr_stud` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`adresse_student` AS `studying_address`
		FROM `student_person_20161116`
		WHERE `adresse_student` IS NOT NULL
	)
	ORDER BY `person_id`, `studying_address`;

ALTER TABLE `student_studying_address_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_studying_address_time` DEFAULT CHARSET utf8 AS
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
					`wohnadr_stud` AS `studying_address`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`adresse_student` AS `studying_address`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_studying_address_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`studying_address` = `s`.`studying_address`
	ORDER BY `s`.`merged_id`, `s`.`studying_address`, `s`.`semester`;

ALTER TABLE `student_studying_address_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
