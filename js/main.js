/*
	Minimaxing by HTML5 UP
	html5up.net | @n33co
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

(function($) {

	skel
		.breakpoints({
			desktop: '(min-width: 737px)',
			tablet: '(min-width: 737px) and (max-width: 1200px)',
			mobile: '(max-width: 736px)'
		})
		.viewport({
			breakpoints: {
				tablet: {
					width: 1080
				}
			}
		});

	$(function() {

		var $window = $(window),
			$body = $('body');

		// Fix: Placeholder polyfill.
			$('form').placeholder();

		// Prioritize "important" elements on mobile.
			skel.on('+mobile -mobile', function() {
				$.prioritize(
					'.important\\28 mobile\\29',
					skel.breakpoint('mobile').active
				);
			});

		// Off-Canvas Navigation.

			// Title Bar.
				$(
					'<div id="titleBar">' +
						'<a href="#navPanel" class="toggle"></a>' +
						'<span class="title">' + $('#logo').html() + '</span>' +
					'</div>'
				)
					.appendTo($body);

			// Navigation Panel.
				$(
					'<div id="navPanel">' +
						'<nav>' +
							$('#nav').navList() +
						'</nav>' +
					'</div>'
				)
					.appendTo($body)
					.panel({
						delay: 500,
						hideOnClick: true,
						hideOnSwipe: true,
						resetScroll: true,
						resetForms: true,
						side: 'left',
						target: $body,
						visibleClass: 'navPanel-visible'
					});

			// Fix: Remove navPanel transitions on WP<10 (poor/buggy performance).
				if (skel.vars.os == 'wp' && skel.vars.osVersion < 10)
					$('#titleBar, #navPanel, #page-wrapper')
						.css('transition', 'none');

	});

})(jQuery);


function changeMark(event, ui) {
    var mark_code = marks[$("#mark").val()];
    if(code_hasMark && mark_code) {
        // Replace mark code
        code = code.substring(0, code.length - 4);
        code += " " + mark_code;
    } else if(code_hasMark && !mark_code) {
        // Remove mark code
        code = code.substring(0, code.length - 4);
        code_hasMark = false;
    } else if(!code_hasMark && mark_code) {
        // Add mark code
        code += " " + mark_code;
        code_hasMark = true;
    } else {
        // Do nothing
    }

    code = code.trim();

    $("#deckCode").val(code);
    updateImage();
}

function addCard(event, ui) {
    $.get("card_search.php", { query: $("#add_cards").val(), complex: "true" }, function(data) {
        var code_index = code.indexOf(data);

        // Add it
        code += " " + data;
        code = code.trim();

        // Sort
        sortCode();

        // New row to <table>
        buildTable();

        // Update textarea and image
        $("#deckCode").val(code);
        updateImage();

        // Empty add_cards text box
        $("#add_cards").val("");
    });
}

function updateImage() {
    $("#deckImage").attr("src", "http://dek.im/deck/" + code + ".png");
    $("#deckImage_link").attr("href", "http://dek.im/d/" + code);
}

function sortCode() {
    code_arr = code.split(" ");
    code_arr.sort();
    code = code_arr.join(" ");
}

function buildTable() {
    var html = "";
    code_arr = code.split(" ");
    $.get("deck_format.php", { code: code }, function(data) {
        $("#existing_cards tbody").html(data);
    });
}