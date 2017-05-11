(function () {
    var d = document,
        gc = 'getElementsByClassName',
        gn = 'getElementsByName';

    var btn = d[gc]('btn')[0],
        popupBox = d[gc]('popup-box-wrap')[0];

    var status = 0;

    btn.addEventListener('click', function (e) {
        popupBox.classList.remove('hide');
    });

    d.addEventListener('click', function (e) {
        e = e || event;
        var target = e.target;
        if (target.classList.contains('popup-box-close')) {
            target.parentElement.parentElement.classList.add('hide');
        }
    });

    d.addEventListener('submit', function (e) {
        e = e || event;
        e.preventDefault();

        var name = d[gn]('name')[0],
            phone = d[gn]('phone')[0],
            email = d[gn]('email')[0],
            qq = d[gn]('qq')[0];

        var validator = [
            [email, /[\w!#$%&'*+/=?^_`{|}~-]+(?:\.[\w!#$%&'*+/=?^_`{|}~-]+)*@(?:[\w](?:[\w-]*[\w])?\.)+[\w](?:[\w-]*[\w])?/, true],
            [phone, /^\d{11}$/],
            [name, /\S+/]
        ];

        for (var n = validator.length; n--;) {
            if ((!(validator[n][2] || validator[n][1].test(validator[n][0]['value'])) )
                || (validator[n][2] && validator[n][0]['value'] && !validator[n][1].test(validator[n][0]['value']))) {
                popup(validator[n][0].getAttribute('data-msg'));
                return false;
            }
        }

        ajax({
            type: 'POST',
            url: window.location.origin + '/h5/checkin/',
            handler: function (data) {
                $oData = JSON.parse(data);

                if (-$oData.status && $oData['data']['error']) {
                    popup($oData['data']['error']);
                }
                else {
                    popup('签到成功');
                    status = 1;
                }

            },
            data: {
                name: name.value,
                phone: phone.value,
                email: email.value,
                qq: qq.value,
                id: d.body.id
            }
        });

    });

    function popup(msg) {
        var place = d[gc]('alert')[0],
            content = d[gc]('warn-content')[0];

        content.children[0].innerHTML = '<span>' + msg + '</span>';

        place.classList.remove('hide');

        content.children[1].onclick = function () {
            status && (window.location.href = window.location.origin + '/h5/checkin/');
            place.classList.add('hide');
        };
    }

    function ajax(options) {
        var xhr = new XMLHttpRequest();

        if (xhr === null) {
            console.log('ajax error');
        }

        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                options.handler(this.responseText);
            }
        };

        xhr.open(options.type, options.url, true);

        xhr.setRequestHeader('Content-Type', 'application/json;charset=utf-8');
        xhr.send(JSON.stringify(options.data));
        console.log(JSON.stringify(options.data));
    }

})();