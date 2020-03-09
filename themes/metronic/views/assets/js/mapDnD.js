let sticky = $('.map-to-container').offset().top;
let content = '';
let map = {};
let uid;
let value;
let draggedElement;
let name;

$('.to-drag-map').on('dragstart', e => {
    draggedElement = e.target;
    content = $(e.target).text();
    uid = e.target.dataset.uid;
    value = e.target.dataset.value;
    name = e.target.dataset.name;
});

$('.drop-map').on('dragover', e => {
    e.preventDefault();
});

$('.drop-map').on('dragenter', e => {
    e.target.style.borderColor = 'red';
});

$('.drop-map').on('dragleave', e => {
    e.target.style.borderColor = 'rgb(100, 108,154)';
});

$('.drop-map').on('drop', e => {
    if(e.target.dataset.mapped !== undefined) {
        delete map[e.target.dataset.business][e.target.dataset.uuid];
        if(map[e.target.dataset.mapped] === undefined) {
            let unusedElement = document.getElementById(e.target.dataset.mapped);
            unusedElement.style.borderColor = 'rgb(100, 108,154)';
            unusedElement.style.color = 'rgb(100, 108,154)';
        }
    } else {
        $(e.target).addClass('mapped');
    }

    $(e.target).text(content);
    e.target.style.fontWeight = 'bold';
    draggedElement.style.borderColor = 'red';
    draggedElement.style.color = 'red';
    e.target.style.borderColor = 'rgb(100, 108,154)';
    e.target.dataset.mapped = name;
    e.target.dataset.mappedUuid = uid;
    $(e.target.parentElement).addClass('allow-remove');

    if(map[name] === undefined) {
        map[name] = {[uid]: {'business': fid}};
    }

    map[name] = Object.assign(map[name], { [e.target.dataset.uuid]: { 'business': e.target.dataset.business}});
});

$('.remove-drop').click(e => {
    if(confirm(confirmTxt)) {
        let elemToChange = document.getElementById('drop-' + e.target.dataset.business + e.target.dataset.uuid);
        elemToChange.style.fontWeight = 'normal';
        $(elemToChange.parentElement).removeClass('allow-remove');
        $(elemToChange).text(dropTxt);

        delete map[elemToChange.dataset.mapped][e.target.dataset.uuid];
        if(Object.keys(map[elemToChange.dataset.mapped]).length < 2) {
            let unusedElement = document.getElementById(elemToChange.dataset.mappedUuid);
            unusedElement.style.borderColor = 'rgb(100, 108,154)';
            unusedElement.style.color = 'rgb(100, 108,154)';
            delete map[elemToChange.dataset.mapped];
        }

        delete elemToChange.dataset.mapped;
    }
});

$('#save-map-button').click(e => {
   $.post(saveURL, {map}).done(data => {
       if(data) {
           window.location.href = businessURL;
       } else {
           $('html').animate({scrollTop: 0}, 400);
           $('.alert-danger').slideDown({
               start: function () {
                   $(this).css({
                       display: "flex"
                   })
               }
           }, 100);
       }
   });
});

$(window).on('scroll', () => {
    if(window.pageYOffset > sticky ) {
        $('.map-to-container').addClass('sticky');
    } else {
        $('.map-to-container').removeClass('sticky');
    }
});

function dataUsed(uid){
    for(let business in map) {
        for(let row in map[business]) {
            if(map[business][row].uid == uid) {
                return true;
            }
        }
    }

    return false;
}