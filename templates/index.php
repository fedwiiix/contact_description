<?php
script('contact_description', 'script');
script('contact_description', 'TagForm');
script('contact_description', 'UserForm');
style('contact_description', 'style');
style('contact_description', 'markdown');
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('navigation/index')); ?>
		<?php print_unescaped($this->inc('settings/index')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('content/index')); ?>
		</div>
	</div>
</div>

