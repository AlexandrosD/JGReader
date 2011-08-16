<?php
/*------------------------------------------------------------------------
# com_jgreader - J! GoogleReader
# ------------------------------------------------------------------------
# @author    Alexandros D
# @copyright Copyright (C) 2011 Alexandros D. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# @Website: http://alexd.mplofa.com
-------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$app = JFactory::getApplication();
$appParams = $app->getParams();
?>

<?php if ($appParams->get('show_page_heading', 1)) : ?>
<h1><?php echo $this->escape($appParams->get('page_heading')); ?></h1>
<?php endif; ?>

<div class="greader-items">
<?php 
$counter = 0;
$alternator = 0;
foreach($this->items as $article): 
$counter++;
$alternator = $alternator-1;
$alternator = $alternator * $alternator;
?>
	<div class="greader-item-<?php echo $alternator; ?>">
		<h3 class="greader-item-title">
		<?php if ($this->params->linkedTitle): ?>
		<a href="<?php echo $article['alternate'][0]['href']; ?>" target="_blank">
		<?php endif; ?>
			<?php echo $article['title']; ?>
		<?php if ($this->params->linkedTitle): ?>
		</a>
		<?php endif; ?>
		</h3>
		<?php if ($this->params->displayDate): ?>
		<h3 class="greader-item-date"><?php echo date('M j, Y', $article['published']); ?></h3>
		<?php endif; ?>
		<?php if ( $this->params->displaySummary ): ?>
		<div class="greader-item-summary">
			<?php if(isset($article['summary']['content'])): ?>
			<span><?php echo $article['summary']['content']; ?></span>
			<?php endif; ?>
			<?php if(isset($article['content']['content'])): 
					$splitdata = split('</p>', $article['content']['content']);
			?>
			<span><?php echo $splitdata[0]; ?>[&#x2026;]</p></span></li>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
</div>
