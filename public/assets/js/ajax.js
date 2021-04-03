async function sendRequest(method, url, data = null) {
    return new Promise(function (resolve, reject) {
        if (method == 'GET') {
            url += '?' + data;
            data = null;
        }

        let xhr = new XMLHttpRequest();

        xhr.open(method, url);

        xhr.onload = function () {
            if (this.status >= 200 && this.status < 300)
                resolve(xhr.response);
            else {
                reject({
                    status: this.status,
                    statusText: xhr.statusText
                });
            }
        };

        xhr.onerror = function () {
            reject({
                status: this.status,
                statusText: xhr.statusText
            });
        };

        if (method == 'POST')
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        xhr.send(data);
    });
}