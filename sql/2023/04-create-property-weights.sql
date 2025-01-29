drop table if exists student_similarity_weight;
create table student_similarity_weight
(
    property varchar(16) primary key not null,
    weight   double unsigned         not null default 0
);