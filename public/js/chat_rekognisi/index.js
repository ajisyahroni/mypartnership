$(document).ready(function () {
    let receiverId = sender ?? $("#receiver_id").val();
    let Idrec = $("#id_rec").val();
    loadMessages();
    console.log(sender);

    if (sender) {
        $(".user-item").removeClass("bg-primary text-white");
        $(`.user-item[data-id="${sender}"]`).addClass("bg-primary text-white");

        $("#receiver_id").val(sender);
    }
    // Event klik pada user-item untuk mengganti receiver_id dan memuat pesan
    $(".user-item").click(function () {
        receiverId = $(this).data("id"); // Ambil receiver_id dari user yang diklik
        $("#receiver_id").val(receiverId); // Perbarui input hidden

        $(".user-item").removeClass("bg-primary text-white"); // Reset tampilan user list
        $(this).addClass("bg-primary text-white"); // Tandai user yang dipilih

        loadMessages(); // Panggil fungsi loadMessages()
    });

    // Fungsi untuk memuat pesan hanya saat halaman di-refresh atau user baru dipilih
    function sendMail(id_chat) {
        toastr.success("Proses Mengirim Pesan Email");
        $.ajax({
            url: "/chatRecognisi/sendMail",
            type: "POST",
            data: {
                receiver_id: receiverId,
                id_chat: id_chat,
                _token: csrfToken,
            },
            dataType: "json",
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
        });
    }

    function loadMessages() {
        $("#message").prop("disabled", true);
        $("#sendMessage").prop("disabled", true);
        if (receiverId) {
            $("#message").prop("disabled", false);
            $("#sendMessage").prop("disabled", false);
        }
        let chatBox = $("#chat-box");
        chatBox.html("<p class='text-center'>Loading...</p>");
        $.ajax({
            url: "/chatRecognisi/load/" + Idrec + "/" + receiverId,
            type: "GET",
            success: function (response) {
                chatBox.html(response);

                chatBox.scrollTop(chatBox[0].scrollHeight);
            },
        });
    }

    // Mengirim pesan
    $("#sendMessage").click(function () {
        let message = $("#message").val();
        let chatBox = $("#chat-box");
        chatBox.html("<p class='text-center'>Loading...</p>");
        if (message.trim() !== "") {
            $.ajax({
                url: "/chatRecognisi/send",
                type: "POST",
                data: {
                    receiver_id: receiverId,
                    id_rec: Idrec,
                    message: message,
                    _token: csrfToken,
                },
                dataType: "json",
                success: function (response) {
                    $("#message").val("");
                    loadMessages(); // Hanya muat ulang pesan setelah mengirim pesan baru
                    sendMail(response.id);
                },
            });
        }
    });
});
