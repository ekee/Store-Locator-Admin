<div class="table_actions well">
	<a href="#" class="btn success geocode_all" title="Geocode all stores on the page">Geocode all stores</a>
	<a href="#" class="btn danger delete_all" title="Delete all stores on the page">Delete all stores</a>
</div>

<table id="store_table" data-ajax-loader-image="<?php echo URL_PUBLIC  ?>/images/ajax_loader.gif">
	<thead>
		<tr>
			<?php foreach( $vars['columns_list'] as $tc ): ?>
				<td><?php e( prettify_var( $tc ) ) ?></td>
			<?php endforeach; ?>
			<td class="center">Geocoded</td>
			<td class="center">Actions</td>
		</tr>
	</thead>
	<tbody>
	<?php foreach( $vars['stores'] as $store ): ?>
	<tr data-geocode="<?php echo $store->getQueryString() ?>">
		<?php foreach( $vars['columns_list'] as $tc ): ?>
			<td><?php e( $store->raw( $tc ) ) ?></td>
		<?php endforeach; ?>
		<td class="center"><?php if( $store->isGeocoded() ): ?>Yes<?php else: ?><a href="#" class="geocode_table btn small success" title="Geocode this store">No</a><?php endif; ?></td>
		<td class="store_actions center">
			<a class="btn small primary" href="<?php echo URL_EDIT ?>/<?php echo $store->getID() ?>/" title="Edit this store">edit</a>
			<a class="btn small danger delete_store" href="<?php echo URL_DELETE ?>/<?php echo $store->getID() ?>/" data-id="<?php echo $store->getID() ?>" class="delete_store" title="Delete this store">X</a>
		</td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>

<?php if( $vars['page_store_count'] > 20 ): ?>
<div class="table_actions well">
	<a href="#" class="btn success geocode_all" title="Geocode all stores on the page">Geocode all stores</a>
	<a href="#" class="btn danger" title="Delete all stores on the page">Delete all stores</a>
</div>
<?php endif; ?>