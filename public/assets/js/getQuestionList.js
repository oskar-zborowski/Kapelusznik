let getListResponse2;
let listDiv2 = document.getElementById('questionList');

const getQuestionList = async () =>
{
    getListResponse2 = await sendRequest('GET', '/questionList');

    const list = JSON.parse(getListResponse2);

    let input = '';

    if (list != null) {
        list.forEach(question => {
            input = input + '<li>' + question.content + ' <a href="room?deleteQuestion=' + question.id + '">Usuń</a>' + '</li>'
        });
    
        listDiv2.innerHTML = input;
    } else {
        listDiv2.innerHTML = '';
    }

    

    getQuestionList();
}

getQuestionList();