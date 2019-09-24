<?php

class Model_Period extends Orm\Model 
{
    // protected static $_table_name = 'user';

    protected static $_properties = array(
        'id',
        'pid',
        'openWin',
        'isClose',
        'created_at',
        'updated_at',
    );

}