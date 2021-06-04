let getListResponse3;

const getGameAjax = async () =>
{
    getListResponse3 = await sendRequest('GET', '/getState');

    if (getListResponse3 == 1) {
        location.reload();
    }

    getGameAjax();
}

getGameAjax();