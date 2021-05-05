let preview = document.getElementById('avatar')
let file_input = document.getElementById('basic_user_data_profile_picture')

window.previewFile = function () {
    let file = file_input.files[0]
    let reader = new FileReader()

    reader.addEventListener('load', function (event) {
        preview.src = reader.result
    }, false)

    if (file) {
        reader.readAsDataURL(file)
    }
}
