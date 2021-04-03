let inFormOrLink = false;

$('a').on('click', function() { 
    inFormOrLink = true;
});

$('form').on('submit', function() {
    inFormOrLink = true;
});

$(window).on('beforeunload', function() { 
    if (!inFormOrLink)
        sendRequest('GET', '/fakeExit');
});

const delayExit = async () =>
{
    const response = await sendRequest('GET', '/delayExit');

    if (response == 1)
        delayExit();
}

delayExit();