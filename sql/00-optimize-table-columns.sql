ALTER TABLE `student_person`
	MODIFY `_id` SMALLINT UNSIGNED NOT NULL,
	MODIFY `merged_id` SMALLINT UNSIGNED NOT NULL,
	MODIFY `student_id` SMALLINT UNSIGNED NOT NULL,
	MODIFY `year_max` SMALLINT UNSIGNED,
	MODIFY `year_min` SMALLINT UNSIGNED,
	MODIFY `born_max` DATE,
	MODIFY `born_min` DATE;

ALTER TABLE `student_person_20161116`
	MODIFY `_id` SMALLINT UNSIGNED NOT NULL,
	MODIFY `id` SMALLINT UNSIGNED NOT NULL,
	MODIFY `lfd_nr` SMALLINT UNSIGNED NOT NULL,
	MODIFY `year_max` SMALLINT UNSIGNED,
	MODIFY `year_min` SMALLINT UNSIGNED,
	MODIFY `born_max` DATE,
	MODIFY `born_min` DATE;

ALTER TABLE `student_lecture`
	MODIFY `_id` MEDIUMINT UNSIGNED NOT NULL,
	MODIFY `merged_id` SMALLINT UNSIGNED NOT NULL,
	MODIFY `student_id` SMALLINT UNSIGNED NOT NULL;

ALTER TABLE `student_lecture_20161116`
	MODIFY `_id` MEDIUMINT UNSIGNED NOT NULL,
	MODIFY `merged` SMALLINT UNSIGNED NOT NULL,
	MODIFY `id` SMALLINT UNSIGNED NOT NULL;
