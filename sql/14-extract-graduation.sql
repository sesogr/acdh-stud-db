DROP TABLE IF EXISTS `student_graduation_time`;
DROP TABLE IF EXISTS `student_graduation_value`;

CREATE TABLE `student_graduation_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`prom` AS `graduation`
	FROM `student_person`
	WHERE `prom` IS NOT NULL
	ORDER BY `merged_id`, `graduation`;

ALTER TABLE `student_graduation_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_graduation_time` AS
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
				`prom` AS `graduation`
			FROM `student_person`
		) AS `s`
		JOIN `student_graduation_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`graduation` = `s`.`graduation`
	ORDER BY `s`.`merged_id`, `s`.`graduation`, `s`.`semester`;

ALTER TABLE `student_graduation_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
