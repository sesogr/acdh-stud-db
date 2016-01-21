DROP TABLE IF EXISTS `student_ethnicity_time`;
DROP TABLE IF EXISTS `student_ethnicity_value`;

CREATE TABLE `student_ethnicity_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`volkszugehoerigkeit` AS `ethnicity`
	FROM `student_person`
	WHERE `volkszugehoerigkeit` IS NOT NULL
	ORDER BY `merged_id`, `ethnicity`;

ALTER TABLE `student_ethnicity_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_ethnicity_time` AS
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
				`volkszugehoerigkeit` AS `ethnicity`
			FROM `student_person`
		) AS `s`
		JOIN `student_ethnicity_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`ethnicity` = `s`.`ethnicity`
	ORDER BY `s`.`merged_id`, `s`.`ethnicity`, `s`.`semester`;

ALTER TABLE `student_ethnicity_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
