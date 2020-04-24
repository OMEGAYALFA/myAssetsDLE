<?php
////////////////////////////////////////////////////////////
// =======================================================
// Mod: MaHarder Assets
// File: editor.php
// Path: /engine/inc/maharder/assets/include/inc/addnews/editor.php
// =======================================================
// Author: Maxim Harder (c) 2020
// Website: https://maxim-harder.de / https://devcraft.club
// Telegram: http://t.me/MaHarder
// =======================================================
// Do not change anything!
// =======================================================
////////////////////////////////////////////////////////////

if( !defined( 'DATALIFEENGINE' ) ) {
	header( "HTTP/1.1 403 Forbidden" );
	header ( 'Location: ../../../../../../' );
	die( "Hacking attempt!" );
}

function insertEditor($field, $iso) {
	global $config, $image_q_upload, $p_name, $dle_login_hash, $implugin, $image_upload,
	       $i18n_lang, $lang, $row, $ed_root;

	$scripts = [];
	$fieldEditor = '';
	$fieldISO = $field . '_' . $iso;
	if( $config['allow_site_wysiwyg'] == "1" ) {

		$scripts[] = <<<HTML
     jQuery(function($){

      $('.wysiwygeditor_{$iso}').froalaEditor({
        dle_root: '',
        dle_upload_area : "{$field}",
        dle_upload_user : "{$p_name}",
        dle_upload_news : "{$row['id']}",
         width: '100%',
        height: '400',
        language: '{$lang['wysiwyg_language']}',
		body_class: dle_theme,

        htmlRemoveTags: [],
		htmlAllowedAttrs: ['.*'],
		
        imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif', 'webp'],
        imageDefaultWidth: 0,
        imageInsertButtons: ['imageBack', '|', 'imageByURL'{$image_q_upload}],
		imageUploadURL: 'engine/ajax/controller.php?mod=upload',
		imageUploadParam: 'qqfile',
		imageUploadParams: { "subaction" : "upload", "news_id" : "{$row['id']}", "area" : "template", "author" : "{$p_name}", "mode" : "quickload", "user_hash" : "{$dle_login_hash}"},
        imageMaxSize: {$config['max_up_size']} * 1024,
		
        toolbarButtonsXS: ['bold', 'italic', 'underline', 'strikeThrough', 'align', 'color', 'insertLink', 'emoticons', '{$implugin}', {$image_upload}'insertVideo', 'paragraphFormat', 'paragraphStyle', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtonsSM: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'color', 'insertLink', '|', 'emoticons', '{$implugin}',{$image_upload}'insertVideo', 'dleaudio', '|', 'paragraphFormat', 'paragraphStyle', '|', 'formatOL', 'formatUL', '|', 'dlehide', 'dlequote', 'dlespoiler'],

        toolbarButtonsMD: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'indent', 'outdent', '|', 'subscript', 'superscript', '|', 'insertTable', 'formatOL', 'formatUL', 'insertHR', '|', 'undo', 'redo', 'dletypo', 'clearFormatting', 'selectAll', '|', 'fullscreen', '-', 
                         'fontFamily', 'fontSize', '|', 'color', 'paragraphFormat', 'paragraphStyle', '|', 'insertLink', 'dleleech', '|', 'emoticons', '{$implugin}',{$image_upload}'|', 'insertVideo', 'dleaudio', 'dlemedia','|', 'dlehide', 'dlequote', 'dlespoiler','dlecode','page_dropdown', 'html'],

        toolbarButtons: ['bold', 'italic', 'underline', 'strikeThrough', '|', 'align', 'indent', 'outdent', '|', 'subscript', 'superscript', '|', 'insertTable', 'formatOL', 'formatUL', 'insertHR', '|', 'undo', 'redo', 'dletypo', 'clearFormatting', 'selectAll', '|', 'fullscreen', '-', 
                         'fontFamily', 'fontSize', '|', 'color', 'paragraphFormat', 'paragraphStyle', '|', 'insertLink', 'dleleech', '|', 'emoticons', '{$implugin}',{$image_upload}'|', 'insertVideo', 'dleaudio', 'dlemedia','|', 'dlehide', 'dlequote', 'dlespoiler','dlecode','page_dropdown', 'html']
                         
      }).on('froalaEditor.image.inserted froalaEditor.image.replaced', function (e, editor, \$img, response) {
	  
			if( response ) {
			
			    response = JSON.parse(response);
			  
			    \$img.removeAttr("data-returnbox").removeAttr("data-success").removeAttr("data-xfvalue").removeAttr("data-flink");

				if(response.flink) {
				  if(\$img.parent().hasClass("highslide")) {
		
					\$img.parent().attr('href', response.flink);
		
				  } else {
		
					\$img.wrap( '<a href="'+response.flink+'" class="highslide"></a>' );
					
				  }
				}
			  
			}
			
		});

});
HTML;

		$fieldEditor = <<<HTML
<textarea id="{$fieldISO}" name="{$fieldISO}" class="wysiwygeditor_{$iso}" style="width:100%;height:200px;">{$row[$fieldISO]}</textarea>
HTML;

	} else {

		$langArr = $i18n_lang['iso'][$iso];
		$spells = [];
		foreach ($langArr as $name => $value) {
			$spells[] = "{$langArr['international']}={$langArr['iso2']}";
		}
		$spells = implode(',', $spells);

		$scripts[] = <<<HTML
jQuery(function($){

	tinyMCE.baseURL = '{$ed_root}engine/editor/jscripts/tiny_mce';
	tinyMCE.suffix = '.min';

	if(dle_theme === null) dle_theme = '';

	tinymce.init({
		selector: 'textarea.wysiwygeditor_{$iso}',
		language : "{$lang['wysiwyg_language']}",
		element_format : 'html',
		body_class: dle_theme,
		width : "100%",
		height : 310,
		plugins: ["fullscreen advlist autolink lists link image charmap anchor searchreplace visualblocks visualchars media nonbreaking table contextmenu emoticons paste textcolor colorpicker codemirror spellchecker dlebutton codesample hr"],
		relative_urls : false,
		convert_urls : false,
		remove_script_host : false,
		toolbar_items_size: 'small',
		verify_html: false,
		branding: false,
		menubar: false,
		image_advtab: true,
		image_dimensions: false,
		image_caption: true,
		toolbar1: "formatselect fontselect fontsizeselect | link anchor dleleech unlink | {$image_upload}{$implugin} dleemo dlemp dletube dlaudio | dlehide dlequote dlespoiler codesample hr visualblocks dlebreak dlepage code",
		toolbar2: "undo redo | copy paste pastetext | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | subscript superscript | table bullist numlist | forecolor backcolor | spellchecker dletypo removeformat searchreplace fullscreen",
		formats: {
		  bold: {inline: 'b'},  
		  italic: {inline: 'i'},
		  underline: {inline: 'u', exact : true},  
		  strikethrough: {inline: 's', exact : true}
		},
		codesample_languages: [ {text: 'HTML/JS/CSS', value: 'markup'}],
		spellchecker_language : "{$iso}",
		spellchecker_languages : "{$spells}",
		spellchecker_rpc_url : "https://speller.yandex.net/services/tinyspell",

		dle_root : "",
		dle_upload_area : "{$field}",
		dle_upload_user : "{$p_name}",
		dle_upload_news : "{$row['id']}",

		content_css : "engine/editor/css/content.css"

	});

});
HTML;

		$fieldEditor = <<<HTML
    <textarea id="{$fieldISO}" name="{$fieldISO}" class="wysiwygeditor_{$iso}" style="width:98%;height:200px;">{$row[$fieldISO]}</textarea>
HTML;


	}

	$fieldEditor .= '<script>' . implode('\n\r', $scripts) . '</script>';

	return $fieldEditor;
}