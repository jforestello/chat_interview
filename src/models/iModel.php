<?php

namespace App\models;

interface iModel {

    public function getId() : ?int;
    public function setId(int $id);
}