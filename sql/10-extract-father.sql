DROP TABLE IF EXISTS `student_father_time`;
DROP TABLE IF EXISTS `student_father_value`;

CREATE TABLE `student_father_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`vater` AS `father`
	FROM `student_person`
	WHERE `vater` IS NOT NULL
	ORDER BY `merged_id`, `father`;

ALTER TABLE `student_father_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_father_time` AS
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
				`vater` AS `father`
			FROM `student_person`
		) AS `s`
		JOIN `student_father_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`father` = `s`.`father`
	ORDER BY `s`.`merged_id`, `s`.`father`, `s`.`semester`;

ALTER TABLE `student_father_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
