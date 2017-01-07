DROP TABLE IF EXISTS `student_literature_time`;
DROP TABLE IF EXISTS `student_literature_value`;

CREATE TABLE `student_literature_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			`literaturhinweise` AS `literature`
		FROM `student_person`
		WHERE `literaturhinweise` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			`literaturhinweise` AS `literature`
		FROM `student_person_20161116`
		WHERE `literaturhinweise` IS NOT NULL
	)
	ORDER BY `person_id`, `literature`;

ALTER TABLE `student_literature_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_literature_time` DEFAULT CHARSET utf8 AS
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
					`literaturhinweise` AS `literature`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id` AS `merged_id`,
					`semester`,
					`year_min`,
					`year_max`,
					`literaturhinweise` AS `literature`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_literature_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`literature` = `s`.`literature`
	ORDER BY `s`.`merged_id`, `s`.`literature`, `s`.`semester`;

ALTER TABLE `student_literature_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
