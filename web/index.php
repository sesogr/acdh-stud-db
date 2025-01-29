<!DOCTYPE html>
<html lang="de-AT">
    <head>
        <meta charset="UTF-8" />
        <title>DEMOS — Suche Studenten Musikwissenschaft Wien</title>
        <style>
            body { font-family: sans-serif; font-size: 10px /*max(1.6vmin, 1vmax)*/; }
            main section datalist, main section datalist option { display: none; }
            main section form>div { display: table-row; }
            main section form>div>* { display: table-cell; }
            main section form>div>input,
            main section form>div>select { width: 100%; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; }
            main section form>div>input[type="checkbox"] { width: auto; }
            main section form>div>label,
            main section form>div>span { padding: 0 6px; }
            main section ul { list-item-type: none; }
            main section ul.dupes a { text-decoration: none; font-size: 80%; font-weight: bold; padding: 2px 4px 0; border-radius: 8px; }
            main section td.orig span { font-size: 80%; font-weight: bold; padding: 2px 4px 0; border-radius: 8px; background-color: #ccc; }
            main section td.dupe span { font-size: 80%; font-weight: bold; color: #000; padding: 2px 4px 0; border-radius: 8px; }
            main section .b { background-color: #f5c0cf; } /* 12:00 000° */
            main section .c { background-color: #a0dde8; } /* 07:00 210° */
            main section .d { background-color: #f0c8a8; } /* 02:00 060° */
            main section .e { background-color: #c0d0fb; } /* 09:00 270° */
            main section .f { background-color: #cbd7a9; } /* 04:00 120° */
            main section .g { background-color: #e9c2e5; } /* 11:00 330° */
            main section .h { background-color: #a3dfd2; } /* 06:00 180° */
            main section .i { background-color: #f7c2b9; } /* 01:00 030° */
            main section .j { background-color: #acd7f6; } /* 08:00 240° */
            main section .k { background-color: #e1d0a2; } /* 03:00 090° */
            main section .l { background-color: #d7c8f5; } /* 10:00 300° */
            main section .m { background-color: #b4ddbb; } /* 05:00 150° */
            main section table { border-collapse: collapse; empty-cells: show; margin: 12px 0; }
            main section table th { vertical-align: top; text-align: left; padding: 3px; border: 1px #999 solid; }
            main section table td { padding: 1px 3px; border: 1px #999 dotted; }
            main section table td[title] { quotes: '[' ']'; }
            main section table td[title]:before { content: open-quote; color: #080; font-weight: bold; }
            main section table td[title]:after { content: close-quote; color: #080; font-weight: bold; }
            main section table td .illegible { background: url(noise.gif) center 133% no-repeat; color: transparent; background-size: cover; }
        </style>
    </head>
    <body>
        <main>
            <section>
                <h1>DEMOS — Suche Studenten Musikwissenschaft Wien</h1>
                <?php
                    if (isset($_POST['action']) && $_POST['action'] === 'search' || !empty($_GET['token'])):
                        require_once __DIR__ . '/src/search.php';
                    elseif (isset($_GET['id'])):
                        require_once __DIR__ . '/src/show.php';
                    else:
                        require_once __DIR__ . '/src/form.php';
                    endif
                ?>
            </section>
        </main>
    </body>
</html>
