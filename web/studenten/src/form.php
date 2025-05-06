<?php
    require_once(realpath($_SERVER["DOCUMENT_ROOT"]) .'/connect/verbind.php');
    $pdo = new PDO($dsndb2, $dbuser, $dbpassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  
    $pdo->exec('SET NAMES utf8');
    $listCountries = $pdo->query(
    /** @lang MySQL */
        <<<'EOD'
SELECT DISTINCT `country`
FROM (
    SELECT DISTINCT `birth_country_historic` AS `country` FROM `student_birth_place_value`
    UNION
    SELECT DISTINCT `birth_country_today` AS `country` FROM `student_birth_place_value`
) AS `temp`
ORDER BY `country`
EOD
);
    $listLanguages = $pdo->query('SELECT DISTINCT `language` FROM `student_language_value` ORDER BY `language`');
    $listLecturers = $pdo->query('SELECT DISTINCT `lecturer` FROM `student_attendance` ORDER BY `ascii_lecturer`');
    $listNames = $pdo->query('SELECT DISTINCT `last_name` FROM `student_last_name_value` ORDER BY `ascii_last_name`');
    $listReligions = $pdo->query('SELECT DISTINCT `religion` FROM `student_religion_value` ORDER BY `religion`');
    $listSemesters = $pdo->query('SELECT DISTINCT `semester_abs` FROM `student_attendance` ORDER BY REGEXP_SUBSTR(semester_abs, "[0-9]{4}"), substring_index(`semester_abs`, \' \', 1)');
    $loadYearRange = $pdo->query('SELECT min(`year_min`), max(`year_max`) FROM `student_identity`');
    $loadSemesterRange = $pdo->query(
        <<<'EOD'
        SELECT
            MIN(CAST(REGEXP_SUBSTR(semester_abs, '[0-9]{4}') AS UNSIGNED)) AS semester_begin,
            MAX(
                if(semester_abs like 'W%%', 1, 0) +
                CAST(REGEXP_SUBSTR(semester_abs, '[0-9]{4}') AS UNSIGNED)
            ) AS semester_end
        FROM student_attendance
        WHERE
            semester_abs IS NOT NULL
            AND REGEXP_SUBSTR(semester_abs, '[0-9]{4}') != ''
        EOD
    );

    $listCountries->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listLanguages->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listLecturers->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listNames->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listReligions->setFetchMode(PDO::FETCH_COLUMN, 0);
    $listSemesters->setFetchMode(PDO::FETCH_COLUMN, 0);
    list($minYear, $maxYear) = $loadYearRange->fetch(PDO::FETCH_NUM);
    list($minSemesterYear, $maxSemesterYear) = $loadSemesterRange->fetch(PDO::FETCH_NUM);
    $minYear = min($minYear, $minSemesterYear);
    $maxYear = max($maxYear, $maxSemesterYear);
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

<div class="demos-form">
 <form action="" method="post">
    <div class="row">
      <div class="col-25">
        <label for="ce133f40-3b4c-47dd-ac6c-b4ce4458daeb">Name</label>
      </div>
      <div class="col-75">
        <input name="ce133f40[<?php echo uniqid() ?>]" type="text" autocomplete="off" id="ce133f40-3b4c-47dd-ac6c-b4ce4458daeb" value="*" placeholder="— ohne —" list="ba2af0bc-d881-40e6-a12a-d6d33349c324" />
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="e95c7283-8156-4303-b0e6-c4c853a3caab">Geburtsland</label>
      </div>
      <div class="col-75">
        <input name="e95c7283[<?php echo uniqid() ?>]" type="text" autocomplete="off" id="e95c7283-8156-4303-b0e6-c4c853a3caab" value="*" placeholder="— ohne —" list="07c18bfa-e763-45e7-af1d-360cdd70aaa5" />
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="8cd799d0-968b-4212-b2a4-fb83c651601e">Muttersprache</label>
      </div>
      <div class="col-75">
        <input name="8cd799d0[<?php echo uniqid() ?>]" type="text" autocomplete="off" id="8cd799d0-968b-4212-b2a4-fb83c651601e" value="*" placeholder="— ohne —" list="f5ec22a8-b746-485e-b3ef-27cdf1cdf055" />
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="10b19606-167c-45b9-984d-0b2ed848540e">Religionszugehörigkeit</label>
      </div>
      <div class="col-75">
        <input name="10b19606[<?php echo uniqid() ?>]" type="text" autocomplete="off" id="10b19606-167c-45b9-984d-0b2ed848540e" value="*" placeholder="— ohne —" list="46745bd4-89b3-43f0-b4b1-942b7d17fa62" />
      </div>
    </div>
    <div class="row">
      <div class="col-25">
       <label for="1b3abf22-fd48-4376-b9a3-499a92ec73af">Zeitraum von</label>
      </div>
      <div class="col-75 display-flex">
         <select name="1b3abf22[<?php echo uniqid() ?>]" id="1b3abf22-fd48-4376-b9a3-499a92ec73af">
            <option selected="selected" value="<?php printf('%04d.0', $minYear) ?>">S <?php printf('%04d', $minYear) ?></option>
            <?php for ($year = $minYear; $year < $maxYear; $year++): ?>
                <option value="<?php printf('%04d.5', $year) ?>">W <?php printf('%04d/%02d', $year, ($year + 1) % 100) ?></option>
                <option value="<?php printf('%04d.0', $year + 1) ?>">S <?php printf('%04d', $year + 1) ?></option>
            <?php endfor ?>
        </select>
        <label for="5807411e-d767-4f59-84be-d94f1f14b214">bis</label>
        <select name="5807411e[<?php echo uniqid() ?>]" id="5807411e-d767-4f59-84be-d94f1f14b214">
            <?php for ($year = $minYear; $year < $maxYear; $year++): ?>
                <option value="<?php printf('%04d.0', $year) ?>">S <?php printf('%04d', $year) ?></option>
                <option value="<?php printf('%04d.5', $year) ?>">W <?php printf('%04d/%02d', $year, ($year + 1) % 100) ?></option>
            <?php endfor ?>
            <option selected="selected" value="<?php printf('%04d.0', $maxYear) ?>">S <?php printf('%04d', $maxYear) ?></option>
        </select>
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="f04509d6-6967-440e-bdbe-03c6631e521d">Dozent</label>
      </div>
      <div class="col-75">
        <input name="f04509d6[<?php echo uniqid() ?>]" type="text" autocomplete="off" id="f04509d6-6967-440e-bdbe-03c6631e521d" value="*" placeholder="— ohne —" list="ffae63a7-88e3-428b-be0c-4f2b767cb418" />
      </div>
    </div>
    <div class="row">
      <div class="col-25">
        <label for="2068e07a-1385-481a-a23a-8fb069b40254">Sortierung nach</label>
      </div>
      <div class="col-75 display-flex">
        <select name="2068e07a[<?php echo uniqid() ?>]" id="2068e07a-1385-481a-a23a-8fb069b40254">
            <option value="name">Name</option>
            <option value="birth_date">Geburtsdatum</option>
            <option value="birth_country_historic">Geburtsland (hist. Bez.)</option>
            <option value="birth_country_today">Geburtsland (heutige Bez.)</option>
            <option value="religion">Religion</option>
            <option value="language">Sprache</option>
            <option value="gender">Geschlecht</option>
        </select>
        <label for="e938f5ac-0d7e-4462-b7d7-cf0db4e609c6">in</label>
        <select name="e938f5ac[<?php echo uniqid() ?>]" id="e938f5ac-0d7e-4462-b7d7-cf0db4e609c6">
            <option value="asc">aufsteigender</option>
            <option value="desc">absteigender</option>
        </select>
        <div class="after-option">Reihenfolge</div>
      </div>
    </div>
    
    <div class="row submit-row">
    <div class="col-25"></div>
    <div class="col-75 display-flex">
      <button type="submit" name="action" value="search">Suchen</button>
      <button type="reset">Einschränkungen aufheben</button>
      </div>
    </div>
  </form>  
 </div> <!-- End Demos Form -->

<div class="columns bottom-4 custom-accordion zero-margin">       
    <div id="accordion" class="accordion style2">          
        <h4><a href="#" aria-expanded="false">Detailinformationen zu dieser Datenbank</a></h4>
        <div><p>Diese Datenbank verzeichnet die Hörer/innen musikwissenschaftlicher Vorlesungen an der Universität Wien ab der Habilitierung von <a href="//www.musiklexikon.ac.at/ml/musik_H/Hanslick_Eduard.xml" target="_blank">Eduard Hanslick</a> im Jahr 1856. </p>
        
        <p>Derzeit sind online:
            <ul class="info-list">
                <li>Inskribierte der philosophischen Fakultät: Wintersemester 1856/57 – Sommersemester 1927</li> 
                <li>Inskribierte der juristischen Fakultät: Wintersemester 1856/57 – Sommersemester 1927</li>
                <li>Inskribierte der medizinischen Fakultät: Wintersemester 1856/57 – Sommersemester 1867</li>
            </ul>
        </p>
        <p>Die vorhandenen Daten basieren auf den Nationalen im Archiv der Universität Wien. Sämtliche Angaben der jedes 
        Semester neu auszufüllenden Nationale wurden soweit als möglich übernommen respektive (wo möglich und notwendig), 
        z. B. hinsichtlich Geburtsdatum, vereinheitlicht. Bei den Dozenten aus dem Fach Musikwissenschaft ist auch der Titel der 
        betreffenden Lehrveranstaltung vermerkt, bei Dozenten aus anderen Fächern ist dies nicht flächendeckend der Fall.
        </p>
        <p>Ausgangspunkt für vorliegende Datenbank waren umfangreiche, nur zu einem geringen Teil veröffentlichte Vorarbeiten von <a href="//www.musiklexikon.ac.at/ml/musik_A/Antonicek_Theophil.xml" target="_blank">Theophil Antonicek</a>, die den Zeitraum 
        1875–1919 der philosophischen Fakultät betrafen. Von ihm stammen auch die Hinweise auf weiterführende Literatur, die bei zahlreichen Einträgen vermerkt sind.
        Auf Antoniceks Arbeiten aufbauend recherchierte Andrea Singer in den Jahren 2015 bis 2019 die Hörer/innen der philosophischen Fakultät vor 1875 bzw. nach 1919 
        sowie der beiden anderen Fakultäten wie oben angeführt.
        </p>
        <p>Literatur:
            <ul class="info-list">
                <li>Theophil Antonicek, Bruckners Universitätsschüler in den Nationalen der philosophischen Fakultät, in: 
                    Othmar Wessely (Hg.), Bruckner-Studien. Festgabe der Österreichischen Akademie der Wissenschaften zum 150. 
                    Geburtstag von Anton Bruckner (Veröffentlichungen der Kommission für Musikforschung 16). Wien 1975, S. 433–487.</li> 
                <li>Andrea Singer, Bruckner-Hörer an der Juristischen Fakultät der Universität Wien, in: Internationale BrucknerGesellschaft 
                (Hg.), Studien & Berichte. IBG Mitteilungsblatt 89 (Dezember 2017), S. 5–12.</li>
                
            </ul>
        </p>
            
        
        </div>         
    </div>
</div><!-- End column -->

<script type="text/javascript">/*<![CDATA[*/
    var inputElements = [
        document.getElementById('ce133f40-3b4c-47dd-ac6c-b4ce4458daeb'),
        document.getElementById('e95c7283-8156-4303-b0e6-c4c853a3caab'),
        document.getElementById('8cd799d0-968b-4212-b2a4-fb83c651601e'),
        document.getElementById('10b19606-167c-45b9-984d-0b2ed848540e'),
        document.getElementById('f04509d6-6967-440e-bdbe-03c6631e521d')
    ];
    inputElements.forEach(function (inputElement) {
        var removeAsterisk = false;
        inputElement.onkeydown = function () {
            if (this.value == '*') {
                removeAsterisk = true;
            }
        };
        inputElement.onkeyup = function () {
            if (removeAsterisk && this.value.length > 1) {
                this.value = this.value.replace(/^\*|\*$/g, '');
                removeAsterisk = false;
            }
        };
    });
/*]]>*/</script>