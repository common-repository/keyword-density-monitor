// digital quill keyowrd analysis for tinyme
(function() {

	tinymce.create('tinymce.plugins.dqKeywords', {

		init : function(ed, url) {

		// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceInsights');
			ed.addCommand('mcedqKeywords', update() );

			// Register a button
			ed.addButton('btndqKeywords', {
				title : 'Keyword Density',
				cmd : 'mcedqKeywords',
				image : url + '/button.gif'
			});


		}

	});

	// Register plugin
	tinymce.PluginManager.add('dqKeywords', tinymce.plugins.Insights);
})();
