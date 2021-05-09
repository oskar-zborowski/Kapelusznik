let preview = document.getElementsByClassName('avatar')[0]
let profile_image_input = document.getElementById('basic_user_data_profile_picture')
let room_image_input = document.getElementById('new_room_logo_filename')

window.previewFile = function () {
    let file = profile_image_input ? profile_image_input.files[0] : room_image_input.files[0];
    let reader = new FileReader()

    reader.addEventListener('load', function (event) {
        preview.src = reader.result
    }, false)

    if (file) {
        reader.readAsDataURL(file)
    }
}
