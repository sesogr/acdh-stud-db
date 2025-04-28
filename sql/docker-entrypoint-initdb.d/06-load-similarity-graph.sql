load data infile '/docker-entrypoint-initdb.d/20250428-student-similarity-graph.csv'
    into table student_similarity_graph (@record) set
        id_low = conv(replace(replace(substr(@record, 1, 3), '@', ''), '-', ''), 36, 10),
        id_high = conv(replace(replace(substr(@record, 4, 3), '@', ''), '-', ''), 36, 10),
        property = elt(conv(replace(substr(@record, 7, 2), '@', ''), 36, 10) % 10 + 1, 'birth_place', 'father',
                       'given_names', 'graduation', 'guardian', 'last_name', 'last_school', 'studying_address',
                       'birth_date', 'birth_range'),
        count = floor(conv(replace(substr(@record, 7, 2), '@', ''), 36, 10) / 10),
        min = conv(replace(replace(substr(@record, 9, 3), '@', ''), '-', ''), 36, 10) / 46655,
        mean = conv(replace(replace(substr(@record, if(length(@record) > 18, 12, 9), 3), '@', ''), '-', ''), 36, 10) /
               46655,
        median = conv(replace(replace(substr(@record, if(length(@record) > 18, 15, 9), 3), '@', ''), '-', ''), 36, 10) /
                 46655,
        max = conv(replace(replace(substr(@record, if(length(@record) > 18, 18, 9), 3), '@', ''), '-', ''), 36, 10) /
              46655