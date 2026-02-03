//
/*The following function is not the original work of Ndimangwa,
It was taken from the book "JavaScript Cookbook" O'REILLY author Shelley Powers, published on July 2010
*/
function listenEvent(eventTarget, eventType, eventHandler)	{
	if (eventTarget.addEventListener)	{
		eventTarget.addEventListener(eventType, eventHandler, false);
	} else if (eventTarget.attachEvent)	{
		eventType = "on" + eventType;
		eventTarget.attachEvent(eventType, eventHandler);
	} else {
		eventType = "on" + eventType;
		eventTarget[eventType] = eventHandler;
	}
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Root(arg)	{
	
	this.setAccessKey = function(key)	{
		this.ref.setAttribute("accesskey", key);
	}
	
	this.getAccessKey = function()	{
		return this.ref.getAttribute("accesskey");
	}
	
	this.setClass = function(iclass)	{
		this.ref.setAttribute("class", iclass);
	}
	
	this.getClass = function()	{
		return this.ref.getAttribute("class");
	}
	
	this.setContentEditable = function(bln)	{
		this.ref.setAttribute("contenteditable", bln);
	}
	
	this.isContentEditable = function()	{
		return this.ref.getAttribute("contenteditable");
	}
	
	this.setContextMenu = function(menuId)	{
		this.ref.setAttribute("contextmenu", menuId);
	}
	
	this.getContextMenu = function()	{
		return this.ref.getAttribute("contextmenu");
	}
	
	this.setDir	= function(dir)	{
		this.ref.setAttribute("dir", dir);
	}
	
	this.getDir = function()	{
		return this.ref.getAttribute("dir");
	}
	
	this.setDraggable = function(bln)	{
		this.ref.setAttribute("draggable", bln);
	}
	
	this.isDraggable = function()	{
		return this.ref.getAttribute("draggable");
	}
	this.setDropZone = function(dropzone)	{
		this.ref.setAttribute("dropzone", dropzone);
	}
	this.getDropZone = function()	{
		return this.ref.getAttribute("dropzone");
	}
	this.setHidden = function(bln)	{
		this.ref.setAttribute("hidden", bln);
	}	
	this.isHidden = function()	{
		return this.ref.getAttribute("hidden");
	}
	this.setId = function(id)	{
		this.ref.setAttribute("id", id);
	}
	this.getId = function()	{
		return this.getAttribute("id");
	}
	this.setInert = function(bln)	{
		this.ref.setAttribute("inert", bln);
	}
	this.isInert = function()	{
		return this.ref.getAttribute("inert");
	}
	this.setLang = function(lang)	{
		this.ref.setAttribute("lang", lang);
	}
	this.getLang = function()	{
		return this.ref.getAttribute("lang");
	}
	this.setSpellCheck = function(bln)	{
		this.ref.setAttribute("spellcheck", bln);
	}
	this.isSpellCheck = function()	{
		return this.ref.getAttribute("spellcheck");
	}
	this.forceSpellCheck = function()	{
		this.ref.forceSpellCheck();
	}
	this.getStyle = function()	{
		return this.ref.getAttribute("style");
	}
	this.setStyle = function(style)	{
		if (this.ref.getAttribute("style"))	{
			this.ref.setAttribute("style", this.ref.getAttribute("style") + style);
		} else {
			this.ref.setAttribute("style", style);
		}
	}
	this.setTabIndex = function(tabindex)	{
		this.ref.setAttribute("tabindex", tabindex);
	}
	this.getTabIndex = function()	{
		return this.ref.getAttribute("tabindex");
	}
	this.setTitle = function(title)	{
		this.ref.setAttribute("title", title);
	}
	this.getTitle = function()	{
		return this.ref.getAttribute("title");
	}
	this.setTranslate = function(translate)	{
		this.ref.setAttribute("translate", translate);
	}
	this.getTranslate = function()	{
		return this.ref.getAttribute("translate");
	}
	//Event Handling
	this.onabort = function(fx)	{
		listenEvent(this.ref, "abort", fx);
	}
	this.onblur = function(fx)	{
		listenEvent(this.ref, "blur", fx);
	}
	this.oncancel = function(fx)	{
		listenEvent(this.ref, "cancel", fx);
	}
	this.oncanplay = function(fx)	{
		listenEvent(this.ref, "canplay", fx);
	}
	this.oncanplaythrough = function(fx)	{
		listenEvent(this.ref, "canplaythrough", fx);
	}
	this.onchange = function(fx)	{
		listenEvent(this.ref, "change", fx);
	}
	this.onclick = function(fx)	{
		listenEvent(this.ref, "click", fx);
	}
	this.onclose = function(fx)	{
		listenEvent(this.ref, "close", fx);
	}
	this.oncontextmenu = function(fx)	{
		listenEvent(this.ref, "contextmenu", fx);
	}
	this.oncuechange = function(fx)	{
		listenEvent(this.ref, "cuechange", fx);
	}
	this.ondblclick = function(fx)	{
		listenEvent(this.ref, "dblclick", fx);
	}
	this.ondrag = function(fx)	{
		listenEvent(this.ref, "drag", fx);
	}
	this.ondragend = function(fx)	{
		listenEvent(this.ref, "dragend", fx);
	}
	this.ondragend = function(fx)	{
		listenEvent(this.ref, "dragend", fx);
	}
	this.ondragenter = function(fx)	{
		listenEvent(this.ref, "dragenter", fx);
	}
	this.ondragexit = function(fx)	{
		listenEvent(this.ref, "dragexit", fx);
	}
	this.ondragleave = function(fx)	{
		listenEvent(this.ref, "dragleave", fx);
	}
	this.ondragover = function(fx)	{
		listenEvent(this.ref, "dragover", fx);
	}
	this.ondragstart = function(fx)	{
		listenEvent(this.ref, "dragstart", fx);
	}
	this.ondrop = function(fx)	{
		listenEvent(this.ref, "drop", fx);
	}
	this.ondurationchange = function(fx)	{
		listenEvent(this.ref, "durationchange", fx);
	}
	this.onemptied = function(fx)	{
		listenEvent(this.ref, "emptied", fx);
	}
	this.onended = function(fx)	{
		listenEvent(this.ref, "ended", fx);
	}
	this.onerror = function(fx)	{
		listenEvent(this.ref, "error", fx);
	}
	this.onfocus = function(fx)	{
		listenEvent(this.ref, "focus", fx);
	}
	this.oninput = function(fx)	{
		listenEvent(this.ref, "input", fx);
	}
	this.oninvalid = function(fx)	{
		listenEvent(this.ref, "invalid", fx);
	}
	this.onkeydown = function(fx)	{
		listenEvent(this.ref, "keydown", fx);
	}
	this.onkeypress = function(fx)	{
		listenEvent(this.ref, "keypress", fx);
	}
	this.onkeyup = function(fx)	{
		listenEvent(this.ref, "keyup", fx);
	}
	this.onload = function(fx)	{
		listenEvent(this.ref, "load", fx);
	}
	this.onloadeddata = function(fx)	{
		listenEvent(this.ref, "loadeddata", fx);
	}
	this.onloadedmetadata = function(fx)	{
		listenEvent(this.ref, "loadedmetadata", fx);
	}
	this.onloadstart = function(fx)	{
		listenEvent(this.ref, "loadstart", fx);
	}
	this.onmousedown = function(fx)	{
		listenEvent(this.ref, "mousedown", fx);
	}
	this.onmouseenter = function(fx)	{
		listenEvent(this.ref, "mouseenter", fx);
	}
	this.onmouseleave = function(fx)	{
		listenEvent(this.ref, "mouseleave", fx);
	}
	this.onmousemove = function(fx)	{
		listenEvent(this.ref, "mousemove", fx);
	}
	this.onmouseout = function(fx)	{
		listenEvent(this.ref, "mouseout", fx);
	}
	this.onmouseover = function(fx)	{
		listenEvent(this.ref, "mouseover", fx);
	}
	this.onmouseup = function(fx)	{
		listenEvent(this.ref, "mouseup", fx);
	}
	this.onmousewheel = function(fx)	{
		listenEvent(this.ref, "mousewheel", fx);
	}
	this.onpause = function(fx)	{
		listenEvent(this.ref, "pause", fx);
	}
	this.onplay = function(fx)	{
		listenEvent(this.ref, "play", fx);
	}
	this.onplaying = function(fx)	{
		listenEvent(this.ref, "playing", fx);
	}
	this.onprogress = function(fx)	{
		listenEvent(this.ref, "progress", fx);
	}
	this.onratechange = function(fx)	{
		listenEvent(this.ref, "ratechange", fx);
	}
	this.onreset = function(fx)	{
		listenEvent(this.ref, "reset", fx);
	}
	this.onscroll = function(fx)	{
		listenEvent(this.ref, "scroll", fx);
	}
	this.onseekend = function(fx)	{
		listenEvent(this.ref, "seekend", fx);
	}
	this.onseeking = function(fx)	{
		listenEvent(this.ref, "seeking", fx);
	}
	this.onselect = function(fx)	{
		listenEvent(this.ref, "select", fx);
	}
	this.onshow = function(fx)	{
		listenEvent(this.ref, "show", fx);
	}
	this.onsort = function(fx)	{
		listenEvent(this.ref, "sort", fx);
	}
	this.onstalled = function(fx)	{
		listenEvent(this.ref, "stalled", fx);
	}
	this.onsubmit = function(fx)	{
		listenEvent(this.ref, "submit", fx);
	}
	this.onsuspend = function(fx)	{
		listenEvent(this.ref, "suspend", fx);
	}
	this.ontimeupdate = function(fx)	{
		listenEvent(this.ref, "timeupdate", fx);
	}
	this.onvolumechange = function(fx)	{
		listenEvent(this.ref, "volumechange", fx);
	}
	this.onwaiting = function(fx)	{
		listenEvent(this.ref, "waiting", fx);
	}
	// end of event handler
	this.me = function()	{
		return this.ref;
	}
	// note ref will contain a reference to an element
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function InnerHTML(arg)	{
	if (typeof arg === "string")	{
		this.ref.appendChild(document.createTextNode(arg));
	} else if ((typeof arg === "object") && (arg.me))	{
		this.ref.appendChild(arg.me());
	}
	this.add = function(ele1)	{
		this.ref.innerHTML = "";
		if (typeof ele1 === "string")	{
			this.ref.appendChild(document.createTextNode(ele1));
		} else if ((typeof ele1 === "object") && (ele1.me))	{
			this.ref.appendChild(ele1.me());
		}
	}
	this.append = function(ele1)	{
		if (typeof ele1 === "string")	{
			this.ref.appendChild(document.createTextNode(ele1));
		} else if ((typeof ele1 === "object") && (ele1.me))	{
			this.ref.appendChild(ele1.me());
		}
	}
	Root.apply(this, arguments);
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Html(arg)	{
	this.ref = document.createElement("html");
	InnerHTML.apply(this, arguments);
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Head(arg)	{
	this.ref = document.createElement("head");
	InnerHTML.apply(this, arguments);
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Title(title)	{
	this.ref = document.createElement("title");
	InnerHTML.apply(this, arguments);
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Base(arg)	{
	this.ref = document.createElement("base");
	Root.apply(this, arguments);	
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Link(arg)	{
	this.ref = document.createElement("link");
	Root.apply(this, arguments);
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Meta(arg)	{
	this.ref = document.createElement("meta");
	Root.apply(this, arguments);
}
/*The following function was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Style(arg)	{
	this.ref = document.createElement("style");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Script(arg)	{
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setType = function(type1)	{
		this.ref.setAttribute("type", type1);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.setCharSet = function(charset1)	{
		this.ref.setAttribute("charset", charset1);
	}
	this.getCharSet = function()	{
		return this.ref.getAttribute("charset");
	}
	this.setAsync = function(async)	{
		this.ref.setAttribute("async", async);
	}
	this.isAsync = function()	{
		return this.ref.getAttribute("async");
	}
	this.setDefer = function(defer)	{
		this.ref.setAttribute("defer", defer);
	}
	this.isDefer = function()	{
		return this.ref.getAttribute("defer");
	}
	this.setCrossOrigin = function(crossorigin)	{
		this.ref.setAttribute("crossorigin", crossorigin);
	}
	this.getCrossOrigin = function()	{
		return this.ref.getAttribute("crossorigin");
	}
	this.ref = document.createElement("script");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function NoScript(arg) {
	this.ref = document.createElement("noscript");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Body(arg)	{
	this.onafterprint = function(fx)	{
		listenEvent(this.ref, "afterprint", fx);
	}
	this.onbeforeprint = function(fx)	{
		listenEvent(this.ref, "beforeprint", fx);
	}
	this.onbeforeunload = function(fx)	{
		listenEvent(this.ref, "beforeunload", fx);
	}
	this.onfullscreenchange = function(fx)	{
		listenEvent(this.ref, "fullscreenchange", fx);
	}
	this.onhaschange = function(fx)	{
		listenEvent(this.ref, "haschange", fx);
	}
	this.onmessage = function(fx)	{
		listenEvent(this.ref, "message", fx);
	}
	this.onoffline = function(fx)	{
		listenEvent(this.ref, "offline", fx);
	}
	this.ononline = function(fx)	{
		listenEvent(this.ref, "online", fx);
	}
	this.onpagehide = function(fx)	{
		listenEvent(this.ref, "pagehide", fx);
	}
	this.onpageshow = function(fx)	{
		listenEvent(this.ref, "pageshow", fx);
	}
	this.onpopstate = function(fx)	{
		listenEvent(this.ref, "popstate", fx);
	}
	this.onresize = function(fx)	{
		listenEvent(this.ref, "resize", fx);
	}
	this.onstorage = function(fx)	{
		listenEvent(this.ref, "storage", fx);
	}
	this.onunload = function(fx)	{
		listenEvent(this.ref, "unload", fx);
	}
	this.ref = document.createElement("body");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Article(arg)	{
	this.ref = document.createElement("article");
	InnerHTML.apply(this, arguments);
}
function Section(arg)	{
	this.ref = document.createElement("section");
	InnerHTML.apply(this, arguments);
}
function Nav(arg)	{
	this.ref = document.createElement("nav");
	InnerHTML.apply(this, arguments);
}
function Aside(arg)	{
	this.ref = document.createElement("aside");
	InnerHTML.apply(this, arguments);
}
function H1(arg)	{
	this.ref = document.createElement("h1");
	InnerHTML.apply(this, arguments);
}
function H2(arg)	{
	this.ref = document.createElement("h2");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function H3(arg)	{
	this.ref = document.createElement("h3");
	InnerHTML.apply(this, arguments);
}
function H4(arg)	{
	this.ref = document.createElement("h4");
	InnerHTML.apply(this, arguments);
}
function H5(arg)	{
	this.ref = document.createElement("h5");
	InnerHTML.apply(this, arguments);
}
function H6(arg)	{
	this.ref = document.createElement("h6");
	InnerHTML.apply(this, arguments);
}
function Header(arg)	{
	this.ref = document.createElement("header");
	InnerHTML.apply(this, arguments);
}
function Footer(arg)	{
	this.ref = document.createElement("footer");
	InnerHTML.apply(this, arguments);
}
function Address(arg)	{
	this.ref = document.createElement("address");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function P(arg)	{
	this.ref = document.createElement("p");
	InnerHTML.apply(this, arguments);
}
function Hr(arg)	{
	this.ref = document.createElement("hr");
	Root.apply(this, arguments);
}
function Pre(arg)	{
	this.ref = document.createElement("pre");
	InnerHTML.apply(this, arguments);
}
function Blockquote(arg)	{
	this.ref = document.createElement("blockquote");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Ol(arg)	{
	this.setReversed = function(bln)	{
		this.ref.reversed = bln;
	}
	this.isReversed = function()	{
		return this.ref.reversed;
	}
	this.setStart = function(str)	{
		this.ref.setAttribute("start", str);
	}
	this.getStart = function()	{
		return this.ref.getAttribute("start");
	}
	this.setType = function(typ)	{
		this.ref.setAttribute("type", typ);
	}
	this.getType = function()	{
		this.ref.getAttribute("type");
	}
	this.ref = document.createElement("ol");
	InnerHTML.apply(this, arguments);
}
function Ul(arg)	{
	this.ref = document.createElement("ul");
	InnerHTML.apply(this, arguments);
}
function Li(arg)	{
	/* if defined inside Ol then values works */
	this.setValue = function(value)	{
		this.ref.setAttribute("value", value);
	}
	this.getValue = function()	{
		return this.ref.getAttribute("value");
	}
	this.ref = document.createElement("li");
	InnerHTML.apply(this, arguments);
}
function Dl(arg)	{
	this.ref = document.createElement("dl");
	InnerHTML.apply(this, arguments);
}
function Dt(arg)	{
	this.ref = document.createElement("dt");
	InnerHTML.apply(this, arguments);
}
function Dd(arg)	{
	this.ref = document.createElement("dd");
	InnerHTML.apply(this, arguments);
}
function Figure(arg)	{
	this.ref = document.createElement("figure");
	InnerHTML.apply(this, arguments);
}
function Figcaption(arg)	{
	this.ref = document.createElement("figcaption");
	InnerHTML.apply(this, arguments);
}
function Div(arg)	{
	this.ref = document.createElement("div");
	InnerHTML.apply(this, arguments);
}
function Main(arg)	{
	this.ref = document.createElement("main");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function A(arg)	{
	this.setHref = function(href)	{
		this.ref.setAttribute("href", href);
	}	
	this.getHref = function()	{
		return this.ref.getAttribute("href");
	}
	this.setTarget = function(targ)	{
		this.ref.setAttribute("target", targ);
	}
	this.getTarget = function()	{
		return this.ref.getAttribute("target");
	}
	this.setDownload = function(dwn)	{
		this.ref.setAttribute("download", dwn);
	}
	this.getDownload = function()	{
		return this.ref.getAttribute("download");
	}
	this.setRel = function(rel)	{
		this.ref.setAttribute("rel", rel);
	}
	this.getRel = function()	{
		return this.ref.getAttribute("rel");
	}
	this.setHrefLang = function(lang)	{
		this.ref.setAttribute("hreflang", lang);
	}
	this.getHrefLang = function()	{
		return this.ref.getAttribute("hreflang");
	}
	this.setType = function(type)	{
		this.ref.setAttribute("type", type);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.ref = document.createElement("a");
	InnerHTML.apply(this, arguments);
}
function Em(arg)	{
	this.ref = document.createElement("em");
	InnerHTML.apply(this, arguments);
}
function Small(arg)	{
	this.ref = document.createElement("small");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function S(arg)	{
	this.ref = document.createElement("s");
	InnerHTML.apply(this, arguments);
}
function Cite(arg)	{
	this.ref = document.createElement("cite");
	InnerHTML.apply(this, arguments);
}
function Q(arg)	{
	this.ref = document.createElement("q");
	InnerHTML.apply(this, arguments);
}
function Dfn(arg)	{
	this.ref = document.createElement("dfn");
	InnerHTML.apply(this, arguments);
}
function Data(arg)	{
	this.setValue = function(value)	{
		this.ref.setAttribute("value", value);
	}
	this.getValue = function()	{
		return this.ref.getAttribute("value");
	}
	this.ref = document.createElement("data");
	InnerHTML.apply(this, arguments);
}
function Time(arg)	{
	this.setDateTime = function(dateti)	{
		this.ref.setAttribute("datetime", dateti);
	}	
	this.getDateTime = function()	{
		return this.ref.getAttribute("datetime");
	}
	this.ref = document.createElement("time");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Code(arg)	{
	this.ref = document.createElement("code");
	InnerHTML.apply(this, arguments);
}
function Var(arg)	{
	this.ref = document.createElement("var");
	InnerHTML.apply(this, arguments);
}
function Samp(arg)	{
	this.ref = document.createElement("samp");
	InnerHTML.apply(this, arguments);
}
function Kbd(arg)	{
	this.ref = document.createElement("kbd");
	InnerHTML.apply(this, arguments);
}
function Sub(arg)	{
	this.ref = document.createElement("sub");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Sup(arg)	{
	this.ref = document.createElement("sup");
	InnerHTML.apply(this, arguments);
}
function I(arg)	{
	this.ref = document.createElement("i");
	InnerHTML.apply(this, arguments);
}
function B(arg)	{
	this.ref = document.createElement("b");
	InnerHTML.apply(this, arguments);
}
function U(arg)	{
	this.ref = document.createElement("u");
	InnerHTML.apply(this, arguments);
}
function Mark(arg)	{
	this.ref = document.createElement("mark");
	InnerHTML.apply(this, arguments);
}
function Ruby(arg)	{
	this.ref = document.createElement("ruby");
	InnerHTML.apply(this, arguments);
}
function Rt(arg)	{
	this.ref = document.createElement("rt");
	InnerHTML.apply(this, arguments);
}
function Rp(arg)	{
	this.ref = document.createElement("rp");
	InnerHTML.apply(this, arguments);
}
function Bdi(arg)	{
	this.ref = document.createElement("bdi");
	InnerHTML.apply(this, arguments);
}
function Bdo(arg)	{
	this.ref = document.createElement("bdo");
	InnerHTML.apply(this, arguments);
}
/*The following function(s) was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Span(arg)	{
	this.ref = document.createElement("span");
	InnerHTML.apply(this, arguments);
}
function Br(arg)	{
	this.ref = document.createElement("br");
	Root.apply(this, arguments);
}
function Wbr(arg)	{
	this.ref = document.createElement("wbr");
	Root.apply(this, arguments);
}
function Edits(arg)	{
	this.setCite = function(cite)	{
		this.ref.setAttribute("cite", cite);
	}
	this.getCite = function()	{
		return this.ref.getAttribute("cite");
	}
	this.setDateTime = function(datetime)	{
		this.ref.setAttribute("datetime", datetime);
	}
	this.getDateTime = function()	{
		return this.ref.getAttribute("datetime");
	}
	InnerHTML.apply(this, arguments);
}
function Ins(arg)	{
	this.ref = document.createElement("ins");
	Edits.apply(this, arguments);
}
function Del(arg)	{
	this.ref = document.createElement("del");
	Edits.apply(this, arguments);
}
function Img(arg){
	this.setAlt = function(alt)	{
		this.ref.setAttribute("alt", alt);
	}
	this.getAlt = function()	{
		return this.ref.getAttribute("alt");
	}
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setCrossOrigin = function(crss)	{
		this.ref.setAttribute("crossorigin", crss);
	}
	this.getCrossOrigin = function()	{
		return this.ref.getAttribute("crossorigin");
	}
	this.setUseMap = function(usemap)	{
		this.ref.setAttribute("usemap", usemap);
	}
	this.getUseMap = function()	{
		return this.ref.getAttribute("usemap");
	}
	this.setIsMap = function(imap)	{
		this.ref.setAttribute("ismap", imap);
	}
	this.isIsMap = function()	{
		return this.ref.getAttribute("ismap");
	}
	this.setWidth = function(width)	{
		this.ref.setAttribute("width", width);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.setHeight = function(height)	{
		this.ref.setAttribute("height", height);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.ref = document.createElement("img");
	Root.apply(this, arguments);
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Iframe(arg)	{
	this.setSrc = function(src) {
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setSrcDoc = function(src)	{
		this.ref.setAttribute("srcdoc", src);
	}
	this.getSrcDoc = function()	{
		return this.ref.getAttribute("srcdoc");
	}
	this.setName = function(iname)	{
		this.ref.setAttribute("name", iname);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setSandBox = function(snd)	{
		this.ref.setAttribute("sandbox", snd);
	}
	this.getSandBox = function()	{
		return this.ref.getAttribute("sandbox");
	}
	this.setSeamless = function(bln)	{
		this.ref.setAttribute("seamless", bln);
	}
	this.isSeamless = function()	{
		return this.ref.getAttribute("seamless");
	}
	this.setAllowFullScreen = function(bln)	{
		this.ref.setAttribute("allowfullscreen", bln);
	}
	this.isAllowFullScreen = function()	{
		return this.ref.getAttribute("allowfullscreen");
	}
	this.setWidth = function(width)	{
		this.ref.setAttribute("width", width);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.setHeight = function(height)	{
		this.ref.setAttribute("height", height);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.ref = document.createElement("iframe");
	InnerHTML.apply(this, arguments);
}
function Embed(arg)	{
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src)
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setType = function(type)	{
		this.ref.setAttribute("type", type);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.setWidth = function(width)	{
		this.ref.setAttribute("width", width);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.setHeight = function(height)	{
		this.ref.setAttribute("height", height);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.ref = document.createElement("embed");
	Root.apply(this, arguments);
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Object(arg)	{
	this.setData = function(data)	{
		this.ref.setAttribute("data", data);
	}
	this.getData = function()	{
		return this.ref.getAttribute("data");
	}
	this.setType = function(type)	{
		this.ref.setAttribute("type", type);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.setTypeMustMatch = function(bln)	{
		this.ref.setAttribute("typemustmatch", bln);
	}
	this.isTypeMustMatch = function()	{
		return this.ref.getAttribute("typemustmatch");
	}
	this.setName = function(name)	{
		this.ref.setAttribute("name", name);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setUseMap = function(umap)	{
		this.ref.setAttribute("usemap", umap);
	}
	this.getUseMap = function()	{
		return this.ref.getAttribute("usemap");
	}
	this.setForm = function(form1)	{
		this.ref.setAttribute("form", form1);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setWidth = function(width)	{
		this.ref.setAttribute("width", width);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.setHeight = function(height)	{
		this.ref.setAttribute("height", height);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.ref = document.createElement("object");
	InnerHTML.apply(this, arguments);
}
function Param(arg)	{
	this.setName = function(name)	{
		this.ref.setAttribute("name", name);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setValue = function(value)	{
		this.ref.setAttribute("value", value);
	}
	this.getValue = function()	{
		return this.ref.getAttribute("value");
	}
	this.ref = document.createElement("param");
	Root.apply(this, arguments);
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Media(arg)	{
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setPreLoad = function(pload)	{
		this.ref.setAttribute("preload", pload);
	}
	this.getPreLoad = function()	{
		this.ref.getAttribute("preload");
	}
	this.setAutoPlay = function(aplay)	{
		this.ref.setAttribute("autoplay", aplay);
	}
	this.getAutoPlay = function()	{
		return this.ref.getAttribute("autoplay");
	}
	this.setMediaGroup = function(mgroup)	{
		this.ref.setAttribute("mediagroup", mgroup);
	}
	this.getMediaGroup = function()	{
		return this.ref.getAttribute("mediagroup");
	}
	this.setLoop = function(iloop)	{
		this.ref.setAttribute("loop", iloop);
	}
	this.isLoop = function()	{
		return this.ref.getAttribute("loop");
	}
	this.setMuted = function(bln)	{
		this.ref.setAttribute("muted", bln);
	}
	this.isMuted = function()	{
		return this.ref.getAttribute("muted");
	}
	this.setControls = function(ctr)	{
		this.ref.setAttribute("controls", ctr);
	}
	this.getControls = function()	{
		return this.ref.getAttribute("controls");
	}
	InnerHTML.apply(this, arguments);
}
function Video(arg)	{
	this.setCrossOrigin = function(ico)	{
		this.ref.setAttribute("crossorigin", ico);
	}
	this.getCrossOrigin = function()	{
		return this.ref.getAttribute("crossorigin");
	}
	this.setPoster = function(iposter)	{
		this.ref.setAttribute("poster", iposter);
	}
	this.getPoster = function()	{
		return this.ref.getAttribute("poster");
	}
	this.setWidth = function(width)	{
		this.ref.setAttribute("width", width);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.setHeight = function(height)	{
		this.ref.setAttribute("height", height);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.ref = document.createElement("video");
	Media.apply(this, arguments);
}
function Audio(arg)	{
	this.setCrossOrigin = function(ico)	{
		this.ref.setAttribute("crossorigin", ico);
	}
	this.getCrossOrigin = function()	{
		return this.ref.getAttribute("crossorigin");
	}
	this.ref = document.createElement("audio");
	Media.apply(this, arguments);
}
function Source(arg)	{
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setType = function(type)	{
		this.ref.setAttribute("type", type);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.setMedia = function(media)	{
		this.ref.setAttribute("media", media);
	}
	this.getMedia = function()	{
		return this.ref.getAttribute("media");
	}
	this.ref = document.createElement("source");
	Root.apply(this, arguments);
}
function Track(arg)	{
	this.setKind = function(kind)	{
		this.ref.setAttribute("kind", kind);
	}
	this.getKind = function()	{
		return this.ref.getAttribute("kind");
	}
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setSrcLang = function(lang)	{
		this.ref.setAttribute("srclang", lang);
	}
	this.getSrcLang = function()	{
		return this.ref.getAttribute("srclang");
	}
	this.setLabel = function(label)	{
		this.ref.setAttribute("label", label);
	}
	this.getLabel = function()	{
		return this.ref.getAttribute("label");
	}
	this.setDefault = function(bln)	{
		this.ref.setAttribute("default", bln);
	}
	this.isDefault = function()	{
		return this.ref.getAttribute("default");
	}
	this.ref = document.createElement("track");
	Root.apply(this, arguments);
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Canvas(arg)	{
	this.setWidth = function(width)	{
		this.ref.setAttribute("width", width);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.setHeight = function(height)	{
		this.ref.setAttribute("height", height);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.ref = document.createElement("canvas");
	InnerHTML.apply(this, arguments);
}
function Map(arg)	{
	this.setName = function(iname)	{
		this.ref.setAttribute("name", iname);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.ref = document.createElement("map");
	InnerHTML.apply(this, arguments);
}
function Area(arg)	{
	this.setAlt = function(alt)	{
		this.ref.setAttribute("alt", alt);
	}
	this.getAlt = function()	{
		return this.ref.getAttribute("alt");
	}
	this.setCoords = function(coor)	{
		this.ref.setAttribute("coords", coor);
	}
	this.getCoords = function()	{
		return this.ref.getAttribute("coords");
	}
	this.setShape = function(shape)	{
		this.ref.setAttribute("shape", shape);
	}
	this.getShape = function()	{
		return this.ref.getAttribute("shape");
	}
	this.setHref = function(href)	{
		this.ref.setAttribute("href", href);
	}
	this.getHref = function()	{
		return this.ref.getAttribute("href");
	}
	this.setTarget = function(targ)	{
		this.ref.setAttribute("target", targ);
	}
	this.getTarget = function()	{
		return this.ref.getAttribute("target");
	}
	this.setDownload = function(down)	{
		this.ref.setAttribute("download", down);
	}
	this.getDownload = function()	{
		return this.ref.getAttribute("download");
	}
	this.setRel = function(rel)	{
		this.ref.setAttribute("rel", rel);
	}
	this.getRel = function()	{
		return this.ref.getAttribute("rel");
	}
	this.setHrefLang = function(lang)	{
		this.ref.setAttribute("hreflang", lang);
	}
	this.getHrefLang = function()	{
		return this.ref.getAttribute("hreflang");
	}
	this.setType = function(type)	{
		this.ref.setAttribute("type", type);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.ref = document.createElement("area");
	Root.apply(this, arguments);
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Table(arg)	{
	this.setBorder = function(border)	{
		this.ref.setAttribute("border", border);
	}
	this.getBorder = function()	{
		return this.ref.getAttribute("border");
	}
	this.setSortable = function(bln)	{
		this.ref.setAttribute("sortable", bln);
	}
	this.isSortable = function()	{
		return this.ref.getAttribute("sortable");
	}
	this.ref = document.createElement("table");
	InnerHTML.apply(this, arguments);
}
function Caption(arg)	{
	this.ref = document.createElement("caption");
	InnerHTML.apply(this, arguments);
}
function t807column(arg)	{
	this.setSpan = function(span)	{
		this.ref.setAttribute("span", span);
	}
	this.getSpan = function()	{
		return this.ref.getAttribute("span");
	}
	InnerHTML.apply(this, arguments);
}
function Colgroup(arg)	{
	this.ref = document.createElement("colgroup");
	t807column.apply(this, arguments);
}
function Col(arg)	{
	this.ref = document.createElement("col");
	t807column.apply(this, arguments);
}
function Tbody(arg)	{
	this.ref = document.createElement("tbody");
	InnerHTML.apply(this, arg);
}
function Thead(arg)	{
	this.ref = document.createElement("thead");
	InnerHTML.apply(this, arg);
}
function Tfoot(arg)	{
	this.ref = document.createElement("tfoot");
	InnerHTML.apply(this, arg);
}
function Tr(arg)	{
	this.ref = document.createElement("tr");
	InnerHTML.apply(this, arg);
}
function t807tabledata(arg)	{
	this.setColSpan = function(cls)	{
		this.ref.setAttribute("colspan", cls);
	}
	this.getColSpan = function()	{
		return this.ref.getAttribute("colspan");
	}
	this.setRowSpan = function(rls)	{
		this.ref.setAttribute("rowspan", rls);
	}
	this.getRowSpan = function()	{
		return this.ref.getAttribute("rowspan");
	}
	this.setHeaders = function(hds)	{
		this.ref.setAttribute("headers", hds);
	}
	this.getHeaders = function()	{
		return this.ref.getAttribute("headers");
	}
	InnerHTML.apply(this, arguments);
}
function Td(arg)	{
	this.ref = document.createElement("td");
	t807tabledata.apply(this, arguments);
}
function Th(arg)	{
	this.setScope = function(scp)	{
		this.ref.setAttribute("scope", scp);
	}
	this.getScope = function()	{
		return this.ref.getAttribute("scope");
	}
	this.setAbbr = function(abbr)	{
		this.ref.setAttribute("abbr", abbr);
	}
	this.getAbbr = function()	{
		return this.ref.getAttribute("abbr");
	}
	this.setSorted = function(sort1)	{
		this.ref.setAttribute("sorted", sort1);
	}
	this.getSorted = function()	{
		return this.ref.getAttribute("sorted");
	}
	this.ref = document.createElement("th");
	t807tabledata.apply(this, arguments);
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Form(arg)	{
	this.setAcceptCharset = function(chr)	{
		this.ref.setAttribute("accept-charset", chr);
	}
	this.getAcceptCharset = function()	{
		return this.ref.getAttribute("accept-charset");
	}
	this.setAction = function(act)	{
		this.ref.setAttribute("action", act);
	}
	this.getAction = function()	{
		return this.ref.getAttribute("action");
	}
	this.setAutoComplete = function(auto1)	{
		this.ref.setAttribute("autocomplete", auto1);
	}
	this.getAutoComplete = function()	{
		return this.ref.getAttribute("autocomplete");
	}
	this.setEncType = function(enc1)	{
		this.ref.setAttribute("enctype", enc1);
	}
	this.getEnType = function()	{
		return this.ref.getAttribute("entype");
	}
	this.setMethod = function(mtd)	{
		this.ref.setAttribute("method", mtd);
	}
	this.getMethod = function()	{
		return this.ref.getAttribute("method");
	}
	this.setName = function(name1)	{
		this.ref.setAttribute("name", name1);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setNoValidate = function(bln)	{
		this.ref.setAttribute("novalidate", bln);
	}
	this.isNoValidate = function()	{
		return this.ref.getAttribute("novalidate");
	}
	this.setTarget = function(trgt)	{
		this.ref.setAttribute("target", trgt);
	}
	this.getTarget = function()	{
		return this.ref.getAttribute("target");
	}
	this.ref = document.createElement("form");
	InnerHTML.apply(this, arguments);
}
function Fieldset(arg)	{
	this.setDisabled = function(bln)	{
		this.ref.setAttribute("disabled", bln);
	}
	this.isDisabled = function()	{
		return this.ref.getAttribute("disabled");
	}
	this.setForm = function(form1)	{
		this.ref.setAttribute("form", form1);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setName = function(iname)	{
		this.ref.setAttribute("name", iname);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.ref = document.createElement("fieldset");
	InnerHTML.apply(this, arguments);
}
function Legend(arg)	{
	this.ref = document.createElement("legend");
	InnerHTML.apply(this, arguments);
}
function Label(arg)	{
	this.setForm = function(frm)	{
		this.ref.setAttribute("form", frm);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setFor = function(fr1)	{
		this.ref.setAttribute("for", fr1);
	}
	this.getFor = function()	{
		return this.ref.getAttribute("for");
	}
	this.ref = document.createElement("label");
	InnerHTML.apply(this, arguments);
}
function Input(arg) {
	this.setAccept = function(acc)	{
		this.ref.setAttribute("accept", acc);
	}
	this.getAccept = function()	{
		return this.ref.getAttribute("accept");
	}
	this.setAlt = function(alt)	{
		this.ref.setAttribute("alt", alt);
	}
	this.getAlt = function()	{
		return this.ref.getAttribute("alt");
	}
	this.setAutoComplete = function(aut)	{
		this.ref.setAttribute("autocomplete", aut);
	}
	this.getAutoComplete = function()	{
		return this.ref.getAttribute("autocomplete");
	}
	this.setAutoFocus = function(fcs)	{
		this.ref.setAttribute("autofocus", fcs);
	}
	this.isAutoFocus = function()	{
		return this.ref.getAttribute("autofocus");
	}
	this.setChecked = function(chk)	{
		this.ref.setAttribute("checked", chk);
	}
	this.isChecked = function()	{
		return this.ref.getAttribute("checked");
	}
	this.setDirName = function(di)	{
		this.ref.setAttribute("dirname", di);
	}
	this.getDirName = function()	{
		return this.ref.getAttribute("dirname");
	}
	this.setDisabled = function(bln)	{
		this.ref.setAttribute("disabled", bln);
	}
	this.isDisabled = function()	{
		return this.ref.getAttribute("disabled");
	}
	this.setForm = function(fr1)	{
		this.ref.setAttribute("form", fr1);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setFormAction = function(fr1)	{
		this.ref.setAttribute("formaction", fr1);
	}
	this.getFormAction = function()	{
		return this.ref.getAttribute("formaction");
	}
	this.setFormEncType = function(enc)	{
		this.ref.setAttribute("formenctype", enc);
	}
	this.getFormEncType = function()	{
		return this.ref.getAttribute("formenctype");
	}
	this.setFormMethod = function(meth)	{
		this.ref.setAttribute("formmethod", meth);
	}
	this.getFormMethod = function()	{
		return this.ref.getAttribute("formmethod");
	}
	this.setFormNoValidate = function(bln)	{
		this.ref.setAttribute("formnovalidate", bln);
	}
	this.isFormNoValidate = function()	{
		return this.ref.getAttribute("formnovalidate");
	}
	this.setFormTarget = function(tar1)	{
		this.ref.setAttribute("formtarget", tar1);
	}
	this.getFormTarget = function()	{
		return this.ref.getAttribute("formtarget");
	}
	this.setHeight = function(ht)	{
		this.ref.setAttribute("height", ht);
	}
	this.getHeight = function()	{
		return this.ref.getAttribute("height");
	}
	this.setInputMode = function(imode)	{
		this.ref.setAttribute("inputmode", imode);
	}
	this.getInputMode = function()	{
		return this.ref.getAttribute("inputmode");
	}
	this.setList = function(ilist)	{
		this.ref.setAttribute("list", ilist);
	}
	this.getList = function()	{
		return this.ref.getAttribute("list");
	}
	this.setMax = function(max1)	{
		this.ref.setAttribute("max", max1);
	}
	this.getMax = function()	{
		return this.ref.getAttribute("max");
	}
	this.setMaxLength = function(max1)	{
		this.ref.setAttribute("maxlength", max1);
	}
	this.getMaxLength = function()	{
		return this.ref.getAttribute("maxlength");
	}
	this.setMin = function(min1)	{
		this.ref.setAttribute("min", min1);
	}
	this.getMin = function()	{
		return this.ref.getAttribute("min");
	}
	this.setMultiple = function(mlt)	{
		this.ref.setAttribute("multiple", mlt);
	}
	this.isMultiple = function()	{
		return this.ref.getAttribute("multiple");
	}
	this.setName = function(iname)	{
		this.ref.setAttribute("name", iname);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setPattern = function(rgx)	{
		this.ref.setAttribute("pattern", rgx);
	}
	this.getPattern = function()	{
		return this.ref.getAttribute("pattern");
	}
	this.setPlaceHolder = function(pholder)	{
		this.ref.setAttribute("placeholder", pholder);
	}
	this.getPlaceHolder = function()	{
		return this.ref.getAttribute("placeholder");
	}
	this.setReadOnly = function(bln)	{
		this.ref.setAttribute("readonly", bln);
	}
	this.getReadOnly = function()	{
		return this.ref.getAttribute("readonly");
	}
	this.setRequired = function(bln)	{
		this.ref.setAttribute("required", bln);
	}
	this.getRequired = function()	{
		return this.ref.getAttribute("required");
	}
	this.setSize = function(isize)	{
		this.ref.setAttribute("size", isize);
	}
	this.getSize = function()	{
		return this.ref.getAttribute("size");
	}
	this.setSrc = function(src)	{
		this.ref.setAttribute("src", src);
	}
	this.getSrc = function()	{
		return this.ref.getAttribute("src");
	}
	this.setStep = function(stp)	{
		this.ref.setAttribute("step", stp);
	}
	this.getStep = function()	{
		return this.ref.getAttribute("step");
	}
	this.setType = function(typ)	{
		this.ref.setAttribute("type", typ);
	}
	this.getType = function()	{
		return this.ref.getAttribute("type");
	}
	this.setValue = function(ival)	{
		this.ref.setAttribute("value", ival);
	}
	this.getValue = function()	{
		return this.ref.getAttribute("value");
	}
	this.setWidth = function(iwdt)	{
		this.ref.setAttribute("width", iwdt);
	}
	this.getWidth = function()	{
		return this.ref.getAttribute("width");
	}
	this.ref = document.createElement("input");
	Root.apply(this, arguments);
	if (typeof arg === "string")	{
		this.ref.setAttribute("value", arg);
	}
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/	
function Hidden(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "hidden");
	/* overriding (c) ndimangwa fadhili */
	this.setType = function(typ)	{}
}
function Text(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "text");
	this.setType = function(typ)	{}
}
function Search(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "search");
	this.setType = function(typ)	{}
}
function Tel(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "tel");
	this.setType = function(typ)	{}
}
function Url(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "url");
	this.setType = function(typ)	{}
}
function Email(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "email");
	this.setType = function(typ)	{}
}
function Password(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "password");
	this.setType = function(typ)	{}
}
function Datetime(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "datetime");
	this.setType = function(typ)	{}
}
function DateObj(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "date");
	this.setType = function(typ)	{}
}
function Month(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "month");
	this.setType = function(typ)	{}
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Week(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "week");
	this.setType = function(typ)	{}
}
function Time(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "time");
	this.setType = function(typ)	{}
}
function DatetimeLocal(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "datetime-local");
	this.setType = function(typ)	{}
}
function Range(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "range");
	this.setType = function(typ)	{}
}
function Number(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "number");
	this.setType = function(typ)	{}
}
function Color(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "color");
	this.setType = function(typ)	{}
}
function Checkbox(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "checkbox");
	this.setType = function(typ)	{}
}
function Radio(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "radio");
	this.setType = function(typ)	{}
}
function File(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "file");
	this.setType = function(typ)	{}
}
function Submit(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "submit");
	this.setType = function(typ)	{}
}
/*The following function class was created by Ndimangwa Fadhili Ngoya
(c) 2013, ndimangwa@gmail.com +255787101808
*/
function Image(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "image");
	this.setType = function(typ)	{}
}
function Reset(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "reset");
	this.setType = function(typ)	{}
}
function Button(arg)	{
	Input.apply(this, arguments);
	this.ref.setAttribute("type", "button");
	this.setType = function(typ)	{}
}
function Select(arg)	{
	this.setAutoFocus = function(bln)	{
		this.ref.autofocus = bln;
	}
	this.isAutoFocus = function()	{
		return this.ref.autofocus;
	}
	this.setDisabled = function(bln)	{
		this.ref.disabled = bln;
	}
	this.isDisabled = function()	{
		return this.ref.disabled;
	}
	this.setForm = function(frm)	{
		this.ref.setAttribute("form", frm);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setMultiple = function(mul)	{
		this.ref.multiple = mul;
	}
	this.isMultiple = function()	{
		return this.ref.multiple;
	}
	this.setName = function(iname)	{
		this.ref.setAttribute("name", iname);	
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setRequired = function(bln)	{
		this.ref.required  = bln;
	}
	this.isRequired = function()	{
		return this.ref.required;
	}
	this.setSize = function(isize)	{
		this.ref.setAttribute("size", isize);
	}	
	this.getSize = function()	{
		return this.ref.getAttribute("size");
	}
	this.getSelectedText = function()	{
		return this.ref.options[this.ref.selectedIndex].text;
	}
	this.ref = document.createElement("select");
	InnerHTML.apply(this, arguments);
}
function Datalist(arg)	{
	this.ref = document.createElement("datalist");
	InnerHTML.apply(this, arguments);
}
function OptGroup(arg)	{
	this.setDisabled = function(bln)	{
		this.ref.disabled = bln;
	}
	this.isDisabled = function()	{
		return this.ref.disabled;
	}
	this.setLabel = function(lbl)	{
		this.ref.setAttribute("label", lbl);
	}
	this.getLabel = function()	{
		return this.ref.getAttribute("label");
	}
	this.ref = document.createElement("optgroup");
	if (typeof arg === "string")	{
		this.ref.setAttribute("label", arg);
	}
	InnerHTML.apply(this, arguments);
}
function Option(arg)	{
	this.setDisabled = function(bln)	{
		this.ref.disabled = bln;
	}
	this.isDisabled = function()	{
		return this.ref.disabled;
	}
	this.setLabel = function(label)	{
		this.ref.setAttribute("label", label);
	}
	this.getLabel = function()	{
		return this.ref.getAttribute("label");
	}
	this.setSelected = function(bln)	{
		this.ref.selected = bln;
	}
	this.isSelected = function()	{
		return this.ref.selected;
	}
	this.setValue = function(ival)	{
		this.ref.setAttribute("value", ival);
	}
	this.getValue = function()	{
		return this.ref.getAttribute("value");
	}
	this.ref = document.createElement("option");
	InnerHTML.apply(this, arguments);
}
function TextArea(arg)	{
	this.setAutoComplete = function(aut)	{
		this.ref.setAttribute("autocomplete", aut);
	}
	this.getAutoComplete = function()	{
		return this.ref.getAttribute("autocomplete");
	}
	this.setAutoFocus = function(bln)	{
		this.ref.autofocus = bln;
	}
	this.isAutoFocus = function()	{
		return this.ref.autofocus;
	}	
	this.setDirName = function(_dir)	{
		this.ref.setAttribute("dirname", _dir);
	}
	this.getDirName = function()	{
		return this.ref.getAttribute("dirname");
	}
	this.setDisabled = function(bln)	{
		this.ref.disabled = bln;
	}
	this.isDisabled = function()	{
		return this.ref.disabled;
	}
	this.setInputMode = function(imode)	{
		this.ref.setAttribute("inputmode", imode);
	}
	this.getInputMode = function()	{
		return this.ref.getAttribute("inputmode");
	}
	this.setMaxLength = function(ilen)	{
		this.ref.setAttribute("maxlength", ilen);
	}
	this.getMaxLength = function()	{
		return this.ref.getAttribute("maxlength");
	}
	this.setName = function(lnx)	{
		this.ref.setAttribute("name", lnx);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.setPlaceHolder = function(plx)	{
		this.ref.setAttribute("placeholder", plx);
	}
	this.getPlaceHolder = function()	{
		return this.ref.getAttribute("placeholder");
	}
	this.setReadOnly = function(bln)	{
		this.ref.readonly = bln;
	}
	this.isReadOnly = function()	{
		return this.ref.readonly;
	}
	this.setRequired = function(bln)	{
		this.ref.required = bln;
	}
	this.isRequired = function()	{
		return this.ref.required;
	}
	this.setRows = function(irw)	{
		this.ref.setAttribute("rows", irw);
	}
	this.getRows = function()	{
		return this.ref.getAttribute("rows");
	}
	this.setWrap = function(iwr)	{
		this.ref.setAttribute("wrap", iwr);
	}
	this.getWrap = function()	{
		return this.ref.getAttribute("wrap");
	}
	this.ref = document.createElement("textarea");
	InnerHTML.apply(this, arguments);
}
function Keygen(arg)	{
	this.setAutoFocus = function(bln)	{
		this.ref.autofocus = bln;
	}
	this.isAutoFocus = function()	{
		return this.ref.autofocus;
	}
	this.setChallenge = function(chl)	{
		this.ref.setAttribute("challenge", chl);
	}	
	this.getChallenge = function()	{
		return this.ref.getAttribute("challenge");
	}
	this.setDisabled = function(bln)	{
		this.ref.disabled = bln;
	}
	this.isDisabled = function()	{
		return this.ref.disabled;
	}
	this.setForm = function(fx)	{
		this.ref.setAttribute("form", fx);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setKeyType = function(fx)	{
		this.ref.setAttribute("keytype", fx);
	}
	this.getKeyType = function()	{
		return this.ref.getAttribute("keytype");
	}
	this.setName = function(fn)	{
		this.ref.setAttribute("name", fn);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.ref = document.createElement("keygen");
	Root.apply(this, arguments);
}
function Output(arg)	{
	this.setFor = function(fr)	{
		this.ref.setAttribute("for", fr);
	}
	this.getFor = function()	{
		return this.ref.getAttribute("for");
	}
	this.setForm = function(fm)	{
		this.ref.setAttribute("form", fm);
	}
	this.getForm = function()	{
		return this.ref.getAttribute("form");
	}
	this.setName = function(fm)	{
		this.ref.setAttribute("name", fm);
	}
	this.getName = function()	{
		return this.ref.getAttribute("name");
	}
	this.ref = document.createElement("output");
	InnerHTML.apply(this, arguments);
}	
function tProgress(arg)	{
	this.setValue = function(val)	{
		this.ref.setAttribute("value", val);
	}
	this.getValue = function()	{
		return this.ref.getAttribute("value");
	}
	this.setMax = function(imax)	{
		this.ref.setAttribute("max", imax);
	}
	this.getMax = function()	{
		return this.ref.getAttribute("max");
	}
	InnerHTML.apply(this, arguments);
}
function Progress(arg)	{
	this.ref = document.createElement("progress");
	tProgress.apply(this, arguments);
}
function Meter(arg)	{
	this.setMin = function(fx)	{
		this.ref.setAttribute("min", fx);
	}
	this.getMin = function()	{
		return this.ref.getAttribute("min");
	}
	this.setLow = function(lw)	{
		this.ref.setAttribute("low", lw);
	}
	this.getLow = function()	{
		return this.ref.getAttribute("low");
	}
	this.setHigh = function(hg)	{
		this.ref.setAttribute("high", hg);
	}
	this.getHigh = function()	{
		return this.ref.getAttribute("high");
	}
	this.setOptimum = function(fx)	{
		this.ref.setAttribute("optimum", fx);
	}
	this.getOptimum = function()	{
		return this.ref.getAttribute("optimum");
	}
	this.ref = document.createElement("meter");
	tProgress.apply(this, arguments);
}
function Group(inm)	{
	this.setName = function(iname)	{
		this.name = iname;
	}
	this.getName = function()	{
		return this.name;
	}
	this.add = function(ele1)	{
		if (ele1 && this.name)	{
			ele1.setName(this.name);
		}
	}	
	if (inm)	{
		this.name = inm;
	}
}

