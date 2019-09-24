<!doctype html>
<html lang="en">

<head>
    <?php echo $header; ?>
</head>

<body>
    <!-- container -->
    <main role="main" class="container">
        <?php echo $content; ?>
    </main>
    <!-- /.container -->

    <footer>
        <?php echo $footer; ?>
    </footer>
</body>
<?php echo Asset::js('jquery-3.4.1.js') ?>
<?php echo Asset::js('bootstrap.bundle.js') ?>
<?php echo Asset::js('bootstrap.bundle.min.js') ?>

</html>