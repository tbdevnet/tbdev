function addText(elname, wrap1, wrap2) {
	if (document.selection) { // for IE 
		var str = document.selection.createRange().text;
		document.forms['bbcode2text'].elements[elname].focus();
		var sel = document.selection.createRange();
		sel.text = wrap1 + str + wrap2;
		return;
	} else if ((typeof document.forms['bbcode2text'].elements[elname].selectionStart) != 'undefined') { // for Mozilla
		var txtarea = document.forms['bbcode2text'].elements[elname];
		var selLength = txtarea.textLength;
		var selStart = txtarea.selectionStart;
		var selEnd = txtarea.selectionEnd;
		var oldScrollTop = txtarea.scrollTop;
		//if (selEnd == 1 || selEnd == 2)
		//selEnd = selLength;
		var s1 = (txtarea.value).substring(0,selStart);
		var s2 = (txtarea.value).substring(selStart, selEnd)
		var s3 = (txtarea.value).substring(selEnd, selLength);
		txtarea.value = s1 + wrap1 + s2 + wrap2 + s3;
		txtarea.selectionStart = s1.length;
		txtarea.selectionEnd = s1.length + s2.length + wrap1.length + wrap2.length;
		txtarea.scrollTop = oldScrollTop;
		txtarea.focus();
		return;
	} else {
		insertText(elname, wrap1 + wrap2);
	}
}

function insertText(elname, what) {
	if (document.forms['bbcode2text'].elements[elname].createTextRange) {
		document.forms['bbcode2text'].elements[elname].focus();
		document.selection.createRange().duplicate().text = what;
	} else if ((typeof document.forms['bbcode2text'].elements[elname].selectionStart) != 'undefined') { // for Mozilla
		var tarea = document.forms['bbcode2text'].elements[elname];
		var selEnd = tarea.selectionEnd;
		var txtLen = tarea.value.length;
		var txtbefore = tarea.value.substring(0,selEnd);
		var txtafter =  tarea.value.substring(selEnd, txtLen);
		var oldScrollTop = tarea.scrollTop;
		tarea.value = txtbefore + what + txtafter;
		tarea.selectionStart = txtbefore.length + what.length;
		tarea.selectionEnd = txtbefore.length + what.length;
		tarea.scrollTop = oldScrollTop;
		tarea.focus();
	} else {
		document.forms['bbcode2text'].elements[elname].value += what;
		document.forms['bbcode2text'].elements[elname].focus();
	}
}
function tag_url()
{
    var FoundErrors = '';
    var enterURL   = prompt("Please enter the url", "http://");
    var enterTITLE = prompt("please enter the url name", "My Webpage");

    if (!enterURL) {
        FoundErrors += " " + "No url entered";
    }
    if (!enterTITLE) {
        FoundErrors += " " + "No url name entered";
    }

    if (FoundErrors) {
        alert("Error!"+FoundErrors);
        return;
    }

	addText('body', '[url='+enterURL+']'+enterTITLE, '[/url]');
}


function tag_image()
{
    var FoundErrors = '';
    var enterURL   = prompt("Please enter the image url", "http://");

    if (!enterURL) {
        FoundErrors += " " + "No image url added";
    }

    if (FoundErrors) {
        alert("Error!"+FoundErrors);
        return;
    }

	addText('body', '[img]'+enterURL, '[/img]');
}



function tag_list()
{
	var listvalue = "init";
	var thelist = "";
	
	while ( (listvalue != "") && (listvalue != null) )
	{
		listvalue = prompt("list_prompt", "");
		if ( (listvalue != "") && (listvalue != null) )
		{
			thelist = thelist+"[*]"+listvalue+"\n";
		}
	}
	
	if ( thelist != "" )
	{
		addText('body', '[list]\n'+thelist, '[/list]\n');
	}
}



function alterfont(theval, thetag)
{
    if (theval == 0)
    	return;
	
	addText('body', '[' + thetag + '=' + theval + ']', '[/' + thetag + ']');

    document.bbcode2text.ffont.selectedIndex  = 0;
    document.bbcode2text.fsize.selectedIndex  = 0;
    document.bbcode2text.fcolor.selectedIndex = 0;
    
	
}



function more_emoticons()
{
  window.open('emoticonloader.php','Emoticons', 'width=300,height=500,resizable=yes,scrollbars=yes'); 
}

