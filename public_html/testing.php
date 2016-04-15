<?php
header_register_callback(function(){
	;
});
echo '<pre>';
var_dump(filter_var('on',FILTER_VALIDATE_BOOLEAN,FILTER_NULL_ON_FAILURE));


