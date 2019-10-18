<?php

/*
 * @file for functions, which can use all other classes
 * */

namespace Crmgenesis\Bolvanka;

class bitrixfunctions{

    public function logData($data){
        $file = $_SERVER["DOCUMENT_ROOT"].'/zzz.log';
        file_put_contents($file, print_r([date('d.m.Y H:i:s'),$data],true), FILE_APPEND | LOCK_EX);
    }

}