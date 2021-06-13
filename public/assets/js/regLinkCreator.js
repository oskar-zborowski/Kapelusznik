$('.regLink').each(function() {
    let link = $(this).text();
    $(this).contents().replaceWith('<a class="text-white visible" target="_blank" href="agreement/'+link+'</a>');
});
