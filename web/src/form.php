<?php
    require_once __DIR__ . '/credentials.php';
    $pdo = new PDO(MARIA_DSN, MARIA_USER, MARIA_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    $listCountries = $pdo->query('SELECT DISTINCT `geb_land` FROM `student_person` ORDER BY `geb_land`');
    $listLanguages = $pdo->query('SELECT DISTINCT `mspr` FROM `student_person` ORDER BY `mspr`');
    $listLecturers = $pdo->query('SELECT DISTINCT `lecturer` FROM `student_vorlesung` ORDER BY `lecturer`');
    $listNames = $pdo->query('SELECT DISTINCT `name` FROM `student_person` ORDER BY `name`');
    $listReligions = $pdo->query('SELECT DISTINCT `rel` FROM `student_person` ORDER BY `rel`');
    $listSemesters = $pdo->query('SELECT `semester` FROM `student_vorlesung` GROUP BY `semester_year_begin`, `semester_year_end` ORDER BY `semester_year_begin`, `semester_year_end`');
    $loadYearRange = $pdo->query(
        'SELECT min(`semester_year_begin`), max(`semester_year_end`) FROM `student_vorlesung`'
    );
    $listCountries->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listLanguages->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listLecturers->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listNames->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listReligions->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listSemesters->setFetchMode(PDO::FETCH_COLUMN, 0);
    list($minYear, $maxYear) = $loadYearRange->fetch(PDO::FETCH_NUM);
?>
<datalist id="07c18bfa-e763-45e7-af1d-360cdd70aaa5">
    <option value="*">— egal —</option>
    <?php /** @var string $option */ foreach ($listCountries as $option): ?>
        <option value="<?php echo htmlspecialchars($option) ?>"><?php echo htmlspecialchars($option ?: '— ohne —') ?></option>
    <?php endforeach ?>
</datalist>
<datalist id="f5ec22a8-b746-485e-b3ef-27cdf1cdf055">
    <option value="*">— egal —</option>
    <?php /** @var string $option */ foreach ($listLanguages as $option): ?>
        <option value="<?php echo htmlspecialchars($option) ?>"><?php echo htmlspecialchars($option ?: '— ohne —') ?></option>
    <?php endforeach ?>
</datalist>
<datalist id="ffae63a7-88e3-428b-be0c-4f2b767cb418">
    <option value="*">— egal —</option>
    <?php /** @var string $option */ foreach ($listLecturers as $option): ?>
        <option value="<?php echo htmlspecialchars($option) ?>"><?php echo htmlspecialchars($option ?: '— ohne —') ?></option>
    <?php endforeach ?>
</datalist>
<datalist id="ba2af0bc-d881-40e6-a12a-d6d33349c324">
    <option value="*">— egal —</option>
    <?php /** @var string $option */ foreach ($listNames as $option): ?>
        <option value="<?php echo htmlspecialchars($option) ?>"><?php echo htmlspecialchars($option ?: '— ohne —') ?></option>
    <?php endforeach ?>
</datalist>
<datalist id="46745bd4-89b3-43f0-b4b1-942b7d17fa62">
    <option value="*">— egal —</option>
    <?php /** @var string $option */ foreach ($listReligions as $option): ?>
        <option value="<?php echo htmlspecialchars($option) ?>"><?php echo htmlspecialchars($option ?: '— ohne —') ?></option>
    <?php endforeach ?>
</datalist>
<datalist id="38d393b0-c653-4477-a131-505748b60d9b">
    <option value="*">— egal —</option>
    <?php /** @var string $option */ foreach ($listSemesters as $option): ?>
        <option value="<?php echo htmlspecialchars($option) ?>"><?php echo htmlspecialchars($option ?: '— ohne —') ?></option>
    <?php endforeach ?>
</datalist>
<form action="" method="post">
    <div>
        <label for="ce133f40-3b4c-47dd-ac6c-b4ce4458daeb">Name</label>
        <input name="ce133f40[<?php echo uniqid() ?>]" type="text" id="ce133f40-3b4c-47dd-ac6c-b4ce4458daeb" value="*" placeholder="— ohne —" list="ba2af0bc-d881-40e6-a12a-d6d33349c324" />
    </div>
    <div>
        <label for="e95c7283-8156-4303-b0e6-c4c853a3caab">Geburtsland</label>
        <input name="e95c7283[<?php echo uniqid() ?>]" type="text" id="e95c7283-8156-4303-b0e6-c4c853a3caab" value="*" placeholder="— ohne —" list="07c18bfa-e763-45e7-af1d-360cdd70aaa5" />
    </div>
    <div>
        <label for="8cd799d0-968b-4212-b2a4-fb83c651601e">Muttersprache</label>
        <input name="8cd799d0[<?php echo uniqid() ?>]" type="text" id="8cd799d0-968b-4212-b2a4-fb83c651601e" value="*" placeholder="— ohne —" list="f5ec22a8-b746-485e-b3ef-27cdf1cdf055" />
    </div>
    <div>
        <label for="10b19606-167c-45b9-984d-0b2ed848540e">Religionszugehörigkeit</label>
        <input name="10b19606[<?php echo uniqid() ?>]" type="text" id="10b19606-167c-45b9-984d-0b2ed848540e" value="*" placeholder="— ohne —" list="46745bd4-89b3-43f0-b4b1-942b7d17fa62" />
    </div>
    <div>
        <label for="1b3abf22-fd48-4376-b9a3-499a92ec73af">Zeitraum von</label>
        <input name="1b3abf22[<?php echo uniqid() ?>]" type="number" min="<?php echo $minYear ?>" value="<?php echo $minYear ?>" max="<?php echo $maxYear ?>" id="1b3abf22-fd48-4376-b9a3-499a92ec73af" />
        <label for="5807411e-d767-4f59-84be-d94f1f14b214">bis</label>
        <input name="5807411e[<?php echo uniqid() ?>]" type="number" min="<?php echo $minYear ?>" value="<?php echo $maxYear ?>" max="<?php echo $maxYear ?>" id="5807411e-d767-4f59-84be-d94f1f14b214" />
        <label for="d0319d7c-8195-4982-880d-d438c2477e4b">
            <input name="d0319d7c[<?php echo uniqid() ?>]" type="checkbox" id="d0319d7c-8195-4982-880d-d438c2477e4b" />
            auch die ohne Jahr
        </label>
    </div>
    <div>
        <label for="0ced7cdc-8e81-47fc-8f95-22e211004a30">Semester</label>
        <input name="0ced7cdc[<?php echo uniqid() ?>]" type="text" id="0ced7cdc-8e81-47fc-8f95-22e211004a30" value="*" placeholder="— ohne —" list="38d393b0-c653-4477-a131-505748b60d9b" />
    </div>
    <div>
        <label for="f04509d6-6967-440e-bdbe-03c6631e521d">Dozent</label>
        <input name="f04509d6[<?php echo uniqid() ?>]" type="text" id="f04509d6-6967-440e-bdbe-03c6631e521d" value="*" placeholder="— ohne —" list="ffae63a7-88e3-428b-be0c-4f2b767cb418" />
    </div>
    <div>
        <label for="2068e07a-1385-481a-a23a-8fb069b40254">Sortierung nach</label>
        <select name="2068e07a[<?php echo uniqid() ?>]" id="2068e07a-1385-481a-a23a-8fb069b40254">
            <option value="name">Name</option>
            <option value="geb">Geburtsdatum</option>
            <option value="geb_land">Geburtsland</option>
            <option value="rel">Religion</option>
            <option value="mspr">Sprache</option>
            <option value="geschl">Geschlecht</option>
        </select>
        <label for="e938f5ac-0d7e-4462-b7d7-cf0db4e609c6">in</label>
        <select name="e938f5ac[<?php echo uniqid() ?>]" id="e938f5ac-0d7e-4462-b7d7-cf0db4e609c6">
            <option value="asc">aufsteigender</option>
            <option value="desc">absteigender</option>
        </select>
        <span>Reihenfolge</span>
    </div>
    <div>
        <button type="submit" name="action" value="search">Suchen</button>
        <button type="reset">Einschränkungen aufheben</button>
    </div>
</form>
