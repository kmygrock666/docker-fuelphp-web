<!doctype html>
<html lang="en">

<head>
    <?php echo $header; ?>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="#"><?php echo $title; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav mr-auto">
                <?php
                    foreach ($nav as $list)
                    {
                        echo '<li class="nav-item nsearch '.$list['active'].'" ><a class="nav-link" href="'.$list['href'].'" onClick=touch("'.$list['url'].'")>'.$list['title'].'</a></li>';
                    }
                ?>
            </ul>
            <div class="navbar-brand">
                <span>帐号: </span>
                <span><?php echo $username; ?></span>
            </div>
            <div class="navbar-brand">
                <span>余额: </span>
                <span id="balance"><?php echo $amount; ?></span>
            </div>
            <div class="navbar-brand">
                <a href="/user/logout"><span>登出</span></a>
            </div>
        </div>
    </nav>
    <!-- container -->
    <main role="main" class="container">
        <div id="container">
            <?php echo $content; ?>
        </div>
    </main>
    <!-- /.container -->

    <footer>
        <?php echo $footer; ?>
    </footer>
</body>
<?php echo Asset::js('jquery-3.4.1.js') ?>
<?php echo Asset::js('bootstrap.bundle.js') ?>
<?php echo Asset::js('bootstrap.bundle.min.js') ?>
<?php echo Asset::js('custom.js') ?>

</html>