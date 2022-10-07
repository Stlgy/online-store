<?php
//echo realpath('.');
include_once 'libraries/start.php';
include_once 'helpers/session_helper.php';

?>


<!DOCTYPE html>
    <html lang="en">
        <head>
            <?php include_once "views/layouts/head.php"; ?>
            <link rel="stylesheet" href="template/css/style_log.css" type="text/css">
        </head>
            <body>
                <?php include_once "views/layouts/header.php"; ?>
                <div class="container-fluid">
                    <section>
                        <h3>Shop now</h3>
                        <table>
                            <tr>
                                <td>
                                    Products
                            </tr>
                        </table>
                    </section>
                </div>
                <?php include_once "views/layouts/footer.php"; ?>
    
            </body>
</html>
