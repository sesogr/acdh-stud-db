REPLACE INTO `student_identity` (`person_id`, `year_min`, `year_max`)
	SELECT
		`person_id`,
		least(
			`si`.`year_min`,
			min(
				if(
					substring(`sa`.`semester_abs`, 1, 1) = 'W',
					substring(`sa`.`semester_abs`, 3, 4) - 0,
					if(
						substring(`sa`.`semester_abs`, 1, 1) = 'S',
						substring(`sa`.`semester_abs`, 3, 4) - 0,
						9999
					)
				)
			)
		),
		greatest(
			`si`.`year_max`,
			max(
				if(
					substring(`sa`.`semester_abs`, 1, 1) = 'W',
					substring(`sa`.`semester_abs`, 3, 4) + 1,
					if(
						substring(`sa`.`semester_abs`, 1, 1) = 'S',
						substring(`sa`.`semester_abs`, 3, 4) - 0,
						0
					)
				)
			)
		)
	FROM `student_attendance` `sa`
		JOIN `student_identity` `si` USING (`person_id`)
	GROUP BY `person_id`;
