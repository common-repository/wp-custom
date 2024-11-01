<?php
	/* The WP-Custom Management form script &copy; 2009 kovshenin.com */
	
	$options = get_option("wp-custom-data");
	
	// Submitting the form
	if (isset($_POST["custom-submit"]))
	{
		$uid = $_POST["uid"];
		$name = $_POST["name"];
		$type = $_POST["type"];
		$default = $_POST["default"];
		$options_filterhtml = $_POST["filterhtml"];
		
		$custom_msg = "Good job! You made it!";
		
		// TODO: run some checks here perhaps!

		$options[$uid]["name"] = $name;
		$options[$uid]["type"] = $type;
		$options[$uid]["default"] = $default;
		$options[$uid]["options"]["filterhtml"] = $options_filterhtml;
		update_option("wp-custom-data", $options);
	}
	
	// Editing anything?
	$edit = false;
	if (isset($_GET["uid"], $_GET["edit"]))
	{
		$edit_uid = $_GET["uid"];
		if (!empty($options[$edit_uid]))
		{
			$edit_name = $options[$edit_uid]["name"];
			$edit_type = $options[$edit_uid]["type"];
			$edit_default = $options[$edit_uid]["default"];
			$edit_options_filterhtml = $options[$edit_uid]["options"]["filterhtml"];
			
			$type_selected[$edit_type] = "selected=\"selected\"";
			
			$edit = true;
		}
		else
		{
			$edit_uid = false;
		}
	}
	
	// Deleting anything?
	if (isset($_GET["uid"], $_GET["delete"]))
	{
		$delete_uid = $_GET["uid"];
		if (!empty($options[$delete_uid]))
		{
			unset($options[$delete_uid]);
			update_option("wp-custom-data", $options);
			$custom_msg = "Field successfully deleted. I hope you did that intentionally...";
		}
	}
?>

<div class="wrap">
<h2>WP-Custom Management</h2>
<?php if ($custom_msg) { ?><div class="updated fade below-h2" id="message" style="background-color: rgb(255, 251, 204);"><p><?=$custom_msg;?></p></div><?php } ?>
<div id="col-container">
	<div id="col-right">
<h3>Or manage some</h3>
<p>If you still have no idea of what WP-Custom is for and why these custom fields are here, then you should probably go and read the Help &amp; Support page. Continue only if you know what you are doing. And please, be careful... Good luck my friend! May the flower-power be with you LOL.</p>
<table class="widefat fixed">
	<thead>
		<tr>
			<th class="manage-column column-name" id="id" scope="col">Unique ID</th>
			<th class="manage-column column-name" id="name" scope="col">Name</th>
			<th class="manage-column column-name" id="type" scope="col">Type</th>
			<th class="manage-column column-name" id="default" scope="col">Default value</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="manage-column column-name" id="id" scope="col">Unique ID</th>
			<th class="manage-column column-name" id="name" scope="col">Name</th>
			<th class="manage-column column-name" id="type" scope="col">Type</th>
			<th class="manage-column column-name" id="default" scope="col">Default value</th>
		</tr>
	</tfoot>
	<tbody>
<?php
	if ($options == false)
	{
?>
			<tr class="alternate">
				<td colspan="4">No data yet. Please add a field using the form on the left.</td>
			</tr>
<?php
	}
	else
	{
		foreach ($options as $key => $value)
		{
			$uid = $key;
			$name = $value["name"];
			$type = $value["type"];
			$default = $value["default"];
?>
			<tr class="alternate">
				<td>
					<strong><a class="row-title" href="#"><?=$uid;?></a></strong><br />
					<div class="row-actions"><span class="edit"><a href="?page=wp-custom/manage.php&edit&uid=<?=$uid;?>">Edit</a> | </span><span class="delete"><a onclick="if ( confirm('You are about to delete this item\n  \'Cancel\' to stop, \'OK\' to delete.') ) { return true;}return false;" href="?page=wp-custom/manage.php&delete&uid=<?=$uid;?>" class="submitdelete">Delete</a></span></div>
				</td>
				<td><a href="#"><?=$name;?></a><br /></td>
				<td><a href="#"><?=$type;?></a></td>
				<td><a href="#"><?=$default;?></a></td>
			</tr>
<?php
		}
	}
?>
	</tbody>
</table>

	
	</div>
	<div id="col-left">
	<h3><?=($edit) ? "Edit $edit_uid" : "Add a new field";?></h3>
	<?=($edit) ? "<a href=\"?page=wp-custom/manage.php\">Cancel edit</a>" : "";?>
	<p>So, you want to add a new field don't you? Go ahead, try it out! It's not as dangerous as editing fields. Please read the comments for each field carefully. They mean a lot! Oh and please do fill all the fields, it will work better, I promise.</p>
	<p><strong>Important!</strong> Please make sure that your unique ID doesn't match an already existing custom field otherwise it'll be overwritten.</p>	
<div class="form-wrap">
<form method="post" class="" <?=(!$edit) ? "action=\"?page=wp-custom/manage.php\"" : ""; ?>>
<?php wp_nonce_field('update-options'); ?>
	<div class="form-field form-required">
		<label for="uid">Field unique ID</label>
		<input type="text" value="<?=$edit_uid;?>" id="uid" name="uid"/>
		<p>This is the unique ID for your field. It will actually be the name of the custom fields for the posts, hence you'll use this in your custom theme.</p>
	</div>
	<div class="form-field form-required">
		<label for="name">Field name</label>
		<input type="text" value="<?=$edit_name;?>" id="name" name="name"/>
		<p>This text will appear right next to the actual field. Some sort of Caption I think..</p>
	</div>
	<div class="form-field form-required">
		<label for="type">Field type</label>
		<select id="type" name="type" style="width: 95%;">
			<option value="text" <?=$type_selected["text"];?>>Textbox</option>
			<option value="textarea" <?=$type_selected["textarea"];?>>Textarea</option>
			<option value="url" <?=$type_selected["url"];?>>URL</option>
		</select>
		<p>Type of the custom input field. Text? Area? URL? I'll get more options here sooner or later so don't worry ;)</p>
	</div>
	<div class="form-field form-required">
		<label for="type">Field options</label>
		<input id="filterhtml" name="filterhtml" type="checkbox" value="checked" <?=$edit_options_filterhtml;?> style="width: 1%; margin-left: 20px;" /> Filter HTML<br />
		<p>You know what this is all about</p>
	</div>
	<div class="form-field form-required">
		<label for="default">Default value</label>
		<input type="text" value="<?=$edit_default;?>" id="default" name="default"/>
		<p>Just use text for text.. Duh!</p>
	</div>
	<input type="hidden" id="custom-submit" name="custom-submit" value="1" />
	<p class="submit">
		<input type="submit" class="button-primary" value="<?=($edit) ? "Save Field" : "Add Field"; ?>" />
		<?=($edit) ? '<input type="button" class="button" value="Cancel" onclick="history.go(-1);" />' : ""; ?>
	</p>

</form>
</div>

	</div>
</div>
<? /*
<h3>Debug</h3>
Options: <?=nl2br(print_r($options,true)); ?><br />
_GET: <?=nl2br(print_r($_GET, true)); ?>
*/ ?>