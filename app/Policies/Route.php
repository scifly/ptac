<?php
/**
 * Created by PhpStorm.
 * User: guoxl
 * Date: 2017/12/18
 * Time: 11:49
 */

namespace App\Policies;


class Route {

    public $uri;

    function __construct(string $uri) {
        $this->uri = $uri;
    }

}