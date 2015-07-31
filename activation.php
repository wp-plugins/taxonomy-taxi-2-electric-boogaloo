<?php

namespace taxonomytaxi\electric_boogaloo;

/*
*	called on activation hook
*/
function activate(){
	flush_rewrite_rules( FALSE );
}

/*
*	called on deactivation hook
*/
function deactivate(){
	flush_rewrite_rules( FALSE );
}
