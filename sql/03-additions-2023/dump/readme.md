# The pseudo-binary dump from 2025-03-10

## Export

The file [20250310-student-similarity-graph.csv](20250310-student-similarity-graph.csv) was created by exporting the
result of the following query into a CSV file.

```MariaDB
select lower(
               concat(
                       lpad(conv(id_low, 10, 36), 3, '@-'),
                       lpad(conv(id_high, 10, 36), 3, '@-'),
                       lpad(
                               conv(
                                       find_in_set(
                                               property,
                                               'birth_place,father,given_names,graduation,guardian,last_name,last_school,studying_address,birth_date,birthrange'
                                       ) - 1 + 10 * count,
                                       10,
                                       36
                               ),
                               2,
                               '@'
                       ),
                       lpad(conv(floor(46655 * min), 10, 36), 3, '@-'),
                       if(
                               max > min,
                               concat(
                                       lpad(conv(floor(46655 * mean), 10, 36), 3, '@-'),
                                       lpad(conv(floor(46655 * median), 10, 36), 3, '@-'),
                                       lpad(conv(floor(46655 * max), 10, 36), 3, '@-')
                               )
                           ,
                               ''
                       )
               )
       )
from student_similarity_graph
where max > 0
```

From the 203725338 records in this table we ignore all those where max = 0, so we only have to export 123007711 records.
Using `find_in_set` we convert the property to a number. The `mean`, `median` and `max` values are only included in the
output when they are greater than `min`.

Finally all those numbers are converted to base 36 (0..9 then a..z) with padding to reduce the file size, while still
being ASCII compatible for easier file exchange. This export took about 12 minutes and created a 1.5GiB file.

## Adding to Github repo

A 1.5 GiB file exceeds the maximum file size allowed for Github repos, so we used
`split -dC80M 20250310-student-similarity-graph.csv 20250310-student-similarity-graph-`
to chop it down into 19 files of 80MiB and committed those. To restore the original file, you can use

```bash
cat 20250310-student-similarity-graph-* > 20250310-student-similarity-graph.csv
```

## Import

With the indices and the unique key removed, the import with the following statement took 13 minutes.

```MariaDB
load data local infile './20250310-student-similarity-graph.csv'
    into table student_similarity_graph (@record) set
        id_low = conv(replace(replace(substr(@record, 1, 3), '@', ''), '-', ''), 36, 10),
        id_high = conv(replace(replace(substr(@record, 4, 3), '@', ''), '-', ''), 36, 10),
        property = elt(conv(replace(substr(@record, 7, 2), '@', ''), 36, 10) % 10 + 1, 'birth_place', 'father',
                       'given_names', 'graduation', 'guardian', 'last_name', 'last_school', 'studying_address',
                       'birth_date', 'birthrange'),
        count = floor(conv(replace(substr(@record, 7, 2), '@', ''), 36, 10) / 10),
        min = conv(replace(replace(substr(@record, 9, 3), '@', ''), '-', ''), 36, 10) / 46655,
        mean = conv(replace(replace(substr(@record, if(length(@record) > 18, 12, 9), 3), '@', ''), '-', ''), 36, 10) /
               46655,
        median = conv(replace(replace(substr(@record, if(length(@record) > 18, 15, 9), 3), '@', ''), '-', ''), 36, 10) /
                 46655,
        max = conv(replace(replace(substr(@record, if(length(@record) > 18, 18, 9), 3), '@', ''), '-', ''), 36, 10) /
              46655;
```

The problem was, that adding the keys after that aborts with "table is full" errors.

With enough temp space for the Docker container and with the indices present from the beginning on the empty table, the
same import finished after 1h and 36min.

---
*to be continued...*