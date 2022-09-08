/**************
 * TOAST *
 **************/
const toast = $('.notify');
if(toast) {
    function launch_toast() {
        toast.removeClass()
            .addClass('bottom-right' + ' notify')
            .addClass('do-show');

        setTimeout( () => {
            $('.notify').fadeOut();
        }, 4500);
    }
    launch_toast()
}
