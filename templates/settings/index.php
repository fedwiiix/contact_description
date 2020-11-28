<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button" data-apps-slide-toggle="#app-settings-content"><?php echo p($l->t('Settings')); ?></button>
	</div>
	<div id="app-settings-content">
		<div class="settings-block">
			<ul>
				<li>
					<a href="#" id="import-file" class="icon-confirm">
						<label for="import-file-input"><?php echo p($l->t('Import from json file')); ?></label>
						<input hidden type="file" id="import-file-input" multiple>
					</a>
				</li>
				<li>
					<a href="#" id="export-json-file" class="icon-external">
						<label><?php echo p($l->t('Export to json file')); ?></label>
					</a>
				</li>
				<li>
					<a href="#" id="export-csv-file" class="icon-external">
						<label><?php echo p($l->t('Export files to csv file')); ?></label>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>