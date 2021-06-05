let getListResponse3;

const getGameAjax = async () =>
{
    getListResponse3 = await sendRequest('GET', '/getState');

    if (getListResponse3 == 1) {
        location.reload();
    } else if (getListResponse3 == 0) {
        getGameAjax();
    } else if (getListResponse3 == 2) {
        location.reload();
    }
}

getGameAjax();