$('.fakeloader').fakeLoader().fadeIn();

//数据替换成自己需要用的数据
let data = []

//直接把表头和表内全部载入
let addTable = function (features) {
    let head = "";
    let body = "";
    let num = 0;
    let width = $("table").width() - 20;
    head += '<tr>';//编写表头
    for (let j in features[0]) {
        head += '<th><div class="st' + (++num) + '">' + j + '</div></th>';
    }
    head += '</tr>';
    for (let i = 0, len = features.length; i < len; i++) {//编写表格
        body += '<tr>';
        num = 0;
        for (let j in features[i]) {
            body += '<td><div class="st' + (++num) + '">' + features[i][j] + '</div></td>';
        }
        body += '</tr>';
    }
    $("table thead").empty().html(head);
    $("table tbody").empty().html(body);
    $(".st1").css("width", (width / 9 * 3.0) + "px");
    $(".st2").css("width", (width / 9 * 0.5) + "px");
    $(".st3").css("width", (width / 9 * 0.5) + "px");
    $(".st4").css("width", (width / 9 * 1.5) + "px");
    $(".st5").css("width", (width / 9 * 0.5) + "px");
    $(".st6").css("width", (width / 9 * 0.5) + "px");
    $(".st7").css("width", (width / 9 * 0.5) + "px");
    $(".st8").css("width", (width / 9 * 1.5) + "px");
    $(".st9").css("width", (width / 9 * 0.5) + "px");
    $('.btn-prev').on('click', function () {
        let old = $(this).attr('data-old');
        let id = $(this).attr('data-id');
        animeEdit(id, Number(old) - 1);
    });
    $('.btn-next').on('click', function () {
        let old = $(this).attr('data-old');
        let id = $(this).attr('data-id');
        animeEdit(id, Number(old) + 1);
    });
}

let animeList = function () {
    $.ajax({
        type: "GET",
        url: "https://zerorei.top/anime/notion.php?action=list",
        dataType: "jsonp",
        success: function (result) {
            if (result.code === 0) {
                addTable(result.data);
            } else {
                console.log(result);
            }
            $('.fakeloader').fadeOut();
        },
        error: function (result) {
            console.log(result);
            $('.fakeloader').fadeOut();
        }
    })
}
animeList()

let animeEdit = function (id, number) {
    $.ajax({
        type: "GET",
        url: "https://zerorei.top/anime/notion.php?action=edit&id=" + id + "&number=" + number,
        dataType: "jsonp",
        beforeSend: function () {
            $('.fakeloader').fadeIn();
        },
        complete: function () {
        },
        success: function (result) {
            if (result.code === 0) {
                animeList();
            } else {
                console.log(result);
                $('.fakeloader').fadeOut();
            }
        },
        error: function (result) {
            console.log(result);
            $('.fakeloader').fadeOut();
        }
    })
}