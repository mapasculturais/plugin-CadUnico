<?php
$this->part("cadunico/header");
echo $TEMPLATE_CONTENT;
if($app->auth->isUserAuthenticated()):
$this->part("cadunico/footer");
endif
?>
