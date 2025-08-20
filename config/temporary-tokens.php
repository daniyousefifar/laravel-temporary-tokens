<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify the name of the table used to store temporary tokens.
    | This is useful if you want to avoid table name collisions.
    |
    */
    'table_name' => 'temporary_tokens',

    /*
    |--------------------------------------------------------------------------
    | Default Token Length
    |--------------------------------------------------------------------------
    |
    | This value specifies the default length for the generated numeric tokens.
    | The default is 6, which generates a 6-digit number.
    |
    */
    'default_token_length' => 6,

    /*
    |--------------------------------------------------------------------------
    | Pruning Expired Tokens
    |--------------------------------------------------------------------------
    |
    | This option defines the number of hours after which the expired tokens
    | will be pruned (deleted) from the database by the command.
    |
    */
    'prune_expired_after_hours' => 24,

];
