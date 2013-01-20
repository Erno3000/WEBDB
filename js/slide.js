window.slide = (function() {

    function init() {
        for(var i = 0; i < 5; i++) {
            var ele = document.getElementById('slide' + i);
            if(ele !== null) {
                setDisplay(ele, 'none');
            }
        }
    }

    function setDisplay(ele, value) {
        ele.style.display = value;
    }

    function toggleDisplay(id) {
        var ele = document.getElementById(id);
        if(ele !== null) {
            if(ele.style.display == 'none') {
                setDisplay(ele, 'block');
            } else {
                setDisplay(ele, 'none');
            }
        }
    }

    return {
        init: init,

        toggleDisplay: toggleDisplay

    };

})();