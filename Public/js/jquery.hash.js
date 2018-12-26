function hash($url) {
        if (window.history && window.history.pushState) {
            $(window).on('popstate', function () {
                var hashLocation = location.hash;
                var hashSplit = hashLocation.split("#!/");
                var hashName = hashSplit[1];
                if (hashName !== '') {
                    var hash = window.location.hash;
                    if (hash === '') {
                        if ($url != null) {

                            window.location.href = $url;

                        }
                    }
                }
            });
            window.history.pushState('#forward', null, '?#forward');
        }
    }
