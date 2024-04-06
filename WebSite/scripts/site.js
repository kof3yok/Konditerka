function windowSizeChange() {
  this.windowWidth = window.innerWidth;
  var pcoded = document.querySelector("#pcoded");
  if (windowWidth >= 768 && windowWidth <= 1024) {
    pcoded.setAttribute("pcoded-device-type", "tablet");
    pcoded.setAttribute("vertical-nav-type", "expanded");
    pcoded.setAttribute("vertical-effect", "push");
  } else if (windowWidth < 768) {
    pcoded.setAttribute("pcoded-device-type", "mobile");
    pcoded.setAttribute("vertical-nav-type", "offcanvas");
    pcoded.setAttribute("vertical-effect", "overlay");
  } else {
    pcoded.setAttribute("pcoded-device-type", "desktop");
    pcoded.setAttribute("vertical-nav-type", "expanded");
    pcoded.setAttribute("vertical-effect", "shrink");
  }
}
window.onresize = windowSizeChange;
window.onload = (event) => {
  windowSizeChange();
};
window.addEventListener("load", (event) => {
  windowSizeChange();
});

function showMenu() {
  var pcoded = document.querySelector("#pcoded");
  if (pcoded.getAttribute("vertical-nav-type") === "expanded")
    pcoded.setAttribute("vertical-nav-type", "offcanvas");
  else pcoded.setAttribute("vertical-nav-type", "expanded");
}
