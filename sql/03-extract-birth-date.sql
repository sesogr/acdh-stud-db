DROP TABLE IF EXISTS `student_birth_date_time`;
DROP TABLE IF EXISTS `student_birth_date_value`;

CREATE TABLE `student_birth_date_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`geb` AS `birth_date`,
			`born_min` AS `born_on_or_after`,
			`born_max` AS `born_on_or_before`,
			'' AS `is_from_supplemental_data_source`
		FROM `student_person`
		WHERE `geb` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`geb_dat` AS `birth_date`,
			`born_min` AS `born_on_or_after`,
			`born_max` AS `born_on_or_before`,
			if(ifnull(geb_dat_gruen, '') = '', '', 'true') AS `is_from_supplemental_data_source`
		FROM `student_person_20161116`
		WHERE `geb_dat` IS NOT NULL
	)
	ORDER BY `person_id`, `birth_date`;

ALTER TABLE `student_birth_date_value`
MODIFY `is_from_supplemental_data_source` SET ('true') NOT NULL DEFAULT '',
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_birth_date_time` DEFAULT CHARSET utf8 AS
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
					`geb` AS `birth_date`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`geb_dat` AS `birth_date`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_birth_date_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`birth_date` = `s`.`birth_date`
	ORDER BY `s`.`merged_id`, `s`.`birth_date`, `s`.`semester`;

ALTER TABLE `student_birth_date_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
