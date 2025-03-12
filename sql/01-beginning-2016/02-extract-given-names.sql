DROP TABLE IF EXISTS `student_given_names_time`;
DROP TABLE IF EXISTS `student_given_names_value`;

CREATE TABLE `student_given_names_value` DEFAULT CHARSET utf8 AS
	(
		SELECT DISTINCT
			`merged_id` AS `person_id`,
			substring(`name` FROM 2 + char_length(substring_index(`name`, ' ', 1))) AS `given_names`
		FROM `student_person`
		WHERE `name` IS NOT NULL
	)
	UNION (
		SELECT DISTINCT
			`id` AS `person_id`,
			substring(`name` FROM 2 + char_length(substring_index(`name`, ' ', 1))) AS `given_names`
		FROM `student_person_20161116`
		WHERE `name` IS NOT NULL
	)
	ORDER BY `person_id`, `given_names`;

ALTER TABLE `student_given_names_value`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`person_id`);

CREATE TABLE `student_given_names_time` DEFAULT CHARSET utf8 AS
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
					substring(`name` FROM 2 + char_length(substring_index(`name`, ' ', 1))) AS `given_names`
				FROM `student_person`
			)
			UNION (
				SELECT DISTINCT
					`id`,
					`semester`,
					`year_min`,
					`year_max`,
					substring(`name` FROM 2 + char_length(substring_index(`name`, ' ', 1))) AS `given_names`
				FROM `student_person_20161116`
			)
		) AS `s`
		JOIN `student_given_names_value` AS `v` ON `v`.`person_id` = `s`.`merged_id` AND `v`.`given_names` = `s`.`given_names`
	ORDER BY `s`.`merged_id`, `s`.`given_names`, `s`.`semester`;

ALTER TABLE `student_given_names_time`
ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
ADD INDEX (`value_id`);
