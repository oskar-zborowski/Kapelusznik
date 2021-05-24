let getListResponse;
let listDiv = document.getElementById('userList');

const getUserList = async () =>
{
    getListResponse = await sendRequest('GET', '/userList');

    const list = JSON.parse(getListResponse);

    let input = '';

    list.forEach(person => {
        input = input + '<li>' + person + '</li>'
    });

    listDiv.innerHTML = input;

    getUserList();
}

getUserList();