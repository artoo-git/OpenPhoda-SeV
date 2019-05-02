
// target elements with the "draggable" class
interact('.draggable')
  .draggable({

    // enable inertial throwing
    inertia: false,
    // keep the element within the area of it's parent
    modifiers: [
      interact.modifiers.restrict({
        restriction: 'self'
      }),
    ],
    // enable autoScroll
    autoScroll: true,
    
    // call this function on every dragmove event
    onmove: dragMoveListener,
    // call this function on every dragend event
    onend: function (event) {
      //var textEl = event.target.querySelector('p');
      var element = document.getElementById(event.target.id);
      var pos = element.getBoundingClientRect();
      var position = (pos.left/document.getElementById('container').offsetWidth)*100;
      
      var hash = window.location.search.substr(1);
      var postdata = "item["+ event.target.id + "]=" + parseFloat(position).toFixed(0) + "&ajax=1&" + hash;

      // AJAX call
      postAjax(postdata);

      //Update table
      var cellId = "col" + event.target.id;
      var cell= document.getElementById(cellId);
      cell.innerHTML = parseFloat(position).toFixed(0);      
    }
  });

  function dragMoveListener (event) {
    var target = event.target,
        // keep the dragged position in the data-x/data-y attributes
        x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx,
        y = (parseFloat(target.getAttribute('data-y')) || 0) + event.dy;

    // translate the element
    target.style.webkitTransform =
    target.style.transform =
      'translate(' + x + 'px, ' + y + 'px)';

    // update the position attributes
    target.setAttribute('data-x', x);
    target.setAttribute('data-y', y);
  }

  function postAjax(postdata) {
    
    var xhr = new XMLHttpRequest();
    
    xhr.open('POST', 'exp.php');
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send(encodeURI(postdata));
}
  

  
