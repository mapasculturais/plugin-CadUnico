<?php
$this->part("streamlinedopportunity/header");
echo $TEMPLATE_CONTENT;
if($app->auth->isUserAuthenticated()):
$this->part("streamlinedopportunity/footer");
endif
?>
