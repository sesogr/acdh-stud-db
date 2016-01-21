DROP TABLE IF EXISTS `student_last_school_time`;
DROP TABLE IF EXISTS `student_last_school_value`;

CREATE TABLE `student_last_school_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`schule_zuletzt` AS `last_school`
	FROM `student_person`
	WHERE `schule_zuletzt` IS NOT NULL
	ORDER BY `merged_id`, `last_school`;

ALTER TABLE `student_last_school_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_last_school_time` AS
	SELECT
		`v`.`id` AS `value_id`,
		`semester` AS `time`,
		`year_min`,
		`year_max`
	FROM
		(
			SELECT DISTINCT
				`merged_id`,
				`semester`,
				`year_min`,
				`year_max`,
				`schule_zuletzt` AS `last_school`
			FROM `student_person`
		) AS `s`
		JOIN `student_last_school_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`last_school` = `s`.`last_school`
	ORDER BY `s`.`merged_id`, `s`.`last_school`, `s`.`semester`;

ALTER TABLE `student_last_school_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
