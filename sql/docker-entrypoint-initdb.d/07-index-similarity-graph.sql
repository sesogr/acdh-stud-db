alter table student_similarity_graph
    add key (id_low),
    add key (id_high),
    add key (property),
    add unique key (id_low, id_high, property);
