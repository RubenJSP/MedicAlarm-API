<?php
use Auth;

function guard($user){
    return $user == Auth::user()->id;
}
