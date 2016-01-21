DROP TABLE IF EXISTS `student_birth_place_time`;
DROP TABLE IF EXISTS `student_birth_place_value`;

CREATE TABLE `student_birth_place_value` AS
	SELECT DISTINCT
		`merged_id` AS `person_id`,
		`geb_ort` AS `birth_place`,
		`geb_land` AS `birth_country`
	FROM `student_person`
	WHERE `geb_ort` IS NOT NULL OR `geb_land` IS NOT NULL
	ORDER BY `merged_id`, `birth_place`;

ALTER TABLE `student_birth_place_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_birth_place_time` AS
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
				`geb_ort` AS `birth_place`,
				`geb_land` AS `birth_country`
			FROM `student_person`
		) AS `s`
		JOIN `student_birth_place_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`birth_place` = `s`.`birth_place` AND `v`.`birth_country` = `s`.`birth_country`
	ORDER BY `s`.`merged_id`, `s`.`birth_place`, `s`.`semester`;

ALTER TABLE `student_birth_place_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
