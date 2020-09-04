let $toast = $(".toast");
export let toast = content => {
    $(".toast-body").text(content)
    $toast.toast("show");
};