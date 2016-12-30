DROP TABLE IF EXISTS `student_birth_place_time`;
DROP TABLE IF EXISTS `student_birth_place_value`;

CREATE TABLE `student_birth_place_value` AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`geb_ort` AS `birth_place`,
			`geb_land` AS `birth_country_historic`,
			`geb_land_heute` AS `birth_country_today`
		FROM `student_person`
		WHERE `geb_ort` IS NOT NULL OR `geb_land` IS NOT NULL OR `geb_land_heute` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`geburtsort` AS `birth_place`,
			`geburtsland_historisch` AS `birth_country_historic`,
			`geburtsland_heute` AS `birth_country_today`
		FROM `student_person_20161116`
		WHERE `geburtsort` IS NOT NULL OR `geburtsland_historisch` IS NOT NULL OR `geburtsland_heute` IS NOT NULL
	)
	ORDER BY `person_id`, `birth_place`;

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
			(
				SELECT DISTINCT
					`merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`geb_ort` AS `birth_place`,
					`geb_land` AS `birth_country_historic`,
					`geb_land_heute` AS `birth_country_today`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`geburtsort` AS `birth_place`,
					`geburtsland_historisch` AS `birth_country_historic`,
					`geburtsland_heute` AS `birth_country_today`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_birth_place_value` AS `v` ON `v`.`person_id` = `s`.`merged_id`
			AND `v`.`birth_place` = `s`.`birth_place`
			AND `v`.`birth_country_historic` = `s`.`birth_country_historic`
			AND `v`.`birth_country_today` = `s`.`birth_country_today`
	ORDER BY `s`.`merged_id`, `s`.`birth_place`, `s`.`semester`;

ALTER TABLE `student_birth_place_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
