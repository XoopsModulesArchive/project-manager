// Adapted from: 
//      http://www.breakingpar.com/bkp/home.nsf/Doc?OpenNavigator&U=87256B14007C5C6A87256B4B0005BFA6
// Set Netscape up to run the "captureMousePosition" function whenever
// the mouse is moved. For Internet Explorer and Netscape 6, you can capture
// the movement a little easier.
if (document.layers) { // Netscape
    document.captureEvents(Event.MOUSEMOVE);
    document.onmousemove = captureMousePosition;
} else if (document.all) { // Internet Explorer
    document.onmousemove = captureMousePosition;
} else if (document.getElementById) { // Mozilla
    document.onmousemove = captureMousePosition;
}
// Global variables
xMousePos = 0; // Horizontal position of the mouse on the screen
yMousePos = 0; // Vertical position of the mouse on the screen
xMousePosMax = 0; // Width of the page
yMousePosMax = 0; // Height of the page

function show ( name ) {
    x = xMousePos;
    y = yMousePos + 20;
    //window.status = "xMousePos=" + xMousePos + ", yMousePos=" + yMousePos + ", xMousePosMax=" + xMousePosMax + ", yMousePosMax=" + yMousePosMax;
    if (document.layers) {
        document.layers[name].xpos = parseInt ( x );
        document.layers[name].left = parseInt ( x );
        document.layers[name].ypos = parseInt ( y );
        document.layers[name].top = parseInt ( y );
        document.layers[name].visibility = "show";
    } else if (document.all) {
        document.all[name].style.left = parseInt ( x );
        document.all[name].style.top = parseInt ( y );
        document.all[name].style.visibility = "visible";
    } else if (document.getElementById){
        document.getElementById(name).style.left = x + "px";
        document.getElementById(name).style.top = y + "px";
        //window.status = "x=" + x + "y=" + y + "left=" + document.getElementById(name).style.left + ", top=" + document.getElementById(name).style.top;
        document.getElementById(name).style.visibility = "visible";
    }
}

function hide ( name ) {
    if (document.layers) {
        document.layers[name].visibility = "hide";
    } else if (document.all){
        document.all[name].style.visibility = "hidden";
    } else if (document.getElementById){
        document.getElementById(name).style.visibility = "hidden";
    }
}

function captureMousePosition(e) {
    if (document.layers) {
        // When the page scrolls in Netscape, the event's mouse position
        // reflects the absolute position on the screen. innerHight/Width
        // is the position from the top/left of the screen that the user is
        // looking at. pageX/YOffset is the amount that the user has
        // scrolled into the page. So the values will be in relation to
        // each other as the total offsets into the page, no matter if
        // the user has scrolled or not.
        xMousePos = e.pageX;
        yMousePos = e.pageY;
        xMousePosMax = window.innerWidth+window.pageXOffset;
        yMousePosMax = window.innerHeight+window.pageYOffset;
    } else if (document.all) {
        // When the page scrolls in IE, the event's mouse position
        // reflects the position from the top/left of the screen the
        // user is looking at. scrollLeft/Top is the amount the user
        // has scrolled into the page. clientWidth/Height is the height/
        // width of the current page the user is looking at. So, to be
        // consistent with Netscape (above), add the scroll offsets to
        // both so we end up with an absolute value on the page, no
        // matter if the user has scrolled or not.
        xMousePos = window.event.x+document.body.scrollLeft;
        yMousePos = window.event.y+document.body.scrollTop;
        xMousePosMax = document.body.clientWidth+document.body.scrollLeft;
        yMousePosMax = document.body.clientHeight+document.body.scrollTop;
    } else if (document.getElementById) {
        // Netscape 6 behaves the same as Netscape 4 in this regard
        xMousePos = e.pageX;
        yMousePos = e.pageY;
        xMousePosMax = window.innerWidth+window.pageXOffset;
        yMousePosMax = window.innerHeight+window.pageYOffset;
//        window.status = "xMousePos=" + xMousePos + ", yMousePos=" + yMousePos + ", xMousePosMax=" + xMousePosMax + ", yMousePosMax=" + yMousePosMax;
    }
}

