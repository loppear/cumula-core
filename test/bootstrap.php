<?php

defined('BASE_DIR') ||
    define('BASE_DIR', $this->getProject()->getProperty('project.basedir'));

set_include_path(implode(PATH_SEPARATOR, array(
    get_include_path(),
    BASE_DIR,
    BASE_DIR .'/test',
)));
