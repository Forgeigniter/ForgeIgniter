<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/admin.css" media="all" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/lightbox.css" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url() . $this->config->item('staticPath'); ?>/css/datepicker.css" media="screen" />
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/jquery.lightbox.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/default.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url() . $this->config->item('staticPath'); ?>/js/admin.js"></script>

	<script language="JavaScript">			
		$(function(){
			$('ul#menubar li').hover(
				function() { $('ul', this).css('display', 'block').parent().addClass('hover'); },
				function() { $('ul', this).css('display', 'none').parent().removeClass('hover'); }
			);			
		});		
	</script>		
	
	<title><?php echo (isset($this->site->config['siteName'])) ? $this->site->config['siteName'] : 'Login to'; ?> Admin - ForgeIgniter</title>
	
</head>
<body>

<div class="bg">
	
	<div class="container">
	
		<div id="header">

			<div id="logo">
			
				<?php
					// set logo
					if ($this->config->item('logoPath')) $logo = $this->config->item('logoPath');
					elseif ($image = $this->uploads->load_image('admin-logo')) $logo = $image['src'];
					else $logo = base_url() . $this->config->item('staticPath').'/images/ForgeIgniter-Logo.jpg';
				?>

				<h1><a href="<?php echo site_url('/admin'); ?>"><?php echo (isset($this->site->config['siteName'])) ? $this->site->config['siteName'] : 'Login to'; ?> Admin</a></h1>
				<a href="<?php echo site_url('/admin'); ?>"><img src="<?php echo $logo; ?>" alt="Logo" /></a>

			</div>

			<div id="siteinfo">
				<ul id="toolbar">
					<li><a href="<?php echo site_url('/'); ?>">View Site</a></li>				
					<?php if ($this->flexi_auth->is_admin()): ?>				
						<li><a href="<?php echo site_url('/admin/dashboard'); ?>">Dashboard</a></li>
						<li><a href="<?php echo site_url('/admin/users/edit/'.$this->session->userdata('userID')); ?>">My Account</a></li>
						<?php if ($this->session->userdata('groupID') == $this->site->config['groupID'] || $this->session->userdata('groupID') < 0): ?>
							<li><a href="<?php echo site_url('/admin/site/'); ?>">My Site</a></li>
							<li><a href="<?php echo base_url('/static/docs'); ?>" target="_blank">Docs</a></li>
						<?php endif; ?>
						<?php if ($this->flexi_auth->is_admin() && $this->flexi_auth->in_group('Master Admin')): ?>
							<li class="noborder"><a href="<?php echo site_url('/admin/logout'); ?>">Logout</a></li>
							<li class="superuser"><a href="<?php echo site_url('/halogy/sites'); ?>">Sites</a></li>
						<?php else: ?>
							<li class="last"><a href="<?php echo site_url('/admin/logout'); ?>">Logout</a></li>
						<?php endif; ?>						
					<?php else: ?>
						<li class="last"><a href="<?php echo site_url('/admin'); ?>">Login</a></li>
					<?php endif; ?>
				</ul>

				<?php if ($this->flexi_auth->is_admin()): ?>	
					<h2 class="clear"><strong><?php echo $this->site->config['siteName']; ?> Admin</strong></h2>
					<h3>Logged in as: <strong><?php echo $this->session->userdata('username'); ?></strong></h3>
				<?php endif; ?>	
			</div>

		</div>
		
		<div id="navigation">
			<ul id="menubar">
			<?php if ($this->flexi_auth->is_logged_in_via_password() || $this->flexi_auth->is_admin()): ?>
				
				<?php if ($this->flexi_auth->is_privileged('Allow Pages')): ?>
					<!-- Pages -->
					<li><a href="<?php echo site_url('/admin/pages'); ?>">Pages</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/pages/viewall'); ?>">All Pages</a></li>
							<?php if ($this->flexi_auth->is_privileged('Add / edit pages')): ?>
								<li><a href="<?php echo site_url('/admin/pages/add'); ?>">Add Page</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				
				<?php if ($this->flexi_auth->is_privileged('Allow Templates')): ?>
					<!-- Templates -->
					<li><a href="<?php echo site_url('/admin/pages/templates'); ?>">Templates</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/pages/templates'); ?>">All Templates</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes'); ?>">Includes</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes/css'); ?>">CSS</a></li>
							<li><a href="<?php echo site_url('/admin/pages/includes/js'); ?>">Javascript</a></li>
						</ul>
					</li>
				<?php endif; ?>	
				
				<?php if ($this->flexi_auth->is_privileged('Allow image uploads')): ?>
					<!-- Uploads -->
					<li><a href="<?php echo site_url('/admin/images/viewall'); ?>">Uploads</a>
						<ul class="subnav">				
							<li><a href="<?php echo site_url('/admin/images/viewall'); ?>">Images</a></li>
							<?php if ($this->flexi_auth->is_privileged('Access to all images')): ?>
								<li><a href="<?php echo site_url('/admin/images/folders'); ?>">Image Folders</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Allow file uploads')): ?>
								<li><a href="<?php echo site_url('/admin/files/viewall'); ?>">Files</a></li>
								<?php if ($this->flexi_auth->is_privileged('Access to all files')): ?>							
									<li><a href="<?php echo site_url('/admin/files/folders'); ?>">File Folders</a></li>						
								<?php endif; ?>
							<?php endif; ?>								
						</ul>
					</li>
				<?php endif; ?>
				
				<?php if ($this->flexi_auth->is_privileged('Allow Web Forms')): ?>
					<!-- Web Forms -->
					<li><a href="<?php echo site_url('/admin/webforms/tickets'); ?>">Web Forms</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/webforms/tickets'); ?>">Tickets</a></li>
							<?php if ($this->flexi_auth->is_privileged('Add / edit web forms')): ?>
								<li><a href="<?php echo site_url('/admin/webforms/viewall'); ?>">All Web Forms</a></li>
								<li><a href="<?php echo site_url('/admin/webforms/add_form'); ?>">Add Web Form</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>
				
				<?php if ($this->flexi_auth->is_privileged('Allow Blog')): ?>
					<li><a href="<?php echo site_url('/admin/blog/viewall'); ?>">Blog</a>
						<!-- Blog -->
						<ul class="subnav">
							<?php if ($this->flexi_auth->is_privileged('Access to all posts')): ?>
								<li><a href="<?php echo site_url('/admin/blog/viewall'); ?>">All Posts</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit posts')): ?>
								<li><a href="<?php echo site_url('/admin/blog/add_post'); ?>">Add Post</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit categories')): ?>
								<li><a href="<?php echo site_url('/admin/blog/categories'); ?>">Categories</a></li>
							<?php endif; ?>							
							<li><a href="<?php echo site_url('/admin/blog/comments'); ?>">Comments</a></li>
						</ul>
					</li>
				<?php endif; ?>
				
				<?php if ($this->flexi_auth->is_privileged('Allow Shop')): ?>
					<!-- Shop -->
					<li><a href="<?php echo site_url('/admin/shop/products'); ?>">Shop</a>
						<ul class="subnav">
							<?php if ($this->flexi_auth->is_privileged('Access to all products')): ?>
								<li><a href="<?php echo site_url('/admin/shop/products'); ?>">All Products</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit products')): ?>
								<li><a href="<?php echo site_url('/admin/shop/add_product'); ?>">Add Product</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit categories')): ?>
								<li><a href="<?php echo site_url('/admin/shop/categories'); ?>">Categories</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit orders')): ?>
								<li><a href="<?php echo site_url('/admin/shop/orders'); ?>">View Orders</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit shipping')): ?>
								<li><a href="<?php echo site_url('/admin/shop/bands'); ?>">Shipping Bands</a></li>
								<li><a href="<?php echo site_url('/admin/shop/postages'); ?>">Shipping Costs</a></li>
								<li><a href="<?php echo site_url('/admin/shop/modifiers'); ?>">Shipping Modifiers</a></li>								
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit discounts')): ?>
								<li><a href="<?php echo site_url('/admin/shop/discounts'); ?>">Discount Codes</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit reviews')): ?>
								<li><a href="<?php echo site_url('/admin/shop/reviews'); ?>">Reviews</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit upsells')): ?>
								<li><a href="<?php echo site_url('/admin/shop/upsells'); ?>">Upsells</a></li>
							<?php endif; ?>			
						</ul>
					</li>
				<?php endif ?>
				
				<?php if ($this->flexi_auth->is_privileged('Access Events')): ?>
					<!-- Events -->
					<li><a href="<?php echo site_url('/admin/events/viewall'); ?>">Events</a>
						<ul class="subnav">
							<li><a href="<?php echo site_url('/admin/events/viewall'); ?>">All Events</a></li>
						<?php if ($this->flexi_auth->is_privileged('Add / edit events')): ?>
							<li><a href="<?php echo site_url('/admin/events/add_event'); ?>">Add Event</a></li>
						<?php endif; ?>	
						</ul>
					</li>
				<?php endif; ?>

				<?php if ($this->flexi_auth->is_privileged('Access Forums')): ?>
					<!-- Forums -->
					<li><a href="<?php echo site_url('/admin/forums/forums'); ?>">Forums</a>
						<ul class="subnav">
							<?php if ($this->flexi_auth->is_privileged('Access Forums')): ?>
								<li><a href="<?php echo site_url('/admin/forums/forums'); ?>">Forums</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit categories')): ?>
								<li><a href="<?php echo site_url('/admin/forums/categories'); ?>">Forum Categories</a></li>
							<?php endif; ?>
						</ul>
					</li>
				<?php endif; ?>

				<?php if ($this->flexi_auth->is_privileged('Access Wiki')): ?>
					<!-- WIKI -->
					<li><a href="<?php echo site_url('/admin/wiki'); ?>">Wiki</a>
						<ul class="subnav">
							<?php if ($this->flexi_auth->is_privileged('Add / edit wiki')): ?>
								<li><a href="<?php echo site_url('/admin/wiki/viewall'); ?>">All Wiki Pages</a></li>
							<?php endif; ?>
							<?php if ($this->flexi_auth->is_privileged('Add / edit categories')): ?>
								<li><a href="<?php echo site_url('/admin/wiki/categories'); ?>">Wiki Categories</a></li>
							<?php endif; ?>
							<li><a href="<?php echo site_url('/admin/wiki/changes'); ?>">Recent Changes</a></li>							
						</ul>
					</li>
				<?php endif; ?>			
				<?php if ($this->flexi_auth->is_privileged('View Users')): ?>
					<li><a href="<?php echo site_url('/admin/users/viewall'); ?>">Users</a>
					<?php if ($this->flexi_auth->is_privileged('View User Groups')): ?>
						<ul class="subnav">				
							<li><a href="<?php echo site_url('/admin/users/viewall'); ?>">All Users</a></li>
							<li><a href="<?php echo site_url('/admin/users/groups'); ?>">User Groups</a></li>
						</ul>
					<?php endif; ?>						
					</li>
				<?php endif; ?>
				<?php else: ?>
					<li><a href="<?php echo site_url('/admin'); ?>">Login</a></li>
				<?php endif; ?>					
			</ul>
			
		</div>
		
		<div id="content" class="content">
	