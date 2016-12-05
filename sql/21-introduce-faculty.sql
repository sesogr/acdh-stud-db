ALTER TABLE `student_attendance`
	ADD COLUMN `faculty` VARCHAR(255) DEFAULT NULL AFTER `class_extra`;

UPDATE `student_attendance`
SET `faculty` = 'Phil. Fak.'
WHERE `faculty` IS NULL;
