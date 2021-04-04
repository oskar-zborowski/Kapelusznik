// const aClickListener = document.querySelector('a');
// const formSubmitListener = document.querySelector('form');

let isFormOrLink = false;
let delayExitResponse;

// aClickListener.addEventListener('click', function (e) {    
//     isFormOrLink = true;
// }, false);

// formSubmitListener.addEventListener('submit', function (e) {    
//     isFormOrLink = true;
// }, false);

window.onbeforeunload = function (e) {
    if (!isFormOrLink)
        sendRequest('GET', '/fakeExit');
}

const delayExit = async () =>
{
    delayExitResponse = await sendRequest('GET', '/delayExit');

    if (delayExitResponse == 1)
        delayExit();
}

delayExit();