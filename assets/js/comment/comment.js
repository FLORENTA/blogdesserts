import {toast} from "../alert";

$(document).ready(() => {
    $(".remove-comment").click(e => {
        e.preventDefault();
        let $target = $(e.target);
        let $url = $target.data("href");
        $.ajax({
            type: "DELETE",
            url: $url,
            success: response => {
                toast(response);
                location.reload();
            },
            error: err => {
                toast(err.responseJSON);
            }
        });
    });
});