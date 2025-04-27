DROP VIEW v_combine_graphs;
CREATE ALGORITHM = UNDEFINED DEFINER = `rksd` @`%` SQL SECURITY DEFINER VIEW `v_combine_graphs` AS
select `student_similarity_graph`.`id_low`   AS `id_low`,
       `student_similarity_graph`.`id_high`  AS `id_high`,
       `student_similarity_graph`.`property` AS `property`,
       `student_similarity_graph`.`mean`     AS `mean`,
       `student_similarity_graph`.`median`   AS `median`,
       `student_similarity_graph`.`min`      AS `min`,
       `student_similarity_graph`.`max`      AS `max`,
       `student_similarity_graph`.`count`    AS `count`
from `student_similarity_graph`
union
select `student_similarity_graph_birth_range`.`id_low`   AS `id_low`,
       `student_similarity_graph_birth_range`.`id_high`  AS `id_high`,
       `student_similarity_graph_birth_range`.`property` AS `property`,
       `student_similarity_graph_birth_range`.`mean`     AS `mean`,
       `student_similarity_graph_birth_range`.`median`   AS `median`,
       `student_similarity_graph_birth_range`.`min`      AS `min`,
       `student_similarity_graph_birth_range`.`max`      AS `max`,
       `student_similarity_graph_birth_range`.`count`    AS `count`
from `student_similarity_graph_birth_range`