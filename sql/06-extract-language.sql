DROP TABLE IF EXISTS `student_language_time`;
DROP TABLE IF EXISTS `student_language_value`;

CREATE TABLE `student_language_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`mspr` AS `language`
	FROM `student_person`
	WHERE `mspr` IS NOT NULL
	ORDER BY `merged_id`, `language`;

ALTER TABLE `student_language_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_language_time` AS
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
				`mspr` AS `language`
			FROM `student_person`
		) AS `s`
		JOIN `student_language_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`language` = `s`.`language`
	ORDER BY `s`.`merged_id`, `s`.`language`, `s`.`semester`;

ALTER TABLE `student_language_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
