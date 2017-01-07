DROP TABLE IF EXISTS `student_identity`;

CREATE TABLE `student_identity` DEFAULT CHARSET utf8 AS
	SELECT
		`person_id`,
		min(`year_min`) AS `year_min`,
		max(`year_max`) AS `year_max`
	FROM (
			 (
				 SELECT
					 `merged_id` AS `person_id`,
					 `year_min`,
					 `year_max`
				 FROM `student_person`
			 )
			 UNION (
				 SELECT
					 `id` AS `person_id`,
					 `year_min`,
					 `year_max`
				 FROM `student_person_20161116`
			 )
		 ) `s`
	GROUP BY `person_id`;

ALTER TABLE `student_identity`
CHANGE `person_id` `person_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY;
