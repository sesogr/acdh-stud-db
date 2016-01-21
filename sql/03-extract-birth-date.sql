DROP TABLE IF EXISTS `student_birth_date_time`;
DROP TABLE IF EXISTS `student_birth_date_value`;

CREATE TABLE `student_birth_date_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`geb` AS `birth_date`,
		`born_min` AS `born_on_or_after`,
		`born_max` AS `born_on_or_before`
	FROM `student_person`
	WHERE `geb` IS NOT NULL
	ORDER BY `merged_id`, `birth_date`;

ALTER TABLE `student_birth_date_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_birth_date_time` AS
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
				`geb` AS `birth_date`
			FROM `student_person`
		) AS `s`
		JOIN `student_birth_date_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`birth_date` = `s`.`birth_date`
	ORDER BY `s`.`merged_id`, `s`.`birth_date`, `s`.`semester`;

ALTER TABLE `student_birth_date_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
