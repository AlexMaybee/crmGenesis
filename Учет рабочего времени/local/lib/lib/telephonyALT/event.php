<?php
file_put_contents(
	__DIR__."/event.log",
	var_export($_REQUEST, 1)."\n",
	FILE_APPEND
);
