let getListResponse4;
let listDiv4 = document.getElementById('usersNumber');

const getUsersNumber = async () =>
{
    getListResponse4 = await sendRequest('GET', '/getAnswersNumber');

    if (getListResponse4 != null) {
        listDiv4.innerHTML = getListResponse4;
        getUsersNumber();
    }
}

getUsersNumber();