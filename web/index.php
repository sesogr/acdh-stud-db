<!DOCTYPE html>
<html lang="de-AT">
    <head>
        <meta charset="UTF-8" />
        <title>DEMOS — Suche Studenten Musikwissenschaft Wien</title>
        <style type="text/css">
            body { font-family: sans-serif; font-size: 10px; }
            main section datalist, main section datalist option { display: none; }
            main section form>div { display: table-row; }
            main section form>div>* { display: table-cell; }
            main section form>div>input,
            main section form>div>select { width: 100%; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; }
            main section form>div>input[type="checkbox"] { width: auto; }
            main section form>div>label,
            main section form>div>span { padding: 0 6px; }
        </style>
    </head>
    <body>
        <main>
            <section>
                <h1>DEMOS — Suche Studenten Musikwissenschaft Wien</h1>
                <?php
                    if (isset($_POST['action']) && $_POST['action'] === 'search'):
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
