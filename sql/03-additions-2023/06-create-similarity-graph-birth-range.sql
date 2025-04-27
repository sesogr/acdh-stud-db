drop table if exists student_similarity_graph_birth_range;
create table student_similarity_graph_birth_range
(
    id_low   bigint unsigned not null,
    id_high  bigint unsigned not null,
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
        )                    not null,
    mean     double unsigned not null default 0,
    median   double unsigned not null default 0,
    min      double unsigned not null default 0,
    max      double unsigned not null default 0,
    count    int unsigned    not null default 0,
    key (id_low),
    key (id_high),
    key (property),
    unique key (id_low, id_high, property)
)