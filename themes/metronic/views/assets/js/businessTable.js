let ids = [];

$('.map-checkbox').change(e => {
    if(e.target.checked) {
        ids.push(data.find(item => item.oid === e.target.value));
    } else {
        ids = ids.filter(item => item.oid !== e.target.value);
    }
});

$('.button-map-to').click(e => {
    e.preventDefault();
    let mapTo = data.find(item => item.oid === e.currentTarget.dataset.oid);
    if(mapTo !== undefined && ids.length > 0) {
        let formData = {mapTo, ids};
        $('#map-params').val(JSON.stringify(formData));
        $('#map-form').submit();
    }
});
