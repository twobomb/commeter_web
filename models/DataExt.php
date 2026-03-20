<?php


namespace app\models;


class DataExt
{


    public const ROLE_USER = "user";
    public const ROLE_ADMIIN = "admin";

    public static function getRoles(){
        return [self::ROLE_USER,self::ROLE_ADMIIN];
    }
    public static function getRolesInp(){
        return [self::ROLE_USER=>"Пользователь",self::ROLE_ADMIIN=>"Администратор"];
    }

}