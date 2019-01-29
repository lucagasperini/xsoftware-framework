function xs_colors_theme_select(color)
{
        var date = new Date();
        date.setTime(date.getTime()+2*60*60*1000);
        document.cookie = 'xs_colors_theme_select'+"="+color+"; expires="+date.toGMTString()+"; path=/";
        location.reload();
}
