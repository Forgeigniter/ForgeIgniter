<?php if (!$this->core->is_ajax()): ?>
	<h1><?php echo (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Navigation</h1>
<?php endif; ?>

<?php if ($errors = validation_errors()): ?>
	<div class="error">
		<?php echo $errors; ?>
	</div>
<?php endif; ?>

<form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

		<label for="navName">Title:</label>
		<?php echo @form_input('navName', $data['navName'], 'class="formelement" id="navName"'); ?>
		<br class="clear" />

		<label for="navPath">Path:</label>
		<?php echo @form_input('uri', $data['uri'], 'class="formelement" id="uri"'); ?>
		<br class="clear" />
			
		<label for="templateID">Parent:</label>
		<?php
		if ($parents):
			$options = '';		
			$options[0] = 'Top Level';		
			foreach ($parents as $parent):
				if ($parent['navID'] != @$data['navID']) $options[$parent['navID']] = $parent['navName'];
			endforeach;
			
			echo @form_dropdown('parentID',$options,$data['parentID'],'id="parentID" class="formelement"');
		endif;
		?>	
		<br class="clear" />
		
	<input type="submit" value="Save Changes" class="button nolabel" />
	<input type="button" value="Cancel" id="cancel" class="button grey" />
	
</form>

<br class="clear" />
