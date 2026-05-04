		<script src="js/jquery.min.js" 									 ></script>
		<script src="js/sortable.min.js" 							defer></script>
		<script src="js/jquery.sortable.js" 						defer></script>
		<script src="js/jquery.table.xlsx.min.js" 					defer></script>
		<script src="js/jquery.form.min.js" 						defer></script>
		
		<script src="js/bootstrap.bundle.min.js" 					defer></script>
	    <script src="js/bootstrap-table.min.js" 					defer></script>
		<script src="js/bootstrap-table-mobile.min.js" 				defer></script>
		<script src="js/bootstrap-table-addtools.js" 				defer></script>
	    <script src="js/locales/bootstrap-table-locale-all.min.js" 	defer></script>
		<script src="js/bootstrap-select.min.js" 					defer></script>
		<script src="js/bootstrap-colorpicker.min.js" 				defer></script>
		<script src="js/moment.min.js" 								defer></script>
		<script src="js/bootstrap-datetimepicker.js" 				defer></script>
		<script src="js/locales/bootstrap-datetimepicker.fr.js" 	defer></script>
		<script src="js/bootstrap-clockpicker.js" 					defer></script>
		
		<script src="js/dropzone.min.js" 							defer></script>
		<script src="js/zoom-by-ironex.min.js" 						defer></script>
		<script src="js/webcam-easy.min.js" 						defer></script>
		<script src="js/chart.min.js" 								defer></script>
		
		<script src="js/main.js?ver=<?php echo filemtime('js/main.js')?>" 			defer></script>
	    <script src="js/main_gen.js?ver=<?php echo filemtime('js/main_gen.js')?>" 	defer></script>
	    <script src="js/main_ext.js?ver=<?php echo filemtime('js/main_ext.js')?>" 	defer></script>
		
		<script>
		if ('serviceWorker' in navigator) {
			window.addEventListener('load', () => {
				// Register the service worker after the page is loaded.
				// Generally not before since this could slow down this loading step.
				navigator.serviceWorker.register('sw.js').then(registration => {
					// Registration was successful so service worker is downloaded.
					// OPTION: registration.update();
					console.log(`Service Worker registered! Scope: ${registration.scope}`);
				}, error => {
					// Registration failed so service worker is not downloaded but just discarded. 
					console.error(`Service Worker registration failed: ${error}`);
				});
			});
		}
		</script>