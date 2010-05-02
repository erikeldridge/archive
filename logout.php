<?php
setcookie( 'local_user_id', '', time() - 3600 );
header( "Location: index.php" );
?>