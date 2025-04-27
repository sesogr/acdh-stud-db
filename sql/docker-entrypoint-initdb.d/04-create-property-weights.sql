drop table if exists student_similarity_weight;
create table student_similarity_weight
(
    property enum (
        'birth_place',
        'father',
        'given_names',
        'graduation',
        'guardian',
        'last_name',
        'last_school',
        'studying_address',
        'birth_date',
        'birth_range'
        ) primary key        not null,
    weight   double unsigned not null default 0
);