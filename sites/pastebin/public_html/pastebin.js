function onTextareaKey(item,e)
{
    //auto-resize textarea?
    var minRows=10;
    var maxRows=40;
    var textLines = item.value.split("\n").length;
    var elementLines = item.rows;
        
    var fittedLines = Math.min(Math.max(textLines, minRows),maxRows);
    if (fittedLines != elementLines)
    {
        item.rows=fittedLines;
    }
}

function initPastebin()
{
    if (document.getElementById)
    {
        var radio;
        
        radio=document.getElementById('expiry_day');
        if (radio)
        {
            radio.onclick=function ()
            {
                var expiryinfo=document.getElementById('expiryinfo');
                expiryinfo.innerHTML="Good for chat conversations";
                
                document.getElementById('expiry_day_label').className='current';
                document.getElementById('expiry_month_label').className='';
                document.getElementById('expiry_forever_label').className='';
            }
            if (radio.checked)
                radio.onclick();
        }
        
        radio=document.getElementById('expiry_month');
        if (radio)
        {
            radio.onclick=function ()
            {
                var expiryinfo=document.getElementById('expiryinfo');
                expiryinfo.innerHTML="Good for email conversations data";
            
                document.getElementById('expiry_day_label').className='';
                document.getElementById('expiry_month_label').className='current';
                document.getElementById('expiry_forever_label').className='';
            }
            if (radio.checked)
                radio.onclick();
        }
        
        radio=document.getElementById('expiry_forever');
        if (radio)
        {
            radio.onclick=function ()
            {
                var expiryinfo=document.getElementById('expiryinfo');
                expiryinfo.innerHTML="Good for long term archival of useful snippets";
            
                document.getElementById('expiry_day_label').className='';
                document.getElementById('expiry_month_label').className='';
                document.getElementById('expiry_forever_label').className='current';
            }
            if (radio.checked)
                radio.onclick();
        }
    }
}
