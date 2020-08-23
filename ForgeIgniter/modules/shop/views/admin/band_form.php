<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
	Shop :
	<small>Shipping Band</small>
  </h1>
  <ol class="breadcrumb">
	<li><a href="<?= site_url('admin/shop'); ?>"><i class="fa fa-shopping-cart"></i> Shop</a></li>
	<li class="active"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Band</li>
  </ol>
</section>

<!-- Main content -->
<section class="content container-fluid">
  <section class="content extra-padding">

  <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>" class="default">

	<div class="row">
		<div class="pull-left">
			<a href="<?php echo site_url('/admin/shop/bands');?>" class="btn btn-crey margin-bottom" style="margin-left: 15px;">Back to Shipping Bands</a>
		</div>
		<div class="col-md-3 pull-right">
			<input
				type="submit"
				value="Save Changes"
				class="btn btn-green margin-bottom"
				style="right:4%;position: absolute;top: 0px;"
			/>
		</div>
	</div>

	<!-- Main row -->
	<div class="row">
		<div class="box box-crey">
			<div class="box-header with-border">
				<i class="fa fa-edit"></i>
				<?php if (!$this->core->is_ajax()): ?>
				<h3 class="box-title"><?= (preg_match('/edit/i', $this->uri->segment(3))) ? 'Edit' : 'Add'; ?> Shipping Band</h3>
				<?php endif; ?>
			</div>

			<div class="box-body" style="padding:20px">

				<?php if ($errors = validation_errors()): ?>
				<div class="callout callout-danger">
					<h4>Warning!</h4>
					<?php echo $errors; ?>
				</div>
				<?php endif; ?>

				<div class="row">
				  <div class="col col-md-4" style="padding-left:30px;">

					<label for="bandName">Name:</label>
				  	<?php echo @form_input('bandName', $data['bandName'], 'class="formelement" id="bandName"'); ?>
				  	<br class="clear" />

				  	<label for="multiplier">Multiplier:</label>
				  	<?php echo @form_input('multiplier', $data['multiplier'], 'class="formelement small" id="multiplier"'); ?>
				  	<span class="price">x</span>
				  	<br class="clear" />

				  </div>
				</div>

			</div> <!-- end box body -->
		</div> <!-- end box -->
	</div> <!-- end main row -->

  </form>

  </section>
</section>
