<div class="error-log">
<?php
	$filename = PMPro_Discord_Logs::$log_file_name;
	$handle   = fopen( ETS_PMPRO_DISCORD_PATH . $filename, 'a+' );
while ( ! feof( $handle ) ) {
	echo fgets( $handle ) . '<br />';
}
	fclose( $handle );
?>
</div>
<div class="clrbtndiv">
	<div class="form-group">
		<input type="button" class="clrbtn ets-submit ets-bg-red" id="clrbtn" name="clrbtn" value="Clear Logs !">
		<span class="clr-log spinner" ></span>
	</div>
	<div class="form-group">
		<input type="button" class="ets-submit ets-bg-green" value="Refresh" onClick="window.location.reload()">
	</div>
	<div class="form-group">
		<a href="<?php echo esc_attr(ETS_PMPRO_DISCORD_URL . 'discord_api_logs.txt'); ?>" class="ets-submit ets-bg-download" download>Download</a>
	</div>
</div>
