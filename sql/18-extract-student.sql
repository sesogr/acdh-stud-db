DROP TABLE IF EXISTS `student_identity`;

CREATE TABLE `student_identity` AS
	SELECT
		`merged_id` AS `person_id`,
		min(`year_min`) AS `year_min`,
		max(`year_max`) AS `year_max`
	FROM `student_person`
	GROUP BY `merged_id`;

ALTER TABLE `student_identity`
CHANGE `person_id` `person_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;