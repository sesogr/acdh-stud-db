UPDATE `student_birth_date_value`
SET
	`born_on_or_after` = `birth_date` + INTERVAL 1 DAY,
	`born_on_or_before` = `birth_date` + INTERVAL 1 DAY,
	`birth_date` = `birth_date` + INTERVAL 1 DAY
WHERE `birth_date` IN (
	'1900-01-09',
	'1900-01-22',
	'1900-01-24',
	'1900-02-03',
	'1900-02-06',
	'1900-02-14'
);
