<?php
    require_once __DIR__ . '/../../web/src/credentials.php';
    require_once __DIR__ . '/../../web/src/UnicodeString.php';
    $pdo = new PDO('mysql:host=127.0.0.1;port=13006;dbname=rksd;charset=utf8', 'rksd', 'nJkyj2pOsfUi', array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ));
    $pdo->exec('SET NAMES utf8');
    $listLastNames = $pdo->query("SELECT id, last_name FROM `student_last_name_value` WHERE `last_name` NOT REGEXP '^[a-z ]+\$'");
    $listGivenNames = $pdo->query("SELECT id, given_names FROM `student_given_names_value` WHERE `given_names` NOT REGEXP '^[a-z ]+$'");
    $listLecturers = $pdo->query("SELECT id, lecturer FROM `student_attendance` WHERE `lecturer` NOT REGEXP '^[a-z,. ]+$'");
    $listLastNames->setFetchMode(PDO::FETCH_NUM);
    $listGivenNames->setFetchMode(PDO::FETCH_NUM);
    $listLecturers->setFetchMode(PDO::FETCH_NUM);
    $updateLastName = $pdo->prepare("UPDATE `student_last_name_value` set `ascii_last_name` = :name WHERE `id` = :id");
    $updateGivenNames = $pdo->prepare("UPDATE `student_given_names_value` set `ascii_given_names` = :name WHERE `id` = :id");
    $updateLecturer = $pdo->prepare("UPDATE `student_attendance` set `ascii_lecturer` = :name WHERE `id` = :id");
    $unicodeString = new UnicodeString();
    $unicodeSpace = new UnicodeString(0x20);
    foreach ($listLastNames as $item) {
        list($id, $name) = $item;
        $unicodeString->loadUtf8String($name);
        $updateLastName->execute([
            'id' => $id,
            'name' => $unicodeString
                ->decompose(true)
                ->filter(null, [UnicodeString::LETTER])
                ->toLowerCase()
                ->saveUtf8String(),
        ]);
    }
    foreach ($listGivenNames as $item) {
        list($id, $name) = $item;
        $unicodeString->loadUtf8String($name);
        $updateGivenNames->execute([
            'id' => $id,
            'name' => $unicodeString
                ->decompose(true)
                ->filter($unicodeSpace, [UnicodeString::LETTER])
                ->toLowerCase()
                ->saveUtf8String(),
        ]);
    }
    foreach ($listLecturers as $item) {
        list($id, $name) = $item;
        $unicodeString->loadUtf8String($name);
        $updateLecturer->execute([
            'id' => $id,
            'name' => $unicodeString
                ->decompose(true)
                ->filter($unicodeSpace, [UnicodeString::LETTER])
                ->toLowerCase()
                ->saveUtf8String(),
        ]);
    }
