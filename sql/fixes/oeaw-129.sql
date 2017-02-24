DROP TABLE IF EXISTS `student_attendance`;

CREATE TABLE `student_attendance` DEFAULT CHARSET utf8 AS
	SELECT
		`person_id`,
		ifnull(`l`.`x_semester`, ifnull(ifnull(`p2`.`semester`, `p3`.`semester`), `p1`.`semester`)) `semester_abs`,
		`semester_rel`,
		ifnull(`l`.`faculty`, ifnull(`p2`.`fakultaet`, `p3`.`fakultaet`)) `faculty`,
		`lecturer`,
		`class`,
		`remarks`
	FROM (
			 (
				 SELECT DISTINCT
					 `merged_id` AS `person_id`,
					 `student_id`,
					 null `seq_no`,
					 `x_semester`,
					 `x_semester_extra` `semester_rel`,
					 `x_lecturer` `lecturer`,
					 `x_class` `class`,
					 concat_ws(';', nullif(`x_class_extra`, ''), nullif(`anmerkungen`, '')) `remarks`,
					 'Phil. Fak.' `faculty`
				 FROM `student_lecture`
			 )
			 UNION (
				 SELECT DISTINCT
					 `merged` AS `person_id`,
					 null `student_id`,
					 `id` `seq_no`,
					 `ws_ss`,
					 `semester` `semester_rel`,
					 `dozent` `lecturer`,
					 `vorlesung` `class`,
					 `anmerkung` `remarks`,
					 null
				 FROM `student_lecture_20161116`
			 )
		 ) `l`
		LEFT JOIN `student_person` `p1` ON `p1`.`student_id` = `l`.`student_id`
		LEFT JOIN `student_person_20161116` `p2` ON `p2`.`lfd_nr` = `l`.`seq_no`
		LEFT JOIN `student_person_20161116` `p3` ON `p3`.`id` = `l`.`person_id`;

ALTER TABLE `student_attendance`
	ADD COLUMN `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST,
	ADD INDEX (`person_id`),
	ADD `ascii_lecturer` VARCHAR(255) DEFAULT NULL;

UPDATE `student_attendance`
SET `ascii_lecturer` = lower(`lecturer`);

UPDATE `student_attendance` SET `ascii_lecturer` = nullif('chem labor', '') WHERE `lecturer` = '1. chem. Labor';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('chem universitatslabor', '') WHERE `lecturer` = '1. Chem. Universitätslabor';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('siegel, stohr und reininger', '') WHERE `lecturer` = '<Siegel>, Stöhr und Reininger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('chem lab', '') WHERE `lecturer` = '1. chem. Lab';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('chem lab', '') WHERE `lecturer` = '2. chem. Lab';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bittner gestrichen', '') WHERE `lecturer` = 'Bittner (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brabbee, ewald', '') WHERE `lecturer` = 'Brabbée, Ewald';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('beer gestrichen', '') WHERE `lecturer` = 'Beer (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bohm', '') WHERE `lecturer` = 'Böhm';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bagster gestrichen', '') WHERE `lecturer` = 'Bagster (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bohm gestrichen', '') WHERE `lecturer` = 'Böhm (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bormann gestrichen', '') WHERE `lecturer` = 'Bormann (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('benndorf gestrichen', '') WHERE `lecturer` = 'Benndorf (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bohm und bawerk', '') WHERE `lecturer` = 'Böhm und Bawerk';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bormann', '') WHERE `lecturer` = 'Bormann?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('arnim gestrichen', '') WHERE `lecturer` = 'Arnim (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bernatzik', '') WHERE `lecturer` = 'Bernatzik?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bohm und bawerk', '') WHERE `lecturer` = 'Böhm (und Bawerk?)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('blum', '') WHERE `lecturer` = 'Blum?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruckner, anton gestrichen', '') WHERE `lecturer` = 'Bruckner, Anton (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('buhler', '') WHERE `lecturer` = 'Bühler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('cornet gestrichen', '') WHERE `lecturer` = 'Cornet (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('claus gestrichen', '') WHERE `lecturer` = 'Claus (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('budinger', '') WHERE `lecturer` = 'Büdinger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruhl', '') WHERE `lecturer` = 'Brühl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruckner, eduard', '') WHERE `lecturer` = 'Brückner, Eduard';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruckner', '') WHERE `lecturer` = 'Brückner';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('buhler, karl', '') WHERE `lecturer` = 'Bühler, Karl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruck', '') WHERE `lecturer` = 'Brück';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruch, josef', '') WHERE `lecturer` = 'Brüch, Josef';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('buhler, charlotte', '') WHERE `lecturer` = 'Bühler, Charlotte';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brecht, w strzygowski, j lach, r', '') WHERE `lecturer` = 'Brecht, W.; Strzygowski, J.; Lach, R.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('buhler,', '') WHERE `lecturer` = 'Bühler,';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brentano gestrichen', '') WHERE `lecturer` = 'Brentano (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('budinger gestrichen', '') WHERE `lecturer` = 'Büdinger (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('diener gestrichen', '') WHERE `lecturer` = 'Diener (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brucke', '') WHERE `lecturer` = 'Brücke';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('conze gestrichen', '') WHERE `lecturer` = 'Conze (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruhl gestrichen', '') WHERE `lecturer` = 'Brühl (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('budinnger', '') WHERE `lecturer` = 'Büdinnger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brucke gestrichen', '') WHERE `lecturer` = 'Brücke (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brecht gestrichen', '') WHERE `lecturer` = 'Brecht (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruch', '') WHERE `lecturer` = 'Brüch';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('chem lab gestrichen', '') WHERE `lecturer` = 'Chem Lab (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('ducker, e', '') WHERE `lecturer` = 'Dücker, E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('doller, j', '') WHERE `lecturer` = 'Döller, J.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('doller, johannes', '') WHERE `lecturer` = 'Döller, Johannes';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('ducker, elise', '') WHERE `lecturer` = 'Dücker, Elise';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('dietz, max gestrichen', '') WHERE `lecturer` = 'Dietz, Max (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('drosch', '') WHERE `lecturer` = 'Drösch';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('doller', '') WHERE `lecturer` = 'Döller';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('dvorak gestrichen', '') WHERE `lecturer` = 'Dvorak (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('dreuer recte doelter', '') WHERE `lecturer` = 'Dreuer (recte Doelter?)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('faulmann gestrichen', '') WHERE `lecturer` = 'Faulmann (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('fellner gestrichen', '') WHERE `lecturer` = 'Fellner (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('exner gestrichen', '') WHERE `lecturer` = 'Exner (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('ettmayer gestrichen', '') WHERE `lecturer` = 'Ettmayer (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('erben gestrichen', '') WHERE `lecturer` = 'Erben (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('escherich und gross', '') WHERE `lecturer` = 'Escherich und Groß';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gradener, hermann', '') WHERE `lecturer` = 'Grädener, Hermann';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furtwangler, philipp', '') WHERE `lecturer` = 'Furtwängler, Philipp';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furtwangler', '') WHERE `lecturer` = 'Furtwängler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('froschels', '') WHERE `lecturer` = 'Fröschels';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furtwanger', '') WHERE `lecturer` = 'Furtwänger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('frosch', '') WHERE `lecturer` = 'Frösch';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gluck, heinrich', '') WHERE `lecturer` = 'Glück, Heinrich';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furtwangler, ph', '') WHERE `lecturer` = 'Furtwängler, Ph.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furtwangler, phil', '') WHERE `lecturer` = 'Furtwängler, Phil.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('froschels, e', '') WHERE `lecturer` = 'Fröschels, E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furth, otto', '') WHERE `lecturer` = 'Fürth, Otto';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('fournier gestrichen', '') WHERE `lecturer` = 'Fournier (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('glowacki', '') WHERE `lecturer` = 'Głowacki';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('glowacki gestrichen', '') WHERE `lecturer` = 'Głowacki (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gitlbauer gestrichen', '') WHERE `lecturer` = 'Gitlbauer (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gomperz gestrichen', '') WHERE `lecturer` = 'Gomperz (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('forster', '') WHERE `lecturer` = 'Förster';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('furth', '') WHERE `lecturer` = 'Fürth';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('frankel', '') WHERE `lecturer` = 'Fränkel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('franke rot durchgestrichen', '') WHERE `lecturer` = 'Franke (rot durchgestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('geiger gestrichen', '') WHERE `lecturer` = 'Geiger (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('froschel', '') WHERE `lecturer` = 'Fröschel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hanslick, eduard gestrichen', '') WHERE `lecturer` = 'Hanslick, Eduard (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunberg', '') WHERE `lecturer` = 'Grünberg';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hartmann gestrichen', '') WHERE `lecturer` = 'Hartmann (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gross', '') WHERE `lecturer` = 'Groß';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunberg, karl', '') WHERE `lecturer` = 'Grünberg, Karl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grassberger, roland', '') WHERE `lecturer` = 'Graßberger, Roland';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grassberger', '') WHERE `lecturer` = 'Graßberger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hasenohrl', '') WHERE `lecturer` = 'Hasenöhrl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunberg, k', '') WHERE `lecturer` = 'Grünberg, K.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('heine geldern, robert', '') WHERE `lecturer` = 'Heine-Geldern, Robert';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grassberger, r', '') WHERE `lecturer` = 'Graßberger, R.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gross, l', '') WHERE `lecturer` = 'Groß, L.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunwald', '') WHERE `lecturer` = 'Grünwald';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hann gestrichen', '') WHERE `lecturer` = 'Hann (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hillebrand gestrichen', '') WHERE `lecturer` = 'Hillebrand (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('haas gestrichen', '') WHERE `lecturer` = 'Haas (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('herzberg frankel', '') WHERE `lecturer` = 'Herzberg-Fränkel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunberg und pribram', '') WHERE `lecturer` = 'Grünberg und Pribram';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grassberger grassberger', '') WHERE `lecturer` = 'Graßberger (Grassberger)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('gross, w', '') WHERE `lecturer` = 'Groß, W.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('haberlandt gestrichen', '') WHERE `lecturer` = 'Haberlandt (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grossmann', '') WHERE `lecturer` = 'Großmann';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hofler', '') WHERE `lecturer` = 'Höfler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hofler, alois', '') WHERE `lecturer` = 'Höfler, Alois';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('husing', '') WHERE `lecturer` = 'Hüsing';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('husing, georg', '') WHERE `lecturer` = 'Hüsing, Georg';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jager, gustav', '') WHERE `lecturer` = 'Jäger, Gustav';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hofler gestrichen', '') WHERE `lecturer` = 'Höfler (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jager', '') WHERE `lecturer` = 'Jäger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jager, g', '') WHERE `lecturer` = 'Jäger, G.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hoffer gestrichen', '') WHERE `lecturer` = 'Hoffer (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jagic', '') WHERE `lecturer` = 'Jagić';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hirschfeld gestrichen', '') WHERE `lecturer` = 'Hirschfeld (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('inama sternegg', '') WHERE `lecturer` = 'Inama-Sternegg';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jirecek', '') WHERE `lecturer` = 'Jireček';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jerusalem gestrichen', '') WHERE `lecturer` = 'Jerusalem (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jirecek gestrichen', '') WHERE `lecturer` = 'Jirecek (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jellinek arnold', '') WHERE `lecturer` = 'Jellinek-Arnold';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jagic gestrichen', '') WHERE `lecturer` = 'Jagic (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('hosing', '') WHERE `lecturer` = 'Hösing';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('knaffl lenz, erich', '') WHERE `lecturer` = 'Knaffl-Lenz, Erich';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('knaffl lenz', '') WHERE `lecturer` = 'Knaffl-Lenz';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('knaffl lenz, e', '') WHERE `lecturer` = 'Knaffl-Lenz, E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('konigsberger', '') WHERE `lecturer` = 'Königsberger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kohn gestrichen', '') WHERE `lecturer` = 'Kohn (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('juger', '') WHERE `lecturer` = 'Jüger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kolzer', '') WHERE `lecturer` = 'Kölzer?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jodl gestrichen', '') WHERE `lecturer` = 'Jodl (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jors', '') WHERE `lecturer` = 'Jörs';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kraft gestrichen', '') WHERE `lecturer` = 'Kraft (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kraus brecht', '') WHERE `lecturer` = 'Kraus-Brecht';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('klein kreibig', '') WHERE `lecturer` = 'Klein-Kreibig';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jurenka gestrichen', '') WHERE `lecturer` = 'Jurenka (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kieb', '') WHERE `lecturer` = 'Kieb?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kammerer gestrichen', '') WHERE `lecturer` = 'Kammerer (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kostler', '') WHERE `lecturer` = 'Köstler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kramsall gestrichen', '') WHERE `lecturer` = 'Kramsall (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kuhnert', '') WHERE `lecturer` = 'Kühnert';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kuchler, walther', '') WHERE `lecturer` = 'Küchler, Walther';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('maddalena gestrichen', '') WHERE `lecturer` = 'Maddalena (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('meyer lybke', '') WHERE `lecturer` = 'Meyer-Lybke';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('lowy, emanuel', '') WHERE `lecturer` = 'Löwy, Emanuel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('lowy', '') WHERE `lecturer` = 'Löwy';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('meyer lubke', '') WHERE `lecturer` = 'Meyer-Lübke';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('loffler, alexander', '') WHERE `lecturer` = 'Löffler, Alexander';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('loffler, a', '') WHERE `lecturer` = 'Löffler, A.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('meister, r much, r kuchler, w luick, k eibl, h dopsch a srbik, h spann, o brecht, w', '') WHERE `lecturer` = 'Meister, R.; Much, R.; Küchler, W.; Luick, K.; Eibl, H.; Dopsch A.; Srbik, H.; Spann, O.; Brecht, W.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('meister, r much, rudolf kuchler, walther luick, k eibl, h dopsch a srbik, h spann, o brecht, w', '') WHERE `lecturer` = 'Meister, R.; Much, Rudolf; Küchler, Walther; Luick, K.; Eibl, H.; Dopsch A.; Srbik, H.; Spann, O.; Brecht, W.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('meister, richard much, r kuchler, w luick, k eibl, hans dopsch a srbik, h spann, o brecht, w', '') WHERE `lecturer` = 'Meister, Richard; Much, R.; Küchler, W.; Luick, K.; Eibl, Hans; Dopsch A.; Srbik, H.; Spann, O.; Brecht, W.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('lieben gestrichen', '') WHERE `lecturer` = 'Lieben (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mathieu gestrichen', '') WHERE `lecturer` = 'Mathieu (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('lang gestrichen', '') WHERE `lecturer` = 'Lang (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('miklosich gestrichen', '') WHERE `lecturer` = 'Miklosich (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mencik', '') WHERE `lecturer` = 'Menčik';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('loffler', '') WHERE `lecturer` = 'Löffler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mack wirtinger', '') WHERE `lecturer` = 'Mack (Wirtinger)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mullner', '') WHERE `lecturer` = 'Müllner';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muhlbacher', '') WHERE `lecturer` = 'Mühlbacher';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller', '') WHERE `lecturer` = 'Müller';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mossler, gustav', '') WHERE `lecturer` = 'Moßler, Gustav';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mossler, g', '') WHERE `lecturer` = 'Moßler, G.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('minor gestrichen', '') WHERE `lecturer` = 'Minor (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller gestrichen', '') WHERE `lecturer` = 'Müller (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('moser gestrichen', '') WHERE `lecturer` = 'Moser (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('neminar gestrichen', '') WHERE `lecturer` = 'Neminar (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mussafia gestrichen', '') WHERE `lecturer` = 'Mussafia (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('neumayr gestrichen', '') WHERE `lecturer` = 'Neumayr (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mullner, l', '') WHERE `lecturer` = 'Müllner, L.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mossler', '') WHERE `lecturer` = 'Moßler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mullner, joh', '') WHERE `lecturer` = 'Müllner, Joh.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller, d h', '') WHERE `lecturer` = 'Müller, D. H.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller, h', '') WHERE `lecturer` = 'Müller, H.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mullner gestrichen', '') WHERE `lecturer` = 'Müllner (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller, l', '') WHERE `lecturer` = 'Müller, L.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('poch', '') WHERE `lecturer` = 'Pöch';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('poch, rudolf', '') WHERE `lecturer` = 'Pöch, Rudolf';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('potzl, otto', '') WHERE `lecturer` = 'Pötzl, Otto';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('poch schurer, helene', '') WHERE `lecturer` = 'Pöch-Schürer, Helene';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('payer thurn, rudolf', '') WHERE `lecturer` = 'Payer-Thurn, Rudolf';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('pribram gestrichen', '') WHERE `lecturer` = 'Pribram (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('polej gestrichen', '') WHERE `lecturer` = 'Polej (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('potzel', '') WHERE `lecturer` = 'Pötzel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('pokorny gestrichen', '') WHERE `lecturer` = 'Pokorny (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('nullner', '') WHERE `lecturer` = 'Nüllner';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('potsch', '') WHERE `lecturer` = 'Pötsch';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('poch gestrichen', '') WHERE `lecturer` = 'Pöch (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('robert, andre', '') WHERE `lecturer` = 'Robert, André';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('rock, friedrich', '') WHERE `lecturer` = 'Röck, Friedrich';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('riegl gestrichen', '') WHERE `lecturer` = 'Riegl (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('sauer gestrichen', '') WHERE `lecturer` = 'Sauer (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('rietsch, heinrich gestrichen', '') WHERE `lecturer` = 'Rietsch, Heinrich (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('riegl gestrichen', '') WHERE `lecturer` = 'Riegl (gestrichen?)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('richter, e gestrichen', '') WHERE `lecturer` = 'Richter, E. (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('reisch gestrichen', '') WHERE `lecturer` = 'Reisch (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('rossmat', '') WHERE `lecturer` = 'Roßmat';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schacherl rot durchgestrichen', '') WHERE `lecturer` = 'Schacherl (rot durchgestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('reininger gestrichen', '') WHERE `lecturer` = 'Reininger (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('reininger und stohr', '') WHERE `lecturer` = 'Reininger und Stöhr';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr', '') WHERE `lecturer` = 'Stöhr';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schmied kowarzik, walther', '') WHERE `lecturer` = 'Schmied-Kowarzik, Walther';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr, adolf', '') WHERE `lecturer` = 'Stöhr, Adolf';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schrodinger', '') WHERE `lecturer` = 'Schrödinger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('seemuller', '') WHERE `lecturer` = 'Seemüller';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schmied kowarzik', '') WHERE `lecturer` = 'Schmied-Kowarzik';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('seemuller, josef', '') WHERE `lecturer` = 'Seemüller, Josef';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schlogl', '') WHERE `lecturer` = 'Schlögl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('spath', '') WHERE `lecturer` = 'Späth';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('spath, ernst', '') WHERE `lecturer` = 'Späth, Ernst';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schutz, julius', '') WHERE `lecturer` = 'Schütz, Julius';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr gestrichen', '') WHERE `lecturer` = 'Stöhr (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schrodinger,', '') WHERE `lecturer` = 'Schrödinger,';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schlick, m thirring, h klein, g oppenheim, s suess, f e bruckner, e wettstein, r abel, o versluys, j', '') WHERE `lecturer` = 'Schlick, M.; Thirring, H.; Klein, G.; Oppenheim, S.; Sueß, F. E.; Brückner, E.; Wettstein, R.; Abel, O.; Versluys, J.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schlogl, nivard', '') WHERE `lecturer` = 'Schlögl, Nivard';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('spath, e', '') WHERE `lecturer` = 'Späth, E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schroder', '') WHERE `lecturer` = 'Schröder';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schroer', '') WHERE `lecturer` = 'Schröer';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schmidt gestrichen', '') WHERE `lecturer` = 'Schmidt (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('simony gestrichen', '') WHERE `lecturer` = 'Simony (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('seemuller gestrichen', '') WHERE `lecturer` = 'Seemüller (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schipper gestrichen', '') WHERE `lecturer` = 'Schipper (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('sklenar', '') WHERE `lecturer` = 'Sklenař';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schreiber gestrichen', '') WHERE `lecturer` = 'Schreiber (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schuster gestrichen', '') WHERE `lecturer` = 'Schuster (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr und reininger', '') WHERE `lecturer` = 'Stöhr und Reininger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr und siegel', '') WHERE `lecturer` = 'Stöhr und Siegel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schlosser gestrichen', '') WHERE `lecturer` = 'Schlosser (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('siegel und stohr', '') WHERE `lecturer` = 'Siegel und Stöhr';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('solder', '') WHERE `lecturer` = 'Sölder';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('siegel, stohr und reininger', '') WHERE `lecturer` = 'Siegel, Stöhr und Reininger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schied kowarzik', '') WHERE `lecturer` = 'Schied-Kowarzik';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schrotter', '') WHERE `lecturer` = 'Schrötter';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('seemuller und minor', '') WHERE `lecturer` = 'Seemüller und Minor';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('soffler', '') WHERE `lecturer` = 'Söffler';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schuller', '') WHERE `lecturer` = 'Schüller';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('siegel, stohr und reininger', '') WHERE `lecturer` = 'Siegel, Stöhr und <Reininger>';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schwiedland', '') WHERE `lecturer` = 'Schwiedland?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, franz e ,', '') WHERE `lecturer` = 'Sueß, Franz E.,';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess', '') WHERE `lecturer` = 'Sueß';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('vortragender', '') WHERE `lecturer` = 'Vortragender?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, franz e', '') WHERE `lecturer` = 'Sueß, Franz E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, f e', '') WHERE `lecturer` = 'Sueß, F. E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('toyama koichi koiclu', '') WHERE `lecturer` = 'Toyama Koichi (Koiclu)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('volker, k', '') WHERE `lecturer` = 'Völker, K.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, f e bruckner, e vierhapper, f abel, o reche, o menghin, o much, r dopsch, a', '') WHERE `lecturer` = 'Sueß, F. E.; Brückner, E.; Vierhapper, F.; Abel, O.; Reche, O.; Menghin, O.; Much, R.; Dopsch, A.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, franz ed', '') WHERE `lecturer` = 'Sueß, Franz Ed.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, franz ed bruckner, e vierhapper, f abel, o reche, o menghin, oswald much, r dopsch, a much, r', '') WHERE `lecturer` = 'Sueß, Franz Ed.; Brückner, E.; Vierhapper, F.; Abel, O.; Reche, O.; Menghin, Oswald; Much, R.; Dopsch, A.; Much, R.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess gestrichen', '') WHERE `lecturer` = 'Suess (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('tomaschek gestrichen', '') WHERE `lecturer` = 'Tomaschek (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('strzygowsky gestrichen', '') WHERE `lecturer` = 'Strzygowsky (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('vondrak', '') WHERE `lecturer` = 'Vondrák';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('toply', '') WHERE `lecturer` = 'Töply';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr, reininger und siegel', '') WHERE `lecturer` = 'Stöhr, Reininger und Siegel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('uhlig und suess', '') WHERE `lecturer` = 'Uhlig und Sueß';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr, siegel und reininger', '') WHERE `lecturer` = 'Stöhr, Siegel und Reininger';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('uhlig, diener, reyer, fuchs, arthaber, suess, kossmath und abel', '') WHERE `lecturer` = 'Uhlig, Diener, Reyer, Fuchs, Arthaber, Sueß, Koßmath und Abel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('uhlig, diener, reyer, suess, arthaber, kossmath und abel', '') WHERE `lecturer` = 'Uhlig, Diener, Reyer, Sueß, Arthaber, Koßmath und Abel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('strzygowski und gluck', '') WHERE `lecturer` = 'Strzygowski und Glück';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, f', '') WHERE `lecturer` = 'Sueß, F.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess, e', '') WHERE `lecturer` = 'Sueß, E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suess', '') WHERE `lecturer` = 'Süess';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stohr, siegel und reininger', '') WHERE `lecturer` = 'Stöhr, Siegel und <Reininger>';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('suida gestrichen', '') WHERE `lecturer` = 'Suida (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stooss', '') WHERE `lecturer` = 'Stooß';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('weiss', '') WHERE `lecturer` = 'Weiß';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('weissenhofer, ans', '') WHERE `lecturer` = 'Weißenhofer, Ans.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('weissenberger, g', '') WHERE `lecturer` = 'Weißenberger, G.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wahner', '') WHERE `lecturer` = 'Wähner';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('weiss gestrichen', '') WHERE `lecturer` = 'Weiß (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('weinwurm, rudolph gestrichen', '') WHERE `lecturer` = 'Weinwurm, Rudolph (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wegscheider gestrichen', '') WHERE `lecturer` = 'Wegscheider (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brucke, ernst', '') WHERE `lecturer` = 'Brücke, Ernst';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('jager, albert', '') WHERE `lecturer` = 'Jäger, Albert';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('barach rappaport, sigmund', '') WHERE `lecturer` = 'Barach-Rappaport, Sigmund';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruhl, carl', '') WHERE `lecturer` = 'Brühl, Carl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('rosler, eduard', '') WHERE `lecturer` = 'Rösler, Eduard';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller, friedrich', '') WHERE `lecturer` = 'Müller, Friedrich';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bohm, carl', '') WHERE `lecturer` = 'Böhm, Carl';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kremer auenrode, hugo ritter von', '') WHERE `lecturer` = 'Kremer-Auenrode, Hugo Ritter von';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('lutzow, carl von', '') WHERE `lecturer` = 'Lützow, Carl von';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wiessner, e', '') WHERE `lecturer` = 'Wießner, E.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wiessner, edmund', '') WHERE `lecturer` = 'Wießner, Edmund';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wiessner, k', '') WHERE `lecturer` = 'Wießner, K.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('zeissberg', '') WHERE `lecturer` = 'Zeißberg';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('zimmermann gestrichen', '') WHERE `lecturer` = 'Zimmermann (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wickhoff gestrichen', '') WHERE `lecturer` = 'Wickhoff (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wiesner gestrichen', '') WHERE `lecturer` = 'Wiesner (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('zeissberg gestrichen', '') WHERE `lecturer` = 'Zeißberg (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wer', '') WHERE `lecturer` = 'wer?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('wettstein gestrichen', '') WHERE `lecturer` = 'Wettstein (gestrichen)';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('zeundorf', '') WHERE `lecturer` = 'Zeundorf?';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('budinger, max', '') WHERE `lecturer` = 'Büdinger, Max';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunhut, carl samuel', '') WHERE `lecturer` = 'Grünhut, Carl Samuel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('inama sternegg, theodor von', '') WHERE `lecturer` = 'Inama-Sternegg, Theodor von';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('stork, felix', '') WHERE `lecturer` = 'Störk, Felix';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schgaffle, albert', '') WHERE `lecturer` = 'Schgäffle, Albert';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('grunhut, samuel', '') WHERE `lecturer` = 'Grünhut, Samuel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schroer, arnold', '') WHERE `lecturer` = 'Schröer, Arnold';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('neumann spallart, franz xaver ritter von', '') WHERE `lecturer` = 'Neumann-Spallart, Franz Xaver Ritter von';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruhl, carl samuel', '') WHERE `lecturer` = 'Brühl, Carl Samuel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bohm, joseph', '') WHERE `lecturer` = 'Böhm, Joseph';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('kurschner, franz', '') WHERE `lecturer` = 'Kürschner, Franz';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('buhler, georg', '') WHERE `lecturer` = 'Bühler, Georg';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruhl, carl b', '') WHERE `lecturer` = 'Brühl, Carl B.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('schaffle, albert', '') WHERE `lecturer` = 'Schäffle, Albert';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('vahlen, johann hoffmann, emanuel', '') WHERE `lecturer` = 'Vahlen, Johann/Hoffmann, Emanuel';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('mullner, laurenz', '') WHERE `lecturer` = 'Müllner, Laurenz';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('brucke, ernst ritter von', '') WHERE `lecturer` = 'Brücke, Ernst Ritter von';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('bruhl, karl b', '') WHERE `lecturer` = 'Brühl, Karl B.';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('muller, heinrich', '') WHERE `lecturer` = 'Müller, Heinrich';
UPDATE `student_attendance` SET `ascii_lecturer` = nullif('', '') WHERE `lecturer` = '?';
