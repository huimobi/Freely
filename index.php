<?php
declare(strict_types=1);

require_once __DIR__ . '/templates/common.tpl.php';  
require_once __DIR__ . '/templates/category.tpl.php';  
require_once __DIR__ . '/templates/top_rated.tpl.php';  
?>

<?php drawHeader(); ?>  
<?php drawCategoryList(); ?>
<?php drawTopRatedBlock(); ?>
<?php drawFooter(); ?>
