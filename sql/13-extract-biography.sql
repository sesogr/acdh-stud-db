DROP TABLE IF EXISTS `student_biography_time`;
DROP TABLE IF EXISTS `student_biography_value`;

CREATE TABLE `student_biography_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`biogr` AS `biography`
	FROM `student_person`
	WHERE `biogr` IS NOT NULL
	ORDER BY `merged_id`, `biography`;

ALTER TABLE `student_biography_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_biography_time` AS
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
				`biogr` AS `biography`
			FROM `student_person`
		) AS `s`
		JOIN `student_biography_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`biography` = `s`.`biography`
	ORDER BY `s`.`merged_id`, `s`.`biography`, `s`.`semester`;

ALTER TABLE `student_biography_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
