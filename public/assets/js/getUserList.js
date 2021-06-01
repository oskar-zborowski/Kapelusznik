let getListResponse;
let listDiv = document.getElementById('userList');

const getUserList = async () =>
{
    getListResponse = await sendRequest('GET', '/userList');

    const list = JSON.parse(getListResponse);

    let input = '';

    if (list != null) {
        list.forEach(person => {
            if (person.admin)
                input = input + '<li>' + person.name + '</li>';
            else
                input = input + '<li>' + person.name + ' <a href="room?deleteUser=' + person.id + '">Wyrzuć</a>' + '</li>';
        });

        listDiv.innerHTML = input;
    } else {
        listDiv.innerHTML = '';
    }

    getUserList();
}

const getMe = async () =>
{
    getListResponse = await sendRequest('GET', '/getMe');

    if (getListResponse == 0) {
        location.reload();
    }

    getMe();
}

getUserList();
getMe();