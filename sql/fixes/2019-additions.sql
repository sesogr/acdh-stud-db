drop table if exists person_2019;

create table person_2019 (
    id                                                          int not null,
    is_existing_id                                              set('true') not null default '',
    lfd_nr                                                      int not null,
    semester                                                    varchar(255) default null,
    fakultaet                                                   varchar(255) default null,
    year_min                                                    smallint not null,
    year_max                                                    smallint not null,
    name                                                        varchar(255) default null,
    jahre_alt                                                   varchar(255) default null,
    geb_dat                                                     varchar(255) default null,
    is_dob_from_supplemental_sources                            set('true') not null default '',
    born_min                                                    date default null,
    born_max                                                    date default null,
    staatsbuergerschaft                                         varchar(255) default null,
    geburtsort                                                  varchar(255) default null,
    geburtsland_historisch                                      varchar(255) default null,
    geburtsland_heute                                           varchar(255) default null,
    muttersprache                                               varchar(255) default null,
    religion                                                    varchar(255) default null,
    adresse_student                                             varchar(255) default null,
    geschlecht                                                  varchar(255) default null,
    name_stand_uwohnort_dvaters                                 varchar(255) default null,
    vormund                                                     varchar(255) default null,
    zuletzt_besuchte_lehranstalt_grundlage_fuer_immatriculation varchar(255) default null,
    angaben_zur_biografie                                       varchar(255) default null,
    is_biography_from_supplemental_sources                      set('true') not null default '',
    hinweise_zur_biografie                                      varchar(255) default null,
    is_comment_from_supplemental_sources                        set('true') not null default '',
    literaturhinweise                                           varchar(255) default null,
    anmerkung                                                   text default null,
    volkszugehoerigkeit                                         varchar(255) default null
) default charset utf8;

load data local infile '/home/developer/projects/sednasoft/klugseder/rksd-2016/files/final_student_person_Jus_1897_1927_mit_ID-1.color-columns.tsv'
    into table person_2019
    charset utf8
    fields terminated by '\t' optionally enclosed by '"'
    lines terminated by '\n'
    ignore 1 lines;

drop table if exists lecture_2019;

create table lecture_2019 (
                              merged    int default null,
                              id        int default null,
                              ws_ss     varchar(255) default null,
                              semester  varchar(255) default null,
                              dozent    varchar(255) default null,
                              vorlesung varchar(255) default null,
                              anmerkung varchar(255) default null
) default charset utf8;

load data local infile '/home/developer/projects/sednasoft/klugseder/rksd-2016/files/final_student_lecture_Jus_1897_1927_mit_ID-1.tsv'
    into table lecture_2019
    charset utf8
    fields terminated by '\t' optionally enclosed by '"'
    lines terminated by '\n'
    ignore 1 lines;
