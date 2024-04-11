jQuery(document).ready(function ($) {
	'use strict';

/*
	Click event fot the book form filter.
*/
$('#form-submit').click(function(e) {
    e.preventDefault();

    setTimeout($('.book-grid').html('Loading..'), 7000);

    var selectedAuthor = $('#author').val();
    var selectedPublication = $('#publication').val();
    var paged = 1;

    var action = 'book_filter';
    var nonce = $('#book_filter_nonce_field').val();

    $.ajax({
        type: 'POST',
        url: book_ajax_object.ajax_url,
        data: {
            action: action,
            selectedAuthor: selectedAuthor,
            selectedPublication: selectedPublication,
            book_filter_nonce_field: nonce,
            paged: page

        },
        success: function(response) {
            if(response){
               $('.book-grid').html(response.data.html);
               paged++;
               $('#load-more').hide();
            }else{
               $('.book-grid').text('No Books Found');
            }
        }
    });
});

/*
	Keyup event for the book search.
*/
$('#book-search').on('keyup', function() {
    var searchText = $(this).val().toLowerCase();
    var nonce = $('#book_filter_nonce_field').val();
    $.ajax({
        type: 'POST',
        url: book_ajax_object.ajax_url,
        data: {
            action: 'book_search',
            searchText: searchText,
            book_filter_nonce_field: nonce
        },
        success: function(response) {
            if(response){
               $('.book-grid').html(response.data.html);
               $('#load-more').hide(); 
            }else{
               $('.book-grid').text('No Books Found');
            }
        }
    });
});

    var page = 1;
    /*
        Load More for books listing
    */
    $('#load-more').on('click', function() {
        page++;
        var data = {
            'action': 'load_more_books',
            'page': page
        };

        $.ajax({
            url: book_ajax_object.ajax_url,
            type: 'post',
            data: data,
            success: function(response) {
                $('.book-grid').append(response.data.html);
                if (response.data.reached_end) {
                    $('#load-more').hide();
                }
            }   
        });
    });
});