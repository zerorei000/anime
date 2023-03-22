
$('.fake-loader').fakeLoader().fadeIn();

window.onresize=function(){
    addTable(tempFeatures);
}

let tempFeatures;
let addTable = function (features) {
    tempFeatures = features;
    let head = "";
    let body = "";
    let num = 0;
    let table = $("table");
    let size = table.css('font-size');
    size = Number(size.substr(0, size.length - 2));
    head += '<tr>';//编写表头
    for (let j in features[0]) {
        if (size > 20 && num > 3) {
            continue;
        }
        head += '<th><div class="st' + (++num) + '">' + j + '</div></th>';
    }
    let width = table.width() - num * 2;
    head += '</tr>';
    for (let i = 0, len = features.length; i < len; i++) {//编写表格
        body += '<tr>';
        num = 0;
        for (let j in features[i]) {
            if (size > 20 && num > 3) {
                continue;
            }
            body += '<td><div class="st' + (++num) + '">' + features[i][j] + '</div></td>';
        }
        body += '</tr>';
    }
    $("table thead").empty().html(head);
    $("table tbody").empty().html(body);
    if (size > 20) {
        $(".st1").css("width", (width / num * 2) + "px");
        $(".st2").css("width", (width / num * 0.5) + "px");
        $(".st3").css("width", (width / num * 0.5) + "px");
        $(".st4").css("width", (width / num) + "px");
    } else {
        $(".st1").css("width", (width / num * 3.5) + "px");
        $(".st2").css("width", (width / num * 0.5) + "px");
        $(".st3").css("width", (width / num * 0.5) + "px");
        $(".st4").css("width", (width / num) + "px");
        $(".st5").css("width", (width / num * 0.5) + "px");
        $(".st6").css("width", (width / num * 0.5) + "px");
        $(".st7").css("width", (width / num * 0.5) + "px");
        $(".st8").css("width", (width / num * 1.5) + "px");
        $(".st9").css("width", (width / num * 0.5) + "px");
    }
    $('.btn-prev').off('click').on('click', function () {
        let old = $(this).attr('data-old');
        let id = $(this).attr('data-id');
        animeEdit(id, Number(old) - 1);
    });
    $('.btn-next').off('click').on('click', function () {
        let old = $(this).attr('data-old');
        let id = $(this).attr('data-id');
        animeEdit(id, Number(old) + 1);
    });
}

let animeList = function (weekday) {
    $.ajax({
        type: "GET",
        url: siteUrl + "/notion.php?action=list&weekday=" + weekday,
        dataType: "jsonp",
        beforeSend: function () {
            $('.fake-loader').fadeIn();
        },
        complete: function () {
            $('.fake-loader').fadeOut();
        },
        success: function (result) {
            if (result.code === 0) {
                $('.btn-week').removeClass('focus');
                $('.btn-week-day-' + result.data.filter.weekday).addClass('focus');
                addTable(result.data.content);
            } else {
                console.log(result);
            }
        },
        error: function (result) {
            console.log(result);
        }
    })
}
animeList(location.hash.substr(1))

let animeEdit = function (id, number) {
    $.ajax({
        type: "GET",
        url: siteUrl + "/notion.php?action=edit&id=" + id + "&number=" + number,
        dataType: "jsonp",
        beforeSend: function () {
            $('.fake-loader').fadeIn();
        },
        complete: function () {
        },
        success: function (result) {
            if (result.code === 0) {
                animeList(location.hash.substr(1));
            } else {
                console.log(result);
                $('.fake-loader').fadeOut();
            }
        },
        error: function (result) {
            console.log(result);
            $('.fake-loader').fadeOut();
        }
    })
}

$('.btn-week').on('click', function () {
    let weekday = $(this).attr('data-day');
    location.hash = weekday;
    animeList(weekday);
});