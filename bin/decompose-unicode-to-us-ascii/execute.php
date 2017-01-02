<?php
    require_once __DIR__ . '/../../web/src/credentials.php';
    require_once __DIR__ . '/../../web/src/UnicodeString.php';
    $pdo = new PDO('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', 'rksd', 'nJkyj2pOsfUi', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    $pdo->exec('SET NAMES utf8');
    $listLastNames = $pdo->query("SELECT DISTINCT last_name FROM `student_last_name_value` WHERE `last_name` NOT REGEXP '^[a-z ]+\$'");
    $listGivenNames = $pdo->query("SELECT DISTINCT given_names FROM `student_given_names_value` WHERE `given_names` NOT REGEXP '^[a-z ]+$'");
    $listLecturers = $pdo->query("SELECT DISTINCT lecturer FROM `student_attendance` WHERE `lecturer` NOT REGEXP '^[a-z,. ]+$'");
    $listLastNames->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listGivenNames->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listLecturers->setFetchMode(PDO::FETCH_COLUMN, 0);
    $templateLastName = "UPDATE `student_last_name_value` SET `ascii_last_name` = nullif('%s', '') WHERE `last_name` = '%s'\n";
    $templateGivenNames = "UPDATE `student_given_names_value` SET `ascii_given_names` = nullif('%s', '') WHERE `given_names` = '%s'\n";
    $templateLecturer = "UPDATE `student_attendance` SET `ascii_lecturer` = nullif('%s', '') WHERE `lecturer` = '%s'\n";
//    $updateLastName = $pdo->prepare("UPDATE `student_last_name_value` SET `ascii_last_name` = nullif(:ascii_name, '') WHERE `last_name` = :name");
//    $updateGivenNames = $pdo->prepare("UPDATE `student_given_names_value` SET `ascii_given_names` = nullif(:ascii_name, '') WHERE `given_names` = :name");
//    $updateLecturer = $pdo->prepare("UPDATE `student_attendance` SET `ascii_lecturer` = nullif(:ascii_name, '') WHERE `lecturer` = :name");
    $unicodeString = new UnicodeString();
    $unicodeSpace = new UnicodeString(0x20);
    foreach ($listLastNames as $name) {
        $normalizedName = str_replace(['(', ')', '[', ']', '{', '}', '<', '>'], '', $name);
        $normalizedName = preg_replace('/[^a-z\\x80-\\xff]+/i', ' ', $normalizedName);
        $unicodeString->loadUtf8String(trim($normalizedName));
        $asciiName = $unicodeString
            ->decompose(true)
            ->filter(null, [UnicodeString::LETTER])
            ->toLowerCase()
            ->saveUtf8String();
//        $updateLastName->execute(['name' => $name, 'ascii_name' => $asciiName]);
        printf($templateLastName, $asciiName, $name);
    }
    foreach ($listGivenNames as $name) {
        $normalizedName = str_replace(['(', ')', '[', ']', '{', '}', '<', '>'], '', $name);
        $normalizedName = preg_replace('/[^a-z\\x80-\\xff]+/i', ' ', $normalizedName);
        $unicodeString->loadUtf8String(trim($normalizedName));
        $asciiName = $unicodeString
            ->decompose(true)
            ->filter($unicodeSpace, [UnicodeString::LETTER])
            ->toLowerCase()
            ->saveUtf8String();
//        $updateGivenNames->execute(['name' => $name, 'ascii_name' => $asciiName]);
        printf($templateGivenNames, $asciiName, $name);
    }
    foreach ($listLecturers as $name) {
        $normalizedName = str_replace(['(', ')', '[', ']', '{', '}', '<', '>'], '', $name);
        $normalizedName = preg_replace('/[^,a-z\\x80-\\xff]+/i', ' ', $normalizedName);
        $unicodeString->loadUtf8String(trim($normalizedName));
        $asciiName = $unicodeString
            ->decompose(true)
            ->filter($unicodeSpace, [UnicodeString::LETTER, UnicodeString::PUNCTUATION_OTHER])
            ->toLowerCase()
            ->saveUtf8String();
//        $updateLecturer->execute(['name' => $name, 'ascii_name' => $asciiName]);
        printf($templateLecturer, $asciiName, $name);
    }
