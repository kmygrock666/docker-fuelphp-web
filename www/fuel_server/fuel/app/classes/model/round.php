<?php

class Model_Round extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'openWin',
        'isWin',
        'period_id',
        'created_at',
        'updated_at',
    );
}