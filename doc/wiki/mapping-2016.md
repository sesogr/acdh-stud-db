# Mapping of 2016 additions

## student_person.20161116.tsv

    ID                                                          => *.person_id
    lfd Nr.                                                     => X
    Semester                                                    => *_time.time
    Fakultät                                                    => student_attendance.faculty
    year min                                                    => *_time.year_min
    year max                                                    => *_time.year_max
    Name                                                        => *_value.last_name, *_value.given_names
    Geb.-Dat.                                                   => *_value.birth_date
    born min                                                    => *_value.born_on_or_after
    born max                                                    => *_value.born_before
    Staatsbürgerschaft                                          => *_value.nationality
    Geburtsort                                                  => *_value.birth_place
    Geburtsland (historisch)                                    => student_birth_place_value.birth_country_historic
    Geburtsland (heute)                                         => student_birth_place_value.birth_country_today
    Muttersprache                                               => *_value.language
    Religion                                                    => *_value.religion
    Adresse Student                                             => *_value.studying_address
    Geschlecht                                                  => *_value.gender
    Name, Stand u Wohnort d. Vaters                             => *_value.father
    Vormund                                                     => *_value.guardian
    zuletzt besuchte Lehranstalt, Grundlage für Immatriculation => *_value.last_school
    Angaben zur Biografie                                       => *_value.biography
    Hinweise zur Promotion                                      => *_value.graduation
    Literaturhinweise                                           => *_value.literature
    Anmerkung                                                   => *_value.remarks
    Volkszugehörigkeit                                          => *_value.ethnicity
    ID altrose                                                  => (merge with existing)
    Geb.-Dat. grün                                              => (alternative source)
    Angaben zur Biografie grün                                  => (alternative source)

## student_lecture.20161116.tsv

    merged    => *.person_id
    ID        => X
    WS / SS   => student_attendance.semester_abs
    Semester  => student_attendance.semester_rel
    Dozent    => student_attendance.lecturer
    Vorlesung => student_attendance.class
    Anmerkung => student_attendance.remarks
