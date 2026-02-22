$(document).ready(function () {
    let receiverId = sender ?? $("#receiver_id").val();
    let Idmou = $("#id_mou").val();
    loadMessages();

    if (sender) {
        $(".user-item").removeClass("bg-primary text-white");
        $(`.user-item[data-id="${sender}"]`).addClass("bg-primary text-white");

        $("#receiver_id").val(sender);
    }

    $(".user-item").click(function () {
        receiverId = $(this).data("id");
        $("#receiver_id").val(receiverId);

        $(".user-item").removeClass("bg-primary text-white");
        $(this).addClass("bg-primary text-white");

        loadMessages();
    });

    function sendMail(id_chat) {
        toastr.success("Proses Mengirim Pesan Email");
        $.ajax({
            url: "/chat/sendMail",
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
            url: "/chat/load/" + Idmou + "/" + receiverId,
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
                url: "/chat/send",
                type: "POST",
                data: {
                    receiver_id: receiverId,
                    id_mou: Idmou,
                    message: message,
                    _token: csrfToken,
                },
                dataType: "json",
                success: function (response) {
                    $("#message").val("");
                    loadMessages();
                    if (response.sendmail) {
                        sendMail(response.message.id);
                    }
                },
            });
        }
    });
});
