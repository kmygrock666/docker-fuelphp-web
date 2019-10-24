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
                        echo '<li class="nav-item nsearch '.$list['active'].'" ><a class="nav-link" href="'.$list['href'].'" onClick=touch("'.$list['url'].'")>'.Lang::get($list['title']).'</a></li>';
                    }
                ?>
            </ul>
            <div class="navbar-brand">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo Lang::get($lang); ?></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown01">
                            <a class="dropdown-item" href="#" onclick="langChange('?lang=en')"><?php echo Lang::get('message.EN'); ?></a>
                            <a class="dropdown-item" href="#" onclick="langChange('?lang=tw')"><?php echo Lang::get('message.TW'); ?></a>
                            <a class="dropdown-item" href="#" onclick="langChange('?lang=cn')"><?php echo Lang::get('message.CN'); ?></a>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="navbar-brand">
                <span><?php echo Lang::get('message.ACCOUNT'); ?></span>
                <span><?php echo $username; ?></span>
            </div>
            <div class="navbar-brand">
                <span><?php echo Lang::get('message.BALANCE'); ?></span>
                <span id="balance"><?php echo $amount; ?></span>
            </div>
            <div class="navbar-brand">
                <a href="/user/logout"><span><?php echo Lang::get('message.LOGOUT'); ?></span></a>
            </div>
        </div>
    </nav>
    <!-- container -->
    <main role="main" class="container">
        <div id="container">
            <?php echo empty($url)? $content : ''; ?>
        </div>
        <div class="modal fade" id="loadingModal" >
            <div style="width: 200px;height:20px; z-index: 20000; position: absolute; text-align: center; left: 50%; top: 50%;margin-left:-100px;margin-top:-10px">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        </div>
    </main>
    <!-- /.container -->

    <footer>
        <?php echo $footer; ?>
    </footer>
</body>

<?php echo Asset::js('require.js', array("data-main" => "assets/js/main"))?>


<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.25/moment.min.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/2.1.25/daterangepicker.min.js"></script>-->
<script>
    var url = <?php echo empty($url)? "''" : "'".$url."'";?>;
</script>

</html>