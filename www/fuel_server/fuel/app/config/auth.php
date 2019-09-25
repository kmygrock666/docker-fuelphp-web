<?php

return array(
    // 驅动程序
    'driver' => array('Ormauth'),

    // 設定为 true 以允許多个登入
    'verify_multiple_logins' => true,

    // 出於安全原因，用你自己的鹽
    'salt' => 'Th1s=mY0Wn_$@|+',

    'iterations' => 10000,
);