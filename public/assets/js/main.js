$('.regLink').each(function() {
    let link = $(this).text();
    $(this).contents().replaceWith('<a class="text-white visible" href="agreement/'+link+'</a>');
});
