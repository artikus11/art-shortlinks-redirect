jQuery(document).ready(function ($) {

    var clipboard = new ClipboardJS('.asr-clipboard');

    clipboard.on('success', function (e) {
        console.info('Action:', e.action);
        console.info('Text:', e.text);
        console.info('Trigger:', e.trigger);
        console.log(e);
        $(e.trigger).prop('title', 'Скопировано!');
        $(e.trigger).addClass('tooltip');
        setTimeout(function () {
            $(e.trigger).removeClass('tooltip');
        }, 1000)
        //e.clearSelection();
    });

    clipboard.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });

});