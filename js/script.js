$(function() {

    TagForm.init()
    UserForm.init()
});

/******************* toast */

function toast(text, time) {
    $('#toast').html(text).animate({ 'top': '60px', 'display': 'block' }, 500)
    setTimeout(() => {
        closeToast()
    }, time * 1000)
}

function closeToast() {
    $('#toast').animate({ 'top': '-60px', 'display': 'none' }, 500).html('')
}

function confirmToast(text, yesCallback) {

    text += `<button id="toast-confirm">Yes</button> 
            <button id="toast-unconfirm">No</button>`

    toast(text, 6)

    $("#toast-confirm").click(() => {
        yesCallback()
        closeToast()
    })

    $("#toast-unconfirm").click(() => {
        closeToast()
    })
}

/********************* markdown */

function parseMarkdown(md) {

    //ul
    md = md.replace(/^\s*\n\*/gm, '<ul>\n*');
    md = md.replace(/^(\*.+)\s*\n([^\*])/gm, '$1\n</ul>\n\n$2');
    md = md.replace(/^\*(.+)/gm, '<li>$1</li>');

    //hr
    md = md.replace(/---+/gm, '<hr>');

    //h
    md = md.replace(/[\#]{3}(.+)/g, '<h3>$1</h3>');
    md = md.replace(/[\#]{2}(.+)/g, '<h2>$1</h2>');
    md = md.replace(/[\#]{1}(.+)/g, '<h1>$1</h1>');

    //links
    md = md.replace(/[\[]{1}([^\]]+)[\]]{1}[\(]{1}([^\)\"]+)(\"(.+)\")?[\)]{1}/g, '<a href="$2" title="$4" target="blanc">$1</a>');

    //font styles
    md = md.replace(/[\*\_]{2}([^\*\_]+)[\*\_]{2}/g, '<b>$1</b>');
    md = md.replace(/[\*\_]{1}([^\*\_]+)[\*\_]{1}/g, '<i>$1</i>');

    //p
    md = md.replace(/^\s*(\n)?(.+)/gm, function(m) {
        return /\<(\/)?(h\d|ul|ol|li)/.test(m) ? m : '<p>' + m + '</p>';
    });

    return md;
}