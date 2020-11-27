$(function() {
    Settings.init();
    Tag.init();
    User.init();
    TagAssign.init();
});

/******************* ajax */

function ajaxRequest(url, methode, data, callBack, callBackError) {
    $.ajax({
            url: OC.generateUrl("/apps/people") + url,
            type: methode,
            contentType: "application/json",
            data,
        })
        .done(function(response) {
            if (callBack) {
                callBack(response);
            }
        })
        .fail(function(response, code) {
            if (callBackError) {
                callBackError(response.status);
            } else {
                toast("An error occurred.", 4);
            }
        });
}

/******************* toast */

function toast(text, time) {
    $("#toast").html(text).animate({ top: "60px", display: "block" }, 500);
    setTimeout(() => {
        closeToast();
    }, time * 1000);
}

function closeToast() {
    $("#toast").animate({ top: "-60px", display: "none" }, 500).html("");
}

function confirmToast(text, yesCallback, noCallback) {
    text += `<button id="toast-confirm">Yes</button> 
            <button id="toast-unconfirm">No</button>`;

    toast(text, 6);

    $("#toast-confirm").click(() => {
        yesCallback();
        closeToast();
    });

    $("#toast-unconfirm").click(() => {
        if (noCallback) {
            noCallback()
        }
        closeToast();
    });
}

/********************* markdown */

function parseMarkdown(md) {
    //ul
    md = md.replace(/^\s*\n\*/gm, "<ul>\n*");
    md = md.replace(/^(\*.+)\s*\n([^\*])/gm, "$1\n</ul>\n\n$2");
    md = md.replace(/^\*(.+)/gm, "<li>$1</li>");

    //hr
    md = md.replace(/---+/gm, "<hr>");

    //h
    md = md.replace(/[\#]{3}(.+)/g, "<h3>$1</h3>");
    md = md.replace(/[\#]{2}(.+)/g, "<h2>$1</h2>");
    md = md.replace(/[\#]{1}(.+)/g, "<h1>$1</h1>");

    //links
    md = md.replace(
        /[\[]{1}([^\]]+)[\]]{1}[\(]{1}([^\)\"]+)(\"(.+)\")?[\)]{1}/g,
        '<a href="$2" title="$4" target="blanc">$1</a>'
    );

    //font styles
    md = md.replace(/[\*\_]{2}([^\*\_]+)[\*\_]{2}/g, "<b>$1</b>");
    md = md.replace(/[\*\_]{1}([^\*\_]+)[\*\_]{1}/g, "<i>$1</i>");

    //p
    md = md.replace(/^\s*(\n)?(.+)/gm, function(m) {
        return /\<(\/)?(h\d|ul|ol|li)/.test(m) ? m : "<p>" + m + "</p>";
    });

    return md;
}

/************************************ */
// author Ben Halpern https://dev.to/ben

function niceColor(hex) {
    let hsl = this.hexToHSL(hex);
    hsl[0] = (hsl[0] + 0.5) % 1;
    hsl[1] = (hsl[1] + 0.5) % 1;
    hsl[2] = (hsl[2] + 0.5) % 1;
    return (
        "hsl(" + hsl[0] * 360 + "," + hsl[1] * 100 + "%," + hsl[2] * 100 + "%)"
    );
}

function hexToHSL(hex) {
    // Convert hex to RGB first
    hex = hex.charAt(0) === "#" ? hex.substring(1, 7) : hex;
    var bigint = parseInt(hex, 16);
    var r = (bigint >> 16) & 255;
    var g = (bigint >> 8) & 255;
    var b = bigint & 255;
    (r /= 255), (g /= 255), (b /= 255);
    var max = Math.max(r, g, b),
        min = Math.min(r, g, b);
    var h,
        s,
        l = (max + min) / 2;
    if (max == min) {
        h = s = 0; // achromatic
    } else {
        var d = max - min;
        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
        switch (max) {
            case r:
                h = (g - b) / d + (g < b ? 6 : 0);
                break;
            case g:
                h = (b - r) / d + 2;
                break;
            case b:
                h = (r - g) / d + 4;
                break;
        }
        h /= 6;
    }
    return [h, s, l];
}