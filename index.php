<?php

namespace taxonomytaxi\electric_boogaloo;

require __DIR__.'/taxo-taxi-2.php';

/*
*	called on activation hook
*/
function activate(){
	\TaxoTaxi2ElecBoog::setup();
	flush_rewrite_rules( FALSE );
}

/*
*	called on deactivation hook
*/
function deactivate(){
	flush_rewrite_rules( FALSE );
}