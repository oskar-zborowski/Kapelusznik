let getListResponse;
let listDiv = document.getElementById('userList');

const getUserList = async () =>
{
    getListResponse = await sendRequest('GET', '/userList');

    const list = JSON.parse(getListResponse);

    let input = '';

    if (list != null) {
        let counter = 0;
        let admin = null;

        list.forEach(person => {
            if (counter == 0) {
                admin = person.admin;
                counter++;
            }
            else if (person.admin)
                input = input + '<li>' + person.name + '</li>';
            else {
                if (admin)
                    input = input + '<li>' + person.name + ' <a href="room?deleteUser=' + person.id + '">Wyrzuć</a>' + '</li>';
                else
                    input = input + '<li>' + person.name + '</li>';
            }
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

    const list = JSON.parse(getListResponse);

    if (list.exit == 1) {
        window.location.replace('/game');
    } else if (list.in == 0) {
        location.reload();
    } else if (list.out == 1) {
        window.location.replace('/game');
    } else {
        getMe();
    }
}

getUserList();
getMe();