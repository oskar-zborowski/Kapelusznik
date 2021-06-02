let getListResponse2;
let listDiv2 = document.getElementById('questionList');

const getQuestionList = async () =>
{
    getListResponse2 = await sendRequest('GET', '/questionList');

    const list = JSON.parse(getListResponse2);

    let input = '';

    if (list != null) {
        let admin2 = null;

        list.forEach(question => {
            if (question['id'] == 0) {
                admin2 = question['content'];
            } else {
                if (admin2)
                    input = input + '<li>' + question.content + ' <a href="room?deleteQuestion=' + question.id + '">Usuń</a>' + '</li>';
                else
                    input = input + '<li>' + question.content + '</li>';
            }
        });
    
        listDiv2.innerHTML = input;
    } else {
        listDiv2.innerHTML = '';
    }

    

    getQuestionList();
}

getQuestionList();