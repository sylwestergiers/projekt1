<!DOCTYPE html>

<html>
    <head>
        <title>Algorytm A*</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/style.css">
        <script src="js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="js/script.js"></script>
    </head>
    <body>
        <main>
            <div class="content-container">
                <h1>Algorytm A*</h1>
                <h3>Wybierz punkt startowy (kolor zielony) i punkt końcowy (kolor czerwony).</h3>
                <h3>Nastepnie zaznacz punkty przezkody (kolor czarny).</h3>
                <div class="board board1">
                    <?php for($i = 1; $i < 21; $i++) : ?>
                        <div class="row row<?= $i ?>">
                            <?php for($j = 1; $j < 21; $j++) : ?>
                                <div class="cell cell-<?= $i ?>" data-cell-id="<?= $i ?>-<?= $j ?>" data-cell-type=0>
                                    <span><?= $i."-".$j ?></span>
                                </div>
                            <?php endfor; ?>
                            <div class="clearfix"></div>
                        </div>
                    <?php endfor; ?>
                <div class="board-finish">
                    <div id="box-video">
                        <!--
                        <iframe width="600" height="400" src="https://www.youtube.com/embed/a-BgREkkjcg?autoplay=1" frameborder="0" autoplay="true" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                        -->
                    </div>
                </div>
                </div>
                <div id="init-btn">Znajdź drogę</div>
            </div>
        </main>
    </body>
</html>
