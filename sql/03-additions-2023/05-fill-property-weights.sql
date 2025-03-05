replace into student_similarity_weight (property, weight)
VALUES ('birth_place', 1 - log(100000) / 12),
       ('father', 1 - log(2) / 12),
       ('given_names', 1 - log(1000) / 12),
       ('graduation', 1 - log(1000) / 12),
       ('guardian', 1 - log(2) / 12),
       ('last_name', 1 - log(1000) / 12),
       ('last_school', 1 - log(1000) / 12),
       ('studying_address', 1 - log(10) / 12),
       ('birth_date', 1 - log(10) / 12),
       ('birthrange', 1 - log(100) / 12)